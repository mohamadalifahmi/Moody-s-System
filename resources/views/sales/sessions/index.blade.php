@extends('layouts.app')

@section('title', 'فترات المبيعات')

@section('content')
@php use App\Domains\Core\Helpers\CurrencyHelper; @endphp
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h1><i class="fas fa-chair text-gold me-2"></i>فترات المبيعات</h1>
        </div>
        <div class="page-actions">
            @if(!$openSession)
                <form method="POST" action="{{ route('sales.sessions.store') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-gold">
                        <i class="fas fa-play-circle me-1"></i>بدء فترة جديدة
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

@if($openSession)
    <div class="content-card mb-4" style="border-right: 4px solid var(--teal);">
        <div class="card-body-custom">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h5 class="mb-1"><i class="fas fa-circle text-teal" style="font-size:10px;"></i> الفترة المفتوحة</h5>
                    <div class="d-flex flex-wrap gap-4 mt-2">
                        <div>
                            <small class="text-muted d-block">رقم الفترة</small>
                            <span class="fw-bold">#{{ $openSession->session_number }}</span>
                        </div>
                        <div>
                            <small class="text-muted d-block">وقت الفتح</small>
                            <span class="time-cell">{{ $openSession->opened_at->format('Y-m-d H:i') }}</span>
                        </div>
                        <div>
                            <small class="text-muted d-block">بواسطة</small>
                            <span>{{ $openSession->openedBy->name ?? '-' }}</span>
                        </div>
                        <div>
                            <small class="text-muted d-block">عدد الطلبات</small>
                            <span class="fw-bold">{{ $openSession->orders_count ?? $openSession->orders->count() }}</span>
                        </div>
                        <div>
                            <small class="text-muted d-block">الإجمالي</small>
                            <span class="fw-bold amount-cell">{{ CurrencyHelper::formatDual($openSession->total_amount ?? 0, $exchangeRate) }}</span>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('sales.sessions.close', $openSession) }}" data-confirm="هل أنت متأكد من إغلاق الفترة #{{ $openSession->session_number }}؟">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-teal">
                        <i class="fas fa-stop-circle me-1"></i>إغلاق الفترة
                    </button>
                </form>
            </div>
        </div>
    </div>
@endif

<div class="content-card">
    <div class="card-header-custom">
        <h5><i class="fas fa-history me-1"></i>الفترات السابقة</h5>
    </div>
    <div class="card-body-custom">
        @if($sessions->count() > 0)
            <div class="table-responsive">
                <table class="table dashboard-table">
                    <thead>
                        <tr>
                            <th>رقم الفترة</th>
                            <th>تاريخ الفتح</th>
                            <th>تاريخ الإغلاق</th>
                            <th>عدد الطلبات</th>
                            <th>الإجمالي</th>
                            <th>أمين الصندوق</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sessions as $session)
                            <tr>
                                <td><span class="fw-bold">#{{ $session->session_number }}</span></td>
                                <td><span class="time-cell">{{ $session->opened_at->format('Y-m-d H:i') }}</span></td>
                                <td><span class="time-cell">{{ $session->closed_at ? $session->closed_at->format('Y-m-d H:i') : '-' }}</span></td>
                                <td>{{ $session->orders_count ?? $session->orders->count() }}</td>
                                <td><span class="amount-cell">{{ CurrencyHelper::formatDual($session->total_amount ?? 0, $exchangeRate) }}</span></td>
                                <td>{{ $session->openedBy->name ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('sales.sessions.show', $session) }}" class="btn-action" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-chair"></i>
                <p>لا توجد فترات سابقة</p>
            </div>
        @endif
    </div>
</div>

<div class="mt-3">
    {{ $sessions->links() }}
</div>
@endsection
