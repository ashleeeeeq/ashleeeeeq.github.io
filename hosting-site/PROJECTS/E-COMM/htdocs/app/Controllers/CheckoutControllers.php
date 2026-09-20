<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\OrderItemsModel;
use App\Models\CartModel;
use App\Models\ProductModel;
use App\Models\ProductVariationModel;
use App\Models\UserModel;

class CheckoutControllers extends BaseController
{
    public function index()
    {
        // Check if user is logged in
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        $cartModel = new CartModel();
        $productModel = new ProductModel();
        $variationModel = new ProductVariationModel();
        $userModel = new UserModel();

        // Check for Buy Now item first
        $buyNowItem = session()->get('buy_now_item');
        
        if ($buyNowItem) {
            // Handle Buy Now checkout (single item, not in cart)
            $product = $productModel->find($buyNowItem['product_id']);
            if ($product) {
                $cartItem = [
                    'id' => 'temp_' . $buyNowItem['product_id'],
                    'product' => $product,
                    'quantity' => $buyNowItem['quantity'],
                    'price' => $buyNowItem['price'],
                    'subtotal' => $buyNowItem['price'] * $buyNowItem['quantity'],
                    'is_buy_now' => true,
                    'variation_id' => $buyNowItem['variation_id'] ?? null,
                    'variation_value' => $buyNowItem['variation_value'] ?? null,
                    'variation_name' => $buyNowItem['variation_name'] ?? null
                ];
                $cart = [$cartItem];
                $total = $cartItem['subtotal'];
                
                // Clear buy now item after using it
                session()->remove('buy_now_item');
            } else {
                return redirect()->to('/cart')->with('error', 'Product not found');
            }
        } else {
            // Get selected cart items from session (for selected items checkout)
            $selectedItemIds = session()->get('selected_cart_items') ?? [];
            
            if (empty($selectedItemIds)) {
                // If no selected items, show all cart items (regular checkout from cart page)
                $cartItems = $cartModel
                    ->where('user_id', session()->get('id'))
                    ->findAll();
            } else {
                // Get only the selected items
                $cartItems = $cartModel
                    ->where('user_id', session()->get('id'))
                    ->whereIn('id', $selectedItemIds)
                    ->findAll();
            }

            if (empty($cartItems)) {
                session()->remove('selected_cart_items');
                return redirect()->to('/cart')->with('error', 'Your cart is empty');
            }

            // Build cart array with variation support
            $cart = [];
            $total = 0;

            foreach ($cartItems as $item) {
                $product = $productModel->find($item['product_id']);
                if ($product) {
                    // Get price based on variation
                    $price = $product['price'];
                    $variationValue = null;
                    $variationName = null;
                    
                    if (!empty($item['variation_id'])) {
                        $variation = $variationModel->find($item['variation_id']);
                        if ($variation) {
                            $price = $variation['price'];
                            $variationValue = $variation['variation_value'];
                            $variationName = $variation['variation_name'];
                            $stock = $variation['stock'];
                        } else {
                            $stock = $product['stock'];
                        }
                    } else {
                        $stock = $product['stock'];
                    }
                    
                    if ($stock < $item['quantity']) {
                        return redirect()->to('/cart')->with('error', "Insufficient stock for {$product['product_name']}" . ($variationValue ? " ({$variationValue})" : ""));
                    }
                    
                    $item['product'] = $product;
                    $item['price'] = $price;
                    $item['variation_value'] = $variationValue;
                    $item['variation_name'] = $variationName;
                    $item['subtotal'] = $price * $item['quantity'];
                    $total += $item['subtotal'];
                    $cart[] = $item;
                }
            }
        }

        // Get user information
        $user = $userModel->find(session()->get('id'));

        $data['cart'] = $cart;
        $data['total'] = $total;
        $data['user'] = $user;
        $data['is_buy_now'] = isset($buyNowItem);

        return view('checkout', $data);
    }

