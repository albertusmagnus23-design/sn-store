<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.admin.dashboard.index', [
            'categoryCount' => Category::query()->count(),
            'productCount' => Product::query()->count(),
            'transactionCount' => Order::query()->count(),
            'userCount' => User::query()->count(),
        ]);
    }
}
