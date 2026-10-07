<?php

namespace App\Controllers;

use App\Models\CartModel;
use App\Models\ProductModel;
use App\Models\ProductVariationModel;

class CartControllers extends BaseController
{
    /**
     * Display cart page
     */
    public function index()
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Please login to view your cart');
        }

        $cartModel = new CartModel();
        $productModel = new ProductModel();
        $variationModel = new ProductVariationModel();

        $cartItems = $cartModel
            ->where('user_id', $this->getCurrentUserId())
            ->findAll();

        $cart = [];
        $total = 0;

        foreach ($cartItems as $item) {
            $product = $productModel->find($item['product_id']);

            if ($product) {
                // Build the cart item with proper structure
                $cartItem = [
                    'id' => $item['id'],
                    'product_id' => $item['product_id'],
                    'variation_id' => $item['variation_id'],
                    'quantity' => $item['quantity'],
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'image' => $item['image'] ?? $product['image'],
                    'stock' => null,
                    'variation_value' => null
                ];

                // If there's a variation, get variation details
                if (!empty($item['variation_id'])) {
                    $variation = $variationModel->find($item['variation_id']);
                    if ($variation) {
                        $cartItem['price'] = $variation['price'];
                        $cartItem['stock'] = $variation['stock'];
                        $cartItem['variation_value'] = $variation['variation_value'];
                        $cartItem['name'] = $product['product_name'];
                    }
                } else {
                    // No variation - use product stock
                    $cartItem['stock'] = $product['stock'];
                    $cartItem['name'] = $product['product_name'];
                }

                $cartItem['subtotal'] = $cartItem['price'] * $cartItem['quantity'];
                $total += $cartItem['subtotal'];
                $cart[] = $cartItem;
            }
        }

        $data['cart'] = $cart;
        $data['total'] = $total;

        return view('cart', $data);
    }

    /**
     * Helper for AJAX responses (ADD THIS METHOD)
     */
    private function ajaxResponse($success, $message, $data = [])
    {
        $response = ['success' => $success, 'message' => $message];
        return $this->response->setJSON(array_merge($response, $data));
    }

    // Helper for AJAX errors
    private function ajaxError($message)
    {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => $message]);
        }
        return redirect()->back()->with('error', $message);
    }

    /**
     * Add item to cart
     */
    public function add($product_id = null)
    {
        if (!$this->isLoggedIn()) {
            return $this->ajaxResponse(false, 'Please login to add items to cart');
        }

        if (!$product_id) {
            return $this->ajaxResponse(false, 'Product not found');
        }

        $cartModel = new CartModel();
        $productModel = new ProductModel();
        $variationModel = new ProductVariationModel();

        $quantity = (int) $this->request->getPost('quantity') ?: 1;
        $variationId = $this->request->getPost('variation_id');

        $product = $productModel->find($product_id);
        if (!$product) {
            return $this->ajaxResponse(false, 'Product not found');
        }

        // Set default values
        $price = $product['price'];
        $stock = $product['stock'];
        $itemName = $product['product_name'];
        $image = $product['image'];

        // Check if variation exists
        if ($variationId) {
            $variation = $variationModel->find($variationId);
            if (!$variation || $variation['product_id'] != $product_id) {
                return $this->ajaxResponse(false, 'Invalid variation');
            }
            $price = $variation['price'];
            $stock = $variation['stock'];
            $itemName = $product['product_name'] . ' (' . $variation['variation_value'] . ')';
            $image = $product['image'];
        }

        // Check stock
        if ($stock < $quantity) {
            return $this->ajaxResponse(false, 'Insufficient stock. Only ' . $stock . ' available.');
        }

        // Check existing cart item
        $existing = $cartModel->where('user_id', $this->getCurrentUserId())
            ->where('product_id', $product_id)
            ->where('variation_id', $variationId)
            ->first();

        if ($existing) {
            $newQuantity = $existing['quantity'] + $quantity;
            if ($stock >= $newQuantity) {
                $cartModel->update($existing['id'], ['quantity' => $newQuantity]);
            } else {
                return $this->ajaxResponse(false, 'Cannot add more than available stock. Only ' . $stock . ' left.');
            }
        } else {
            $cartModel->insert([
                'user_id'      => $this->getCurrentUserId(),
                'product_id'   => $product_id,
                'variation_id' => $variationId,
                'quantity'     => $quantity,
                'price'        => $price,
                'name'         => $itemName,
                'image'        => $image
            ]);
        }

        // Get updated cart count
        $cartCount = $cartModel->where('user_id', $this->getCurrentUserId())
            ->selectSum('quantity')
            ->first()['quantity'] ?? 0;

        return $this->ajaxResponse(true, 'Added to cart', ['cartCount' => $cartCount]);
    }

    /**
     * Update cart quantity (AJAX)
     */
    public function update($id)
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login first'
            ]);
        }

        $quantity = (int)$this->request->getPost('quantity');
        
        if ($quantity < 1) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid quantity'
            ]);
        }
        
        $cartModel = new CartModel();
        $productModel = new ProductModel();

        $cartItem = $cartModel->find($id);
        if (!$cartItem || $cartItem['user_id'] != $this->getCurrentUserId()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cart item not found'
            ]);
        }

        $product = $productModel->find($cartItem['product_id']);
        if ($product['stock'] < $quantity) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Insufficient stock. Available: ' . $product['stock']
            ]);
        }

        $cartModel->update($id, ['quantity' => $quantity]);

        $cartItems = $cartModel
            ->where('user_id', $this->getCurrentUserId())
            ->findAll();
        
        $totalItems = 0;
        $cartTotal = 0;
        
        foreach ($cartItems as $item) {
            $product = $productModel->find($item['product_id']);
            if ($product) {
                $totalItems += $item['quantity'];
                $cartTotal += $product['price'] * $item['quantity'];
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Cart updated',
            'cartCount' => $totalItems,
            'cartTotal' => number_format($cartTotal, 2)
        ]);
    }

    /**
     * Remove item from cart (AJAX)
     */
    public function remove($id)
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login first'
            ]);
        }

        $cartModel = new CartModel();

        $cartItem = $cartModel->find($id);
        if (!$cartItem || $cartItem['user_id'] != $this->getCurrentUserId()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Item not found'
            ]);
        }

        $cartModel->delete($id);

        $cartItems = $cartModel
            ->where('user_id', $this->getCurrentUserId())
            ->findAll();
        
        $totalItems = array_sum(array_column($cartItems, 'quantity'));
        $cartTotal = 0;
        
        $productModel = new ProductModel();
        foreach ($cartItems as $item) {
            $product = $productModel->find($item['product_id']);
            if ($product) {
                $cartTotal += $product['price'] * $item['quantity'];
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Item removed from cart',
            'cartCount' => $totalItems,
            'cartTotal' => number_format($cartTotal, 2)
        ]);
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Please login first');
        }

        $cartModel = new CartModel();
        $cartModel->where('user_id', $this->getCurrentUserId())->delete();

        return redirect()->to('/cart')->with('success', 'Cart cleared successfully');
    }

    /**
     * Get cart count (AJAX)
     */
    public function getCartCount()
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON([
                'success' => true,
                'count' => 0
            ]);
        }

        $cartModel = new CartModel();
        $cartItems = $cartModel
            ->where('user_id', $this->getCurrentUserId())
            ->findAll();
        
        $totalItems = array_sum(array_column($cartItems, 'quantity'));

        return $this->response->setJSON([
            'success' => true,
            'count' => $totalItems
        ]);
    }

    /**
     * Save selected items for checkout
     */
    public function saveSelected()
    {
        if (!$this->isLoggedIn()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login first'
            ]);
        }

        $selected = $this->request->getJSON(true)['selected'] ?? [];
        
        if (empty($selected)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No items selected'
            ]);
        }
        
        // Save selected items in session for checkout
        session()->set('selected_cart_items', $selected);
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Selected items saved'
        ]);
    }

    /**
     * Buy Now - Direct checkout without adding to cart
     */
    public function buyNow($product_id = null)
    {
        if (!$this->isLoggedIn()) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Please login first'
                ]);
            }
            return redirect()->to('/login')->with('error', 'Please login first');
        }

        $productModel = new ProductModel();
        $variationModel = new ProductVariationModel();

        $quantity = (int) $this->request->getPost('quantity') ?: 1;
        $variationId = $this->request->getPost('variation_id');

        $product = $productModel->find($product_id);
        if (!$product) {
            return $this->ajaxError('Product not found');
        }

        $price = $product['price'];
        $stock = $product['stock'];
        $itemName = $product['product_name'];

        if ($variationId) {
            $variation = $variationModel->find($variationId);
            if (!$variation || $variation['product_id'] != $product_id) {
                return $this->ajaxError('Invalid variation');
            }
            $price = $variation['price'];
            $stock = $variation['stock'];
            $itemName = $product['product_name'] . ' (' . $variation['variation_value'] . ')';
        }

        if ($stock < $quantity) {
            return $this->ajaxError('Insufficient stock. Only ' . $stock . ' available.');
        }

        // Create a temporary checkout item without saving to cart
        $checkoutItem = [
            'product_id' => $product_id,
            'variation_id' => $variationId,
            'quantity' => $quantity,
            'price' => $price,
            'name' => $itemName,
            'stock' => $stock
        ];

        // Save the buy now item to session for checkout
        session()->set('buy_now_item', $checkoutItem);
        
        // Also clear any selected cart items
        session()->remove('selected_cart_items');

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success'   => true,
                'message'   => 'Redirecting to checkout',
                'cartCount' => 0
            ]);
        }

        return redirect()->to('/checkout');
    }
}