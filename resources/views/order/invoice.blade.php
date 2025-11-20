<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; }
    </style>
</head>
<body>
<h2>Invoice</h2>
<p><strong>Order Number:</strong> {{ $order->order_number }}</p>
<p><strong>Customer ID:</strong> {{ $order->user_id }}</p>
<p><strong>Status:</strong> {{ $order->status }}</p>
<p><strong>Payment Method:</strong> {{ $order->payment_method }}</p>
<p><strong>Shipping Method:</strong> {{ $order->shipping_method }}</p>
<p><strong>Shipping Address:</strong> {{ $order->shipping_address }}</p>

<table>
    <thead>
    <tr>
        <th>SKU</th>
        <th>Quantity</th>
    </tr>
    </thead>
    <tbody>
    @foreach($order->items as $item)
        <tr>
            <td>{{ $item->variant_sku }}</td>
            <td>{{ $item->quantity }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<p><strong>Shipping Cost:</strong> {{ $order->shipping_cost }}</p>
<p><strong>Total:</strong> {{ $order->total }}</p>
</body>
</html>