    public function process()
    {
        // Check if user is logged in
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        $orderModel = new OrderModel();
        $orderItemModel = new OrderItemsModel();
        $cartModel = new CartModel();
        $productModel = new ProductModel();
        $variationModel = new ProductVariationModel();
        $userModel = new UserModel();

        // Validation
        $validation = \Config\Services::validation();
        $validation->setRules([
            'payment_method' => 'required|in_list[cash,gcash,card]',
            'delivery_method' => 'required|in_list[pickup,delivery]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', 'Validation failed: ' . implode(', ', $validation->getErrors()));
        }

        // Get delivery method
        $deliveryMethod = $this->request->getPost('delivery_method');
        
        // Validate pickup schedule if pickup is selected
        $additionalData = [];
        if ($deliveryMethod === 'pickup') {
            $pickupDate = $this->request->getPost('pickup_date');
            $pickupTimeSlot = $this->request->getPost('pickup_time_slot');
            
            if (!$pickupDate || !$pickupTimeSlot) {
                return redirect()->back()->with('error', 'Please select pickup date and time');
            }
            
            $additionalData['pickup_date'] = $pickupDate;
            $additionalData['pickup_time_slot'] = $pickupTimeSlot;
        }

        // Check if this is a Buy Now checkout
        $buyNowItem = session()->get('buy_now_item');
        
        if ($buyNowItem) {
            // Process Buy Now (single item, not in cart)
            $product = $productModel->find($buyNowItem['product_id']);
            if (!$product) {
                return redirect()->to('/cart')->with('error', 'Product not found');
            }

            // Check variation stock if applicable
            $price = $buyNowItem['price'];
            $stock = $buyNowItem['stock'];
            
            if ($stock < $buyNowItem['quantity']) {
                return redirect()->to('/cart')->with('error', "Insufficient stock for {$product['product_name']}" . ($buyNowItem['variation_value'] ? " ({$buyNowItem['variation_value']})" : ""));
            }

            $total = $buyNowItem['price'] * $buyNowItem['quantity'];
            
            $orderItems = [[
                'product_id' => $buyNowItem['product_id'],
                'variation_id' => $buyNowItem['variation_id'] ?? null,
                'quantity' => $buyNowItem['quantity'],
                'price' => $buyNowItem['price'],
                'name' => $buyNowItem['name']
            ]];
            
            // Update variation stock if applicable
            if ($buyNowItem['variation_id']) {
                $variation = $variationModel->find($buyNowItem['variation_id']);
                if ($variation) {
                    $newStock = $variation['stock'] - $buyNowItem['quantity'];
                    $variationModel->update($buyNowItem['variation_id'], ['stock' => $newStock]);
                }
            } else {
                // Update product stock
                $newStock = $product['stock'] - $buyNowItem['quantity'];
                $productModel->update($buyNowItem['product_id'], ['stock' => $newStock]);
            }
            
            // Clear buy now item from session
            session()->remove('buy_now_item');
            
        } else {
            // Process cart items (regular checkout)
            // Get selected cart items from session
            $selectedItemIds = session()->get('selected_cart_items') ?? [];
            
            if (empty($selectedItemIds)) {
                // If no selected items, use all cart items
                $cartItems = $cartModel
                    ->where('user_id', session()->get('id'))
                    ->findAll();
            } else {
                // Use only selected items
                $cartItems = $cartModel
                    ->where('user_id', session()->get('id'))
                    ->whereIn('id', $selectedItemIds)
                    ->findAll();
            }

            if (empty($cartItems)) {
                session()->remove('selected_cart_items');
                return redirect()->to('/cart')->with('error', 'No items to checkout');
            }

            // Calculate total and verify stock
            $total = 0;
            $orderItems = [];

            foreach ($cartItems as $item) {
                $product = $productModel->find($item['product_id']);
                if (!$product) {
                    return redirect()->to('/cart')->with('error', 'Product not found for ID: ' . $item['product_id']);
                }

                // Get price and stock based on variation
                $price = $product['price'];
                $variationId = null;
                $variationValue = null;
                
                if (!empty($item['variation_id'])) {
                    $variation = $variationModel->find($item['variation_id']);
                    if ($variation) {
                        $price = $variation['price'];
                        $stock = $variation['stock'];
                        $variationId = $variation['id'];
                        $variationValue = $variation['variation_value'];
                    } else {
                        $stock = $product['stock'];
                    }
                } else {
                    $stock = $product['stock'];
                }

                if ($stock < $item['quantity']) {
                    return redirect()->to('/cart')->with('error', "Insufficient stock for {$product['product_name']}" . ($variationValue ? " ({$variationValue})" : ""));
                }

                $subtotal = $price * $item['quantity'];
                $total += $subtotal;

                $orderItems[] = [
                    'product_id' => $item['product_id'],
                    'variation_id' => $variationId,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'name' => $product['product_name'] . ($variationValue ? " ({$variationValue})" : "")
                ];
                
                // Update stock based on variation or product
                if ($variationId) {
                    $newStock = $stock - $item['quantity'];
                    $variationModel->update($variationId, ['stock' => $newStock]);
                } else {
                    $newStock = $product['stock'] - $item['quantity'];
                    $productModel->update($item['product_id'], ['stock' => $newStock]);
                }
            }
        }

        // Get user for shipping address
        $user = $userModel->find(session()->get('id'));
        
        if (!$user) {
            return redirect()->back()->with('error', 'User not found');
        }

        // Start transaction
        $db = db_connect();
        $db->transBegin();

        try {
            // Prepare order data
            $orderData = [
                'user_id' => session()->get('id'),
                'total_price' => $total,
                'payment_method' => $this->request->getPost('payment_method'),
                'delivery_method' => $deliveryMethod,
                'status' => 'pending'
            ];
            
            // Merge additional data (pickup schedule)
            $orderData = array_merge($orderData, $additionalData);
            
            // Add shipping_address if the column exists
            $fields = $db->getFieldNames('orders');
            if (in_array('shipping_address', $fields)) {
                $orderData['shipping_address'] = $user['address'];
            }

            // Create order
            $orderId = $orderModel->insert($orderData);
            
            if (!$orderId) {
                throw new \Exception('Failed to create order: ' . json_encode($orderModel->errors()));
            }

            // Add order items
            foreach ($orderItems as $item) {
                $orderItemData = [
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'variation_id' => $item['variation_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ];
                
                $inserted = $orderItemModel->insert($orderItemData);
                
                if (!$inserted) {
                    throw new \Exception('Failed to insert order item: ' . json_encode($orderItemModel->errors()));
                }
            }

            // Remove purchased items from cart (only for cart items, not buy now)
            if (!isset($buyNowItem) && !empty($cartItems)) {
                if (!empty($selectedItemIds)) {
                    // Remove only selected items
                    $cartModel->where('user_id', session()->get('id'))
                        ->whereIn('id', $selectedItemIds)
                        ->delete();
                } else {
                    // Remove all items from cart (if checking out everything)
                    $cartModel->where('user_id', session()->get('id'))->delete();
                }
            }
            
            // Clear selected items session
            session()->remove('selected_cart_items');

            // Commit transaction
            $db->transCommit();

            return redirect()->to('/transactions')->with('success', 'Order placed successfully! Order ID: #' . str_pad($orderId, 6, '0', STR_PAD_LEFT));

        } catch (\Exception $e) {
            // Rollback transaction
            $db->transRollback();
            
            // Log the error
            log_message('error', 'Checkout error: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Failed to process order: ' . $e->getMessage());
        }
    }
}