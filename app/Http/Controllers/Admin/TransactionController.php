<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(): View
    {
        return view('pages.admin.transactions.index', [
            'transactions' => Order::query()
                ->with('items')
                ->latest()
                ->paginate(15),
            'totalTransactions' => Order::query()->count(),
            'paidTransactions' => Order::query()->where('status', Order::STATUS_PAID)->count(),
            'pendingTransactions' => Order::query()->where('status', Order::STATUS_PENDING)->count(),
            'completedRevenue' => Order::query()->where('status', Order::STATUS_PAID)->sum('total'),
        ]);
    }
}
