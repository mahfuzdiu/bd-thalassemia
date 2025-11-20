<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?php echo e($order->order_number); ?></title>
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
<p><strong>Order Number:</strong> <?php echo e($order->order_number); ?></p>
<p><strong>Customer ID:</strong> <?php echo e($order->user_id); ?></p>
<p><strong>Status:</strong> <?php echo e($order->status); ?></p>
<p><strong>Payment Method:</strong> <?php echo e($order->payment_method); ?></p>
<p><strong>Shipping Method:</strong> <?php echo e($order->shipping_method); ?></p>
<p><strong>Shipping Address:</strong> <?php echo e($order->shipping_address); ?></p>

<table>
    <thead>
    <tr>
        <th>SKU</th>
        <th>Quantity</th>
    </tr>
    </thead>
    <tbody>
    <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($item->variant_sku); ?></td>
            <td><?php echo e($item->quantity); ?></td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>

<p><strong>Shipping Cost:</strong> <?php echo e($order->shipping_cost); ?></p>
<p><strong>Total:</strong> <?php echo e($order->total); ?></p>
</body>
</html>
<?php /**PATH C:\wamp64\www\thalassemia\resources\views\order\invoice.blade.php ENDPATH**/ ?>