<?php

namespace App\Domains\Sales\Controllers;

use App\Domains\Auth\Models\ActivityLog;
use App\Domains\Sales\Models\Order;
use App\Domains\Sales\Models\Payment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('tenant');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,card,other',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $order = Order::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($validated['order_id']);

        $payment = Payment::create([
            'tenant_id' => Auth::user()->tenant_id,
            'order_id' => $order->id,
            'amount' => $validated['amount'],
            'method' => $validated['method'],
            'reference' => $validated['reference'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'created_by' => Auth::id(),
        ]);

        $totalPaid = $order->payments()->sum('amount');

        if ($totalPaid >= $order->total) {
            $order->update(['payment_status' => 'paid']);
        } elseif ($totalPaid > 0) {
            $order->update(['payment_status' => 'partial']);
        }

        ActivityLog::create([
            'tenant_id' => Auth::user()->tenant_id,
            'user_id' => Auth::id(),
            'action' => 'add_payment',
            'description' => 'تم إضافة دفعة بقيمة ' . number_format($validated['amount'], 2) . ' للطلب رقم ' . $order->order_number,
            'subject_type' => Payment::class,
            'subject_id' => $payment->id,
        ]);

        session()->flash('success', 'تم تسجيل الدفعة بنجاح');

        return redirect()->route('sales.orders.show', $order->id);
    }
}
