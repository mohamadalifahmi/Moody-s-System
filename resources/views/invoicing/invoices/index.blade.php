@extends('layouts.app')
@php use App\Domains\Core\Helpers\CurrencyHelper; @endphp

@section('title', 'الفواتير')

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h1><i class="fas fa-file-invoice text-gold me-2"></i> الفواتير</h1>
            <p class="text-muted">إدارة ومتابعة الفواتير</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('invoicing.invoices.create') }}" class="btn btn-gold"><i class="fas fa-plus-circle"></i> إنشاء فاتورة جديدة</a>
        </div>
    </div>
</div>

<div class="content-card mb-4">
    <div class="card-body-custom">
        <form method="GET" action="{{ route('invoicing.invoices.index') }}" class="row g-3">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="بحث برقم الفاتورة..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">جميع الحالات</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                    <option value="issued" {{ request('status') == 'issued' ? 'selected' : '' }}>صادرة</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>مدفوعة</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغية</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control" placeholder="من تاريخ" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control" placeholder="إلى تاريخ" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-teal"><i class="fas fa-filter"></i> تصفية</button>
                <a href="{{ route('invoicing.invoices.index') }}" class="btn btn-outline-gold"><i class="fas fa-undo"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="content-card">
    <div class="card-body-custom p-0">
        @if($invoices->count() > 0)
            <div class="table-responsive">
                <table class="table dashboard-table mb-0">
                    <thead>
                        <tr>
                            <th>رقم الفاتورة</th>
                            <th>العميل</th>
                            <th>التاريخ</th>
                            <th>الإجمالي</th>
                            <th>المدفوع</th>
                            <th>المتبقي</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $invoice)
                            <tr>
                                <td><span class="order-number">{{ $invoice->invoice_number }}</span></td>
                                <td>{{ $invoice->customer_name }}</td>
                                <td class="time-cell">{{ $invoice->created_at->format('Y-m-d') }}</td>
                                <td class="amount-cell">{{ CurrencyHelper::formatDual($invoice->total, $exchangeRate) }}</td>
                                <td class="amount-cell">{{ CurrencyHelper::formatDual($invoice->paid, $exchangeRate) }}</td>
                                <td class="amount-cell {{ $invoice->due > 0 ? 'text-danger' : 'text-success' }}">{{ CurrencyHelper::formatDual($invoice->due, $exchangeRate) }}</td>
                                <td>
                                    @switch($invoice->status)
                                        @case('draft') <span class="status-badge status-pending"><i class="fas fa-file"></i> مسودة</span> @break
                                        @case('issued') <span class="status-badge status-processing"><i class="fas fa-check-circle"></i> صادرة</span> @break
                                        @case('paid') <span class="status-badge status-completed"><i class="fas fa-check-double"></i> مدفوعة</span> @break
                                        @case('cancelled') <span class="status-badge status-cancelled"><i class="fas fa-times-circle"></i> ملغية</span> @break
                                        @default <span class="status-badge status-pending">{{ $invoice->status }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    <a href="{{ route('invoicing.invoices.show', $invoice->id) }}" class="btn-action" title="عرض"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('invoicing.invoices.print', $invoice->id) }}" class="btn-action" title="طباعة" target="_blank"><i class="fas fa-print"></i></a>
                                    @if(in_array($invoice->status, ['issued']))
                                        <form method="POST" action="{{ route('invoicing.invoices.mark-paid', $invoice->id) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn-action" title="تعيين كمدفوع" onclick="return confirm('تأكيد تحديد الفاتورة كمدفوعة؟')"><i class="fas fa-check-circle text-success"></i></button>
                                        </form>
                                    @endif
                                    @if(in_array($invoice->status, ['draft', 'issued']))
                                        <form method="POST" action="{{ route('invoicing.invoices.mark-cancelled', $invoice->id) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn-action" title="إلغاء" onclick="return confirm('تأكيد إلغاء الفاتورة؟')"><i class="fas fa-ban text-danger"></i></button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-body-custom border-top">{{ $invoices->links() }}</div>
        @else
            <div class="empty-state">
                <i class="fas fa-file-invoice"></i>
                <p>لا توجد فواتير بعد</p>
                <a href="{{ route('invoicing.invoices.create') }}" class="btn btn-gold mt-3"><i class="fas fa-plus-circle"></i> إنشاء فاتورة جديدة</a>
            </div>
        @endif
    </div>
</div>
@endsection
