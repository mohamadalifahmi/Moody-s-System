<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>فاتورة #{{ $invoice->invoice_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @page { margin: 15mm; }
        body { font-family: 'Cairo', sans-serif; background: #fff; color: #1e293b; font-size: 13px; line-height: 1.6; }
        .invoice-header { text-align: center; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 2px solid #c9a961; }
        .invoice-header .brand-icon { width: 64px; height: 64px; background: linear-gradient(135deg, #c9a961, #b8943e); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 28px; color: #fff; margin: 0 auto 10px; box-shadow: 0 4px 12px rgba(201,169,97,0.4); }
        .invoice-header h2 { font-size: 20px; font-weight: 800; margin: 0; }
        .invoice-header p { margin: 2px 0; color: #64748b; font-size: 12px; }
        .invoice-meta { display: flex; justify-content: space-between; margin-bottom: 20px; padding: 12px 16px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; }
        .invoice-meta .meta-item { font-size: 12px; }
        .invoice-meta .meta-item strong { display: block; font-size: 14px; color: #1e293b; }
        .invoice-meta .meta-item small { color: #94a3b8; }
        .customer-info { margin-bottom: 20px; padding: 12px 16px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; }
        .customer-info h6 { font-size: 12px; color: #94a3b8; margin-bottom: 4px; }
        .customer-info p { margin: 0; font-weight: 600; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table thead th { background: #1a1a2e; color: #e6f1ff; padding: 10px 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: center; }
        .items-table thead th:first-child { border-radius: 0 8px 8px 0; }
        .items-table thead th:last-child { border-radius: 8px 0 0 8px; }
        .items-table tbody td { padding: 10px 12px; border-bottom: 1px solid #e2e8f0; text-align: center; font-size: 13px; }
        .items-table tbody tr:nth-child(even) { background: #f8fafc; }
        .items-table tbody tr:last-child td { border-bottom: none; }
        .amount-cell { direction: ltr; text-align: right; font-weight: 600; font-family: 'Courier New', monospace; }
        .totals { width: 340px; margin-right: auto; padding: 16px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; }
        .totals .total-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px; }
        .totals .total-row.final { border-top: 2px solid #c9a961; margin-top: 6px; padding-top: 10px; font-size: 16px; font-weight: 800; color: #b8943e; }
        .invoice-footer { text-align: center; margin-top: 30px; padding-top: 16px; border-top: 1px solid #e2e8f0; font-size: 11px; color: #94a3b8; }
        .status-badge { display: inline-block; padding: 4px 14px; border-radius: 50px; font-size: 12px; font-weight: 600; }
        .status-paid { background: #d1fae5; color: #059669; }
        .status-issued { background: #dbeafe; color: #2563eb; }
        .status-draft { background: #fef3c7; color: #d97706; }
        .status-cancelled { background: #fee2e2; color: #dc2626; }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
        .no-print { text-align: center; margin-bottom: 20px; }
        .btn-print { background: #c9a961; color: #fff; border: none; padding: 10px 28px; border-radius: 8px; font-weight: 600; cursor: pointer; font-family: 'Cairo', sans-serif; }
        .btn-print:hover { background: #b8943e; }
    </style>
</head>
<body>
    @php use App\Domains\Core\Helpers\CurrencyHelper; @endphp
    <div class="no-print">
        <button class="btn-print" onclick="window.print()"><i class="fas fa-print"></i> طباعة الفاتورة</button>
    </div>

    <div class="invoice-header">
        <div class="brand-icon"><svg viewBox="0 0 40 40" width="64" height="64" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="sg3" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#d4a853"/><stop offset="100%" stop-color="#a07820"/></linearGradient></defs><path d="M20 3L35 10v13c0 8-15 14-15 14S5 31 5 23V10z" fill="url(#sg3)"/><text x="20" y="26" text-anchor="middle" fill="#0c0c14" font-family="Arial,Helvetica,sans-serif" font-weight="900" font-size="18">M</text></svg></div>
        <h2>{{ config('app.name', 'Moody\'s') }}</h2>
        <p>{{ config('app.address') ?? config('app.name') }}</p>
        <p>{{ config('app.phone') ?? '—' }}</p>
    </div>

    <div class="invoice-meta">
        <div class="meta-item">
            <small>رقم الفاتورة</small>
            <strong>{{ $invoice->invoice_number }}</strong>
        </div>
        <div class="meta-item">
            <small>التاريخ</small>
            <strong>{{ $invoice->created_at->format('Y-m-d') }}</strong>
        </div>
        <div class="meta-item">
            <small>الحالة</small>
            <strong>
                @switch($invoice->status)
                    @case('draft') <span class="status-badge status-draft">مسودة</span> @break
                    @case('issued') <span class="status-badge status-issued">صادرة</span> @break
                    @case('paid') <span class="status-badge status-paid">مدفوعة</span> @break
                    @case('cancelled') <span class="status-badge status-cancelled">ملغية</span> @break
                @endswitch
            </strong>
        </div>
    </div>

    <div class="customer-info">
        <h6>معلومات العميل</h6>
        <p>{{ $invoice->customer_name }}</p>
        @if($invoice->customer_phone)<p style="font-weight:400;color:#64748b;direction:ltr">{{ $invoice->customer_phone }}</p>@endif
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>#</th>
                <th>المنتج</th>
                <th>الكمية</th>
                <th>سعر الوحدة</th>
                <th>الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ number_format($item->quantity, 2) }}</td>
                    <td class="amount-cell">{{ CurrencyHelper::formatDual($item->unit_price, $exchangeRate) }}</td>
                    <td class="amount-cell">{{ CurrencyHelper::formatDual($item->total, $exchangeRate) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="total-row"><span>الإجمالي الفرعي</span><span class="amount-cell">{{ CurrencyHelper::formatDual($invoice->subtotal, $exchangeRate) }}</span></div>
        @if($invoice->discount > 0)
            <div class="total-row"><span>الخصم</span><span class="amount-cell text-danger">-{{ CurrencyHelper::formatDual($invoice->discount, $exchangeRate) }}</span></div>
        @endif
        @if($invoice->tax > 0)
            <div class="total-row"><span>الضريبة</span><span class="amount-cell">{{ CurrencyHelper::formatDual($invoice->tax, $exchangeRate) }}</span></div>
        @endif
        <div class="total-row final"><span>الإجمالي النهائي</span><span class="amount-cell">{{ CurrencyHelper::formatDual($invoice->total, $exchangeRate) }}</span></div>
        <div class="total-row"><span>المدفوع</span><span class="amount-cell text-success">{{ CurrencyHelper::formatDual($invoice->paid, $exchangeRate) }}</span></div>
        <div class="total-row"><span>المتبقي</span><span class="amount-cell {{ $invoice->due > 0 ? 'text-danger' : 'text-success' }}">{{ CurrencyHelper::formatDual($invoice->due, $exchangeRate) }}</span></div>
    </div>

    @if($invoice->notes)
        <div style="margin-top:20px;padding:12px 16px;background:#fffbeb;border-radius:8px;border:1px solid #fde68a;">
            <small style="font-weight:600;color:#92400e;">ملاحظات:</small>
            <p style="margin:4px 0 0;font-size:12px;color:#92400e;">{{ $invoice->notes }}</p>
        </div>
    @endif

    <div class="invoice-footer">
        <p>شكراً لتعاملكم معنا</p>
        <p>{{ config('app.name', 'Moody\'s') }} &mdash; جميع الحقوق محفوظة &copy; {{ date('Y') }}</p>
    </div>

    <script>window.onload = function() { window.print(); }</script>
</body>
</html>
