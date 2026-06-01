@extends('layouts.app')

@section('title', 'التقارير')

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h1>التقارير</h1>
            <p>اختر نوع التقرير الذي تريد عرضه</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-3 col-lg-6">
        <div class="content-card h-100">
            <div class="card-body-custom text-center">
                <div class="qa-icon mx-auto mb-3" style="background: var(--gradient-sales); width: 64px; height: 64px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-calendar-day fa-2x text-white"></i>
                </div>
                <h5 class="fw-bold mb-2">التقرير اليومي</h5>
                <p class="text-muted small mb-3">عرض تقرير مفصل عن المبيعات والمصروفات ليوم محدد</p>
                <form action="{{ route('reports.daily') }}" method="GET" class="text-start">
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">اختر التاريخ</label>
                        <input type="date" name="date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <button type="submit" class="btn btn-gold w-100"><i class="fas fa-eye me-1"></i> عرض التقرير</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6">
        <div class="content-card h-100">
            <div class="card-body-custom text-center">
                <div class="qa-icon mx-auto mb-3" style="background: var(--gradient-expenses); width: 64px; height: 64px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-calendar-alt fa-2x text-white"></i>
                </div>
                <h5 class="fw-bold mb-2">التقرير الشهري</h5>
                <p class="text-muted small mb-3">عرض تقرير شامل عن أداء شهر معين</p>
                <form action="{{ route('reports.monthly') }}" method="GET" class="text-start">
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">الشهر</label>
                            <select name="month" class="form-select form-select-sm">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::createFromFormat('!m', (string) $m)->locale('ar')->format('F') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">السنة</label>
                            <select name="year" class="form-select form-select-sm">
                                @foreach(range(date('Y') - 5, date('Y') + 1) as $y)
                                    <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-gold w-100"><i class="fas fa-eye me-1"></i> عرض التقرير</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6">
        <div class="content-card h-100">
            <div class="card-body-custom text-center">
                <div class="qa-icon mx-auto mb-3" style="background: var(--gradient-orders); width: 64px; height: 64px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-chart-pie fa-2x text-white"></i>
                </div>
                <h5 class="fw-bold mb-2">الأرباح والخسائر</h5>
                <p class="text-muted small mb-3">قائمة الأرباح والخسائر لفترة زمنية محددة</p>
                <form action="{{ route('reports.profit-loss') }}" method="GET" class="text-start">
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">من</label>
                            <input type="date" name="from" class="form-control form-control-sm" value="{{ date('Y-m-01') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">إلى</label>
                            <input type="date" name="to" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-gold w-100"><i class="fas fa-eye me-1"></i> عرض التقرير</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6">
        <div class="content-card h-100">
            <div class="card-body-custom text-center">
                <div class="qa-icon mx-auto mb-3" style="background: var(--gradient-stock); width: 64px; height: 64px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-file-export fa-2x text-white"></i>
                </div>
                <h5 class="fw-bold mb-2">تصدير تقرير يومي</h5>
                <p class="text-muted small mb-3">تصدير تقرير يومي إلى ملف CSV</p>
                <form action="{{ route('reports.export-daily') }}" method="GET" class="text-start">
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">اختر التاريخ</label>
                        <input type="date" name="date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <button type="submit" class="btn btn-teal w-100"><i class="fas fa-download me-1"></i> تصدير CSV</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection