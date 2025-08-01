<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {

        $user = auth()->user();

        $breadcrumbs = [
            ['name' => __('dashboard/layouts.aside.dashboard'), 'url' => route('dashboard.home')],
            ['name' => __('dashboard/subscriptions.page_title'), 'url' => null],
        ];            

        // يمكنك تمرير باقات الاشتراك من هنا
        $packages = [
            // ['name' => 'شهري', 'duration' => 30],
            // ['name' => '3 أشهر', 'duration' => 90],
            // ['name' => 'سنوي', 'duration' => 365],
            ['name' => '30 day', 'duration' => 30],
            ['name' => '90 day', 'duration' => 90],
            ['name' => '365 day', 'duration' => 365],
        ];

        return view('dashboard.pages.subscriptions.list', compact('breadcrumbs', 'packages', 'user'));
    }
}
