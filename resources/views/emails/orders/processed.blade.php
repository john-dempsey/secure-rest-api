<!DOCTYPE html>
<html>
<head>
    <title>Order {{ $status }}</title>
</head>
<body>
    <h1>Order {{ $status }}</h1>
    <p>Dear {{ $order->customer->name }},</p>
    <p>Thank you for your order. Your order number is <strong>{{ $order->id }}</strong>.</p>
    <p>We are pleased to inform you that your order has been {{ $status }} successfully.</p>
    <p>Order Details:</p>
    <ul>
        @foreach ($order->products as $item)
            <li>{{ $item->name }} - Quantity: {{ $item->pivot->quantity }}, price: {{ $item->pivot->price }}</li>
        @endforeach
    </ul>
    <p>Total Amount: <strong>${{ $order->totalAmount() }}</strong></p>
    <p>Thank you for shopping with us!</p>
    <p>Best regards,</p>
    <p>Secure Rest API</p>
</body>
</html>