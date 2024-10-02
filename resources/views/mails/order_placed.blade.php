<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .email-header h1 {
            color: #333;
        }
        .email-content {
            margin-bottom: 20px;
        }
        .email-footer {
            text-align: center;
            font-size: 12px;
            color: #888;
        }
        .order-details {
            width: 100%;
            margin-bottom: 20px;
        }
        .order-details th, .order-details td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .order-details th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>

    <div class="email-container">

        <div class="email-header">
            <h1>Order Initiated</h1>
            <p>Thank you for your order!</p>
        </div>


        <div class="email-content">
            <p>Dear Customer,</p>
            <p>Your order has been successfully placed with the following details:</p>


            <h3>Customer Information</h3>
            <p><strong>Username:</strong> {{$orderContent['user_name']}}</p>


            <h3>Payment Information</h3>
            <p><strong>Total Price:</strong> ${{$orderContent['total']}}</p>
            <p><strong>Subtotal Price:</strong> ${{$orderContent['subtotal']}}</p>
            <p><strong>Shipping Charge:</strong> ${{$orderContent['shipping_charge']}}</p>
            <p><strong>Currency:</strong> ${{$orderContent['currency']}}</p>
            <p><strong>Order No.:</strong> ${{$orderContent['order_no']}}</p>
            <p><strong>Payment Method:</strong> {{$orderContent['method_name']}}</p>

        </div>


        <div class="email-footer">
            <p>If you have any questions or concerns, please feel free to contact us.</p>
            <p>Thank you for shopping with us!</p>
            <p>&copy; {{ date('Y') }} {{ env('APP_NAME') }}. All rights reserved.</p>
        </div>
    </div>

</body>
</html>
