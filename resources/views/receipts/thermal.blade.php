<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt #{{ $order->order_number }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace, monospace;
            width: 76mm;
            margin: 2mm auto;
            padding: 2mm;
            color: #000;
            background: #fff;
            font-size: 11px;
            line-height: 1.25;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        .double-divider {
            border-top: 2px dashed #000;
            margin: 6px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 2px 0;
            vertical-align: top;
        }
        .items-table th {
            border-bottom: 1px dashed #000;
            font-size: 10px;
        }
        .store-title {
            font-size: 15px;
            font-weight: 900;
            margin-bottom: 2px;
        }
        .tagline {
            font-size: 9px;
            margin-bottom: 3px;
        }
        .total-row {
            font-size: 13px;
            font-weight: 900;
        }
        @media print {
            body { width: 100%; margin: 0; padding: 2mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="text-center">
        <div class="store-title">{{ \App\Models\Setting::get('stall_name', 'GHASA PRODUCTS') }}</div>
        <div class="tagline">Authentic Homemade Achar & Crunchy Pani Puri</div>
        <div>{{ \App\Models\Setting::get('account_holder', 'Ghasa Products Nepal') }}</div>
        <div>Ph: {{ \App\Models\Setting::get('contact_phone', '+977-9800000000') }}</div>
        <div>Expo Event Stall Counter</div>
    </div>

    <div class="double-divider"></div>

    <div>
        <div><strong>Receipt:</strong> #{{ $order->order_number }}</div>
        <div><strong>Date:</strong> {{ $order->created_at->format('Y-m-d h:i A') }}</div>
        <div><strong>Payment:</strong> {{ strtoupper($order->payment_method) }} ({{ ucfirst($order->status) }})</div>
    </div>

    <div class="divider"></div>

    <table class="items-table">
        <thead>
            <tr>
                <th class="text-left" style="width: 50%;">Item</th>
                <th class="text-center" style="width: 15%;">Qty</th>
                <th class="text-right" style="width: 35%;">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td class="text-left">
                        {{ $item->product_name }}
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ \App\Models\Setting::get('currency_symbol', 'रू') }} {{ number_format($item->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <table>
        <tr>
            <td class="text-left">Subtotal:</td>
            <td class="text-right">{{ \App\Models\Setting::get('currency_symbol', 'रू') }} {{ number_format($order->subtotal, 2) }}</td>
        </tr>
        @if($order->discount > 0)
            <tr>
                <td class="text-left">Discount:</td>
                <td class="text-right">-{{ \App\Models\Setting::get('currency_symbol', 'रू') }} {{ number_format($order->discount, 2) }}</td>
            </tr>
        @endif
        <tr class="total-row">
            <td class="text-left font-bold">TOTAL:</td>
            <td class="text-right font-bold">{{ \App\Models\Setting::get('currency_symbol', 'रू') }} {{ number_format($order->grand_total, 2) }}</td>
        </tr>
        @if($order->payment_method === 'cash' && $order->cash_received)
            <tr>
                <td class="text-left">Cash Tendered:</td>
                <td class="text-right">{{ \App\Models\Setting::get('currency_symbol', 'रू') }} {{ number_format($order->cash_received, 2) }}</td>
            </tr>
            <tr>
                <td class="text-left font-bold">Change Due:</td>
                <td class="text-right font-bold">{{ \App\Models\Setting::get('currency_symbol', 'रू') }} {{ number_format($order->change_returned, 2) }}</td>
            </tr>
        @endif
    </table>

    <div class="double-divider"></div>

    <div class="text-center" style="margin-top: 6px;">
        <p class="font-bold">धन्यवाद! पुन: पाल्नुहोला!</p>
        <p style="font-size: 9px; margin-top: 2px;">Thank you for visiting Ghasa Stall!</p>
        <p style="font-size: 8px; color: #555;">Live billing system powered by Ghasa POS</p>
    </div>

    <div class="no-print text-center" style="margin-top: 15px;">
        <button onclick="window.print()" style="padding: 6px 12px; background: #881337; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-family: sans-serif; font-size: 11px;">Print Thermal Receipt</button>
    </div>
</body>
</html>

