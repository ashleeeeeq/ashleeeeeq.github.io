<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'user_id',
        'total_price',
        'payment_method',
        'delivery_method',
        'status',
        'shipping_address',
        'pickup_date',
        'pickup_time_slot',
        'seller_confirmed_at',
        'ready_at',
        'shipped_at',
        'delivered_at',
        'picked_up_at',
        'tracking_number',
        'courier'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PROCESSING = 'processing';
    const STATUS_READY_FOR_PICKUP = 'ready_for_pickup';
    const STATUS_TO_SHIP = 'to_ship';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get user orders
     */
    public function getUserOrders($userId)
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get order with items
     */
    public function getOrderWithItems($orderId)
    {
        $order = $this->find($orderId);
        if (!$order) {
            return null;
        }

        $orderItemsModel = new OrderItemsModel();
        $order['items'] = $orderItemsModel->getOrderItems($orderId);
        $order['item_count'] = count($order['items']);
        
        // Add status timeline
        $order['timeline'] = $this->getStatusTimeline($order);

        return $order;
    }

    /**
     * Update order status with timestamps
     */
    public function updateStatus($orderId, $status, $additionalData = [])
    {
        $updateData = ['status' => $status];
        
        // Add timestamp based on status
        switch ($status) {
            case self::STATUS_CONFIRMED:
                $updateData['seller_confirmed_at'] = date('Y-m-d H:i:s');
                break;
            case self::STATUS_READY_FOR_PICKUP:
                $updateData['ready_at'] = date('Y-m-d H:i:s');
                break;
            case self::STATUS_SHIPPED:
                $updateData['shipped_at'] = date('Y-m-d H:i:s');
                break;
            case self::STATUS_COMPLETED:
                $order = $this->find($orderId);
                if ($order && $order['delivery_method'] === 'delivery') {
                    $updateData['delivered_at'] = date('Y-m-d H:i:s');
                } else {
                    $updateData['picked_up_at'] = date('Y-m-d H:i:s');
                }
                break;
        }
        
        $updateData = array_merge($updateData, $additionalData);
        
        return $this->update($orderId, $updateData);
    }

    /**
     * Get status timeline for display
     */
    public function getStatusTimeline($order)
    {
        $timeline = [];
        
        if ($order['created_at']) {
            $timeline[] = [
                'status' => 'Order Placed',
                'date' => $order['created_at'],
                'icon' => 'fa-shopping-cart',
                'completed' => true
            ];
        }
        
        if ($order['seller_confirmed_at']) {
            $timeline[] = [
                'status' => 'Order Confirmed',
                'date' => $order['seller_confirmed_at'],
                'icon' => 'fa-check-circle',
                'completed' => true
            ];
        } else {
            $timeline[] = [
                'status' => 'Order Confirmed',
                'date' => null,
                'icon' => 'fa-check-circle',
                'completed' => false,
                'current' => $order['status'] === self::STATUS_PENDING
            ];
        }
        
        if ($order['delivery_method'] === 'delivery') {
            // Delivery timeline
            if ($order['shipped_at']) {
                $timeline[] = [
                    'status' => 'Shipped',
                    'date' => $order['shipped_at'],
                    'icon' => 'fa-truck',
                    'completed' => true
                ];
            } else {
                $timeline[] = [
                    'status' => 'Shipped',
                    'date' => null,
                    'icon' => 'fa-truck',
                    'completed' => false,
                    'current' => in_array($order['status'], [self::STATUS_CONFIRMED, self::STATUS_PROCESSING, self::STATUS_TO_SHIP])
                ];
            }
            
            if ($order['delivered_at']) {
                $timeline[] = [
                    'status' => 'Delivered',
                    'date' => $order['delivered_at'],
                    'icon' => 'fa-home',
                    'completed' => true
                ];
            } else {
                $timeline[] = [
                    'status' => 'Delivered',
                    'date' => null,
                    'icon' => 'fa-home',
                    'completed' => false,
                    'current' => $order['status'] === self::STATUS_SHIPPED
                ];
            }
        } else {
            // Pickup timeline
            if ($order['ready_at']) {
                $timeline[] = [
                    'status' => 'Ready for Pickup',
                    'date' => $order['ready_at'],
                    'icon' => 'fa-store',
                    'completed' => true
                ];
            } else {
                $timeline[] = [
                    'status' => 'Ready for Pickup',
                    'date' => null,
                    'icon' => 'fa-store',
                    'completed' => false,
                    'current' => in_array($order['status'], [self::STATUS_CONFIRMED, self::STATUS_PROCESSING])
                ];
            }
            
            if ($order['picked_up_at']) {
                $timeline[] = [
                    'status' => 'Picked Up',
                    'date' => $order['picked_up_at'],
                    'icon' => 'fa-check-double',
                    'completed' => true
                ];
            } else {
                $timeline[] = [
                    'status' => 'Picked Up',
                    'date' => null,
                    'icon' => 'fa-check-double',
                    'completed' => false,
                    'current' => $order['status'] === self::STATUS_READY_FOR_PICKUP
                ];
            }
        }
        
        if ($order['status'] === self::STATUS_COMPLETED) {
            $timeline[] = [
                'status' => 'Completed',
                'date' => $order['updated_at'],
                'icon' => 'fa-flag-checkered',
                'completed' => true
            ];
        }
        
        if ($order['status'] === self::STATUS_CANCELLED) {
            $timeline[] = [
                'status' => 'Cancelled',
                'date' => $order['updated_at'],
                'icon' => 'fa-times-circle',
                'completed' => true
            ];
        }
        
        return $timeline;
    }

    /**
     * Get human readable status
     */
    public function getStatusLabel($status)
    {
        $labels = [
            self::STATUS_PENDING => 'Pending Confirmation',
            self::STATUS_CONFIRMED => 'Order Confirmed',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_READY_FOR_PICKUP => 'Ready for Pickup',
            self::STATUS_TO_SHIP => 'Ready to Ship',
            self::STATUS_SHIPPED => 'Shipped',
            self::STATUS_OUT_FOR_DELIVERY => 'Out for Delivery',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled'
        ];
        
        return $labels[$status] ?? ucfirst($status);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass($status)
    {
        $classes = [
            self::STATUS_PENDING => 'warning',
            self::STATUS_CONFIRMED => 'info',
            self::STATUS_PROCESSING => 'info',
            self::STATUS_READY_FOR_PICKUP => 'success',
            self::STATUS_TO_SHIP => 'info',
            self::STATUS_SHIPPED => 'primary',
            self::STATUS_OUT_FOR_DELIVERY => 'primary',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_CANCELLED => 'danger'
        ];
        
        return $classes[$status] ?? 'secondary';
    }

    /**
     * Check if order can be cancelled by customer
     */
    public function canCancel($orderId)
    {
        $order = $this->find($orderId);
        return $order && in_array($order['status'], [self::STATUS_PENDING]);
    }

    /**
     * Check if order can be confirmed by seller
     */
    public function canConfirm($orderId)
    {
        $order = $this->find($orderId);
        return $order && $order['status'] === self::STATUS_PENDING;
    }

    /**
     * Check if customer can mark as received (delivery)
     */
    public function canReceive($orderId)
    {
        $order = $this->find($orderId);
        return $order && $order['status'] === self::STATUS_SHIPPED && $order['delivery_method'] === 'delivery';
    }

    /**
     * Check if customer can confirm pickup
     */
    public function canPickup($orderId)
    {
        $order = $this->find($orderId);
        return $order && $order['status'] === self::STATUS_READY_FOR_PICKUP && $order['delivery_method'] === 'pickup';
    }
}