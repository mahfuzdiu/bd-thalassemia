<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Enums\ShippingMethodEnum;
use App\Enums\OrderStatusEnum;

class OrderService
{
    /**
     * @param $shippingMethod
     * @return mixed
     */
    public function shippingCost($shippingMethod)
    {
        return match($shippingMethod){
            ShippingMethodEnum::STANDARD->value => 60,
            ShippingMethodEnum::SUNDARBAN->value => 120,
            ShippingMethodEnum::FAST_COURIER->value => 100,
        };
    }

    /**
     * @param $orderData
     */
    public function createOrder($orderData)
    {
        return Order::create([
            'user_id' => auth()->user()->id,
            'order_number' => $this->createOrderNum(),
            'total' => $this->calculateTotalPrice($orderData),
            'payment_method' => $orderData['payment']['method'],
            'shipping_method' => $orderData['shipping']['method'],
            'shipping_cost' => $this->shippingCost($orderData['shipping']['method']),
            'shipping_address' => $orderData['shipping']['address']
        ]);
    }

    /**
     * @param $order
     * @param $orderData
     */
    public function updateOrder($order, $orderData)
    {
        $order->update([
            'total' => $this->calculateTotalPrice($orderData),
            'payment_method' => $orderData['payment']['method'],
            'shipping_method' => $orderData['shipping']['method'],
            'shipping_cost' => $this->shippingCost($orderData['shipping']['method']),
            'shipping_address' => $orderData['shipping']['address']
        ]);
    }

    /**
     * @param $orderData
     * @return float|int
     */
    public function calculateTotalPrice($orderData)
    {
        $productVariants = ProductVariant::whereIn('id', array_column($orderData['items'], 'product_variant_id'))->get()->keyBy('id');
        $cost = 0;
        foreach ($orderData['items'] as $item){
            $cost = $cost + $item['quantity'] * $productVariants[$item['product_variant_id']]['price'];
        }

        return $cost;
    }

    public function createOrderNum()
    {
        return now()->format('Ymd') . strtoupper(substr(uniqid(), -6));
    }

    /**
     * @param $order
     * @param $orderData
     */
    public function upsertOrderItems($order, $orderData)
    {
        $orderItems = [];
        foreach ($orderData['items'] as $item){
            $orderItems[] = [
                'order_id' => $order->id,
                'product_variant_id' => $item['product_variant_id'],
                'variant_sku' => $item['variant_sku'],
                'quantity' => $item['quantity'],
                'updated_at' => now()->toDate(),
            ];
        }

        OrderItem::upsert($orderItems, ['order_id', 'product_variant_id'], ['quantity', 'updated_at']);
    }

    /**
     * @param $order
     * @param $validated
     */
    public function deleteItems($order, $validated)
    {
        OrderItem::where('order_id', $order->id)->whereNotIn('product_variant_id', array_column($validated['items'], 'product_variant_id'))->delete();
    }

    /**
     * @param $items
     * @param $status
     */
    public function updateStock($items, $status)
    {
        $productVariants = ProductVariant::whereIn('id', $items->keyBy('product_variant_id')->keys())->get()->keyBy('id');
        $updatedVariants = [];
        foreach ($items as $item){
            $updatedVariants[] = [
                'product_id' => $productVariants[$item['product_variant_id']]->product_id,
                'variant_sku' => $productVariants[$item['product_variant_id']]->variant_sku,
                "uuid" => $productVariants[$item['product_variant_id']]->uuid,
                "stock" => $this->stockCalculation($productVariants, $item, $status),
                "price" => $productVariants[$item['product_variant_id']]->price,
                "updated_at" => now()->toDate(),
            ];
        }

        ProductVariant::upsert($updatedVariants, ['uuid'], ['stock', 'updated_at']);
    }

    /**
     * @param $productVariants
     * @param $item
     * @param $status
     * @return mixed
     */
    private function stockCalculation($productVariants, $item, $status){
        return match($status){
            OrderStatusEnum::PROCESSING->value => $productVariants[$item->product_variant_id]['stock'] - $item['quantity'],
            OrderStatusEnum::CANCELLED->value => $productVariants[$item->product_variant_id]['stock'] + $item['quantity'],
        };
    }

    /**
     * @param $currentStatus
     * @return mixed
     */
    public function getNextStatusToUpdate($currentStatus)
    {
        return match($currentStatus){
            OrderStatusEnum::PROCESSING->value => OrderStatusEnum::SHIPPED->value,
            OrderStatusEnum::SHIPPED->value => OrderStatusEnum::DELIVERED->value
        };
    }
}
