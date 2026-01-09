<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt {{ $receipt->receipt_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 30px;
            background: #fff;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4a90e2;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #4a90e2;
            margin: 0;
            font-size: 28px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .info-box {
            flex: 1;
            padding: 15px;
            border: 1px solid #eee;
            margin: 0 10px;
            background: #f9f9f9;
        }
        .info-box h3 {
            margin: 0 0 10px 0;
            color: #4a90e2;
            font-size: 16px;
        }
        .info-box p {
            margin: 5px 0;
        }
        .details {
            margin-bottom: 30px;
        }
        .details h2 {
            color: #4a90e2;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 10px;
            background: #f9f9f9;
        }
        .detail-label {
            font-weight: bold;
            color: #666;
        }
        .detail-value {
            text-align: right;
        }
        .amount {
            font-size: 18px;
            font-weight: bold;
            color: #2c5aa0;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #4a90e2;
            color: #666;
        }
        .thank-you {
            font-size: 16px;
            font-weight: bold;
            color: #4a90e2;
            margin-bottom: 10px;
        }
        @media print {
            body {
                padding: 0;
            }
            .container {
                border: none;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>PAYMENT RECEIPT</h1>
            <p>Official Payment Confirmation</p>
        </div>

        <div class="receipt-info">
            <div class="info-box">
                <h3>Receipt Details</h3>
                <p><strong>Receipt Number:</strong> {{ $receipt->receipt_number }}</p>
                <p><strong>Date:</strong> {{ $receipt->receipt_date->format('M d, Y') }}</p>
                <p><strong>Status:</strong> <span style="color: #28a745;">{{ ucfirst($receipt->status) }}</span></p>
            </div>
            
            <div class="info-box">
                <h3>Payment Information</h3>
                <p><strong>Method:</strong> {{ ucfirst($receipt->payment_method) }}</p>
                <p><strong>Reference:</strong> {{ $receipt->reference ?? 'N/A' }}</p>
                <p><strong>Processed:</strong> {{ $receipt->created_at->format('M d, Y H:i') }}</p>
            </div>
        </div>

        <div class="details">
            <h2>Payment Details</h2>
            
            <div class="detail-row">
                <span class="detail-label">Customer Name:</span>
                <span class="detail-value">{{ $receipt->customer_name }}</span>
            </div>
            
            @if($receipt->customer_email)
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span class="detail-value">{{ $receipt->customer_email }}</span>
            </div>
            @endif
            
            @if($receipt->customer_phone)
            <div class="detail-row">
                <span class="detail-label">Phone:</span>
                <span class="detail-value">{{ $receipt->customer_phone }}</span>
            </div>
            @endif
            
            <div class="detail-row">
                <span class="detail-label">Amount Paid:</span>
                <span class="detail-value amount">GHS {{ number_format($receipt->amount, 2) }}</span>
            </div>
            
            @if($receipt->invoice && $receipt->invoice->invoice_number)
            <div class="detail-row">
                <span class="detail-label">Invoice Number:</span>
                <span class="detail-value">{{ $receipt->invoice->invoice_number }}</span>
            </div>
            @endif
            
            @if($receipt->notes)
            <div class="detail-row">
                <span class="detail-label">Notes:</span>
                <span class="detail-value">{{ $receipt->notes }}</span>
            </div>
            @endif
        </div>

        <div class="footer">
            <div class="thank-you">Thank you for your payment!</div>
            <p>This receipt serves as confirmation of your payment. Please keep it for your records.</p>
            <p>If you have any questions, please contact our support team.</p>
            
            <div class="no-print" style="margin-top: 20px;">
                <button onclick="window.print()" style="background: #4a90e2; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
                    Print Receipt
                </button>
            </div>
        </div>
    </div>
</body>
</html>