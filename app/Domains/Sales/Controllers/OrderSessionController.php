<?php

namespace App\Domains\Sales\Controllers;

use App\Domains\Auth\Models\Tenant;
use App\Domains\Sales\Models\OrderSession;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrderSessionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('tenant');
    }

    public function index()
    {
        $openSession = OrderSession::where('tenant_id', Auth::user()->tenant_id)
            ->where('status', 'open')
            ->with('orders')
            ->latest()
            ->first();

        $sessions = OrderSession::where('tenant_id', Auth::user()->tenant_id)
            ->with('orders')
            ->latest()
            ->paginate(15);

        $tenant = Tenant::find(Auth::user()->tenant_id);
        $exchangeRate = $tenant->settings['exchange_rate'] ?? 89500;

        return view('sales.sessions.index', compact('openSession', 'sessions', 'exchangeRate'));
    }

    public function store()
    {
        $tenantId = Auth::user()->tenant_id;

        OrderSession::where('tenant_id', $tenantId)
            ->where('status', 'open')
            ->update(['status' => 'closed', 'closed_at' => now()]);

        $session = OrderSession::create([
            'tenant_id' => $tenantId,
            'user_id' => Auth::id(),
            'opened_at' => now(),
            'status' => 'open',
            'total_cash' => 0,
            'total_card' => 0,
            'total_other' => 0,
        ]);

        session()->flash('success', 'تم بدء فترة مبيعات جديدة');

        return redirect()->route('sales.sessions.index');
    }

    public function show($id)
    {
        $session = OrderSession::with(['orders.items', 'orders.payments', 'user'])
            ->where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        $tenant = Tenant::find(Auth::user()->tenant_id);
        $exchangeRate = $tenant->settings['exchange_rate'] ?? 89500;

        return view('sales.sessions.show', compact('session', 'exchangeRate'));
    }

    public function close($id)
    {
        $session = OrderSession::with('orders')
            ->where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        $totalCash = $session->orders->sum(function ($order) {
            return $order->payments->where('method', 'cash')->sum('amount');
        });

        $totalCard = $session->orders->sum(function ($order) {
            return $order->payments->where('method', 'card')->sum('amount');
        });

        $totalOther = $session->orders->sum(function ($order) {
            return $order->payments->where('method', 'other')->sum('amount');
        });

        $session->update([
            'status' => 'closed',
            'closed_at' => now(),
            'total_cash' => $totalCash,
            'total_card' => $totalCard,
            'total_other' => $totalOther,
        ]);

        session()->flash('success', 'تم إغلاق الفترة بنجاح');

        return redirect()->route('sales.sessions.show', $id);
    }
}
