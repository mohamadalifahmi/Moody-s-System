<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>فاتورة #{{ $order->order_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Cairo', sans-serif;
            background: #fff;
            color: #1e293b;
            width: 80mm;
            margin: 0 auto;
            padding: 10px 0;
            font-size: 12px;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            padding-bottom: 10px;
            border-bottom: 2px dashed #1e293b;
            margin-bottom: 10px;
        }
        .header h2 {
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 4px;
        }
        .header p {
            font-size: 11px;
            color: #64748b;
            margin: 2px 0;
        }
        .info {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #cbd5e1;
        }
        .info div {
            display: flex;
            flex-direction: column;
        }
        .info .label {
            color: #94a3b8;
            font-size: 10px;
        }
        .info .value {
            font-weight: 600;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 11px;
        }
        table.items th {
            text-align: right;
            font-size: 10px;
            color: #94a3b8;
            font-weight: 700;
            padding: 6px 4px;
            border-bottom: 1px solid #e2e8f0;
        }
        table.items th:last-child { text-align: left; }
        table.items th:nth-child(2) { text-align: center; }
        table.items td {
            padding: 6px 4px;
            border-bottom: 1px dashed #f1f5f9;
        }
        table.items td:last-child {
            text-align: left;
            font-weight: 600;
            direction: ltr;
        }
        table.items td:nth-child(2) { text-align: center; }
        .totals {
            border-top: 2px dashed #1e293b;
            padding-top: 6px;
            margin-bottom: 8px;
        }
        .totals .row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
            font-size: 11px;
        }
        .totals .row.total {
            font-size: 16px;
            font-weight: 800;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
            margin-top: 4px;
        }
        .totals .row .ltr { direction: ltr; }
        .payment-info {
            text-align: center;
            font-size: 11px;
            padding: 8px 0;
            border-top: 1px dashed #cbd5e1;
            border-bottom: 1px dashed #cbd5e1;
            margin-bottom: 8px;
        }
        .payment-info span { font-weight: 600; }
        .footer {
            text-align: center;
            padding-top: 8px;
            font-size: 14px;
            font-weight: 700;
            color: var(--gold-dark, #b8943e);
        }
        .divider-dash {
            border: none;
            border-top: 1px dashed #cbd5e1;
            margin: 6px 0;
        }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    @php use App\Domains\Core\Helpers\CurrencyHelper; @endphp
    <div class="header">
        <h2>{{ config('app.name', 'Moody\'s') }}</h2>
        <p><i class="fas fa-map-marker-alt"></i> {{ $restaurant_address ?? 'العنوان' }}</p>
        <p><i class="fas fa-phone-alt"></i> {{ $restaurant_phone ?? 'الهاتف' }}</p>
    </div>

    <div class="info">
        <div>
            <span class="label">رقم الفاتورة</span>
            <span class="value">#{{ $order->order_number }}</span>
        </div>
        <div style="text-align:left;">
            <span class="label">التاريخ</span>
            <span class="value" style="direction:ltr;text-align:left;">{{ $order->created_at->format('Y-m-d H:i') }}</span>
        </div>
    </div>

    <div class="info">
        <div>
            <span class="label">أمين الصندوق</span>
            <span class="value">{{ $order->user->name ?? '-' }}</span>
        </div>
        <div style="text-align:left;">
            <span class="label">نوع الطلب</span>
            <span class="value">{{ \App\Domains\Core\Helpers\CurrencyHelper::orderTypeLabel($order->order_type) }}</span>
        </div>
    </div>

    @if($order->customer_name)
    <div class="info">
        <div>
            <span class="label">العميل</span>
            <span class="value">{{ $order->customer_name }}</span>
        </div>
    </div>
    @endif

    <hr class="divider-dash">

    <table class="items">
        <thead>
            <tr>
                <th>المنتج</th>
                <th>الكمية</th>
                <th>المجموع</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? $item->product_name }}</td>
                    <td>{{ $item->quantity }} × {{ CurrencyHelper::formatDual($item->unit_price, $exchangeRate) }}</td>
                    <td>{{ CurrencyHelper::formatDual($item->quantity * $item->unit_price, $exchangeRate) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="row">
            <span>المجموع الفرعي</span>
            <span class="ltr">{{ CurrencyHelper::formatDual($order->subtotal, $exchangeRate) }}</span>
        </div>
        @if($order->discount > 0)
        <div class="row">
            <span>الخصم</span>
            <span class="ltr">-{{ CurrencyHelper::formatDual($order->discount, $exchangeRate) }}</span>
        </div>
        @endif
        <div class="row">
            <span>الضريبة</span>
            <span class="ltr">{{ CurrencyHelper::formatDual($order->tax, $exchangeRate) }}</span>
        </div>
        <div class="row total">
            <span>الإجمالي</span>
            <span class="ltr">{{ CurrencyHelper::formatDual($order->total, $exchangeRate) }}</span>
        </div>
    </div>

    <div class="payment-info">
        <span>طريقة الدفع: </span>
        @if($order->payment_method == 'cash')نقداً
        @elseif($order->payment_method == 'card')بطاقة
        @elseأخرى
        @endif
        @if($order->amount_received)
            &nbsp;|&nbsp; <span>المدفوع: </span>{{ CurrencyHelper::formatDual($order->amount_received, $exchangeRate) }}
            &nbsp;|&nbsp; <span>الباقي: </span>{{ CurrencyHelper::formatDual($order->amount_received - $order->total, $exchangeRate) }}
        @endif
    </div>

    <div class="footer">
        شكراً لزيارتكم
    </div>
</body>
</html>
<script>
    window.onload = function() { window.print(); };
</script>
