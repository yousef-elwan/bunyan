<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Lang;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $langs = Lang::active()->get();

        $breadcrumbs = [
            ['name' => __('dashboard/layouts.aside.dashboard'), 'url' => route('dashboard.home')],
            ['name' => __('dashboard/layouts.aside.my_profile'), 'url' => null],
        ];

        return view('dashboard.pages.profile.list', compact('langs', 'user','breadcrumbs'));
    }
}
