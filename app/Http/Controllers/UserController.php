<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{


    public function register(StoreUserRequest $request)
    {
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'mobile' => $request->phone,
            'password' => Hash::make($request->password),

        ]);

        Auth::login($user);

        return successResponse(message: trans('messages.auth.registered'));
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return successResponse(
                message: translate('messages.auth.login_successful')
            );
        }
        return errorResponse(
            message: translate('messages.auth.the_data_entered_is_incorrect'),
            state: 401,
        );
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/');
    }

    public function show(User $user)
    {
        $user->loadCount([
            'properties as properties_active' => function ($query) {
                $query->where('status_id', 'active');
            },
            'properties as properties_pending' => function ($query) {
                $query->where('status_id', 'inactive');
            },
            'properties as properties_rejected' => function ($query) {
                $query->where('status_id', 'rejected');
            }
        ]);
        return successResponse(
            data: $user
        );
    }

    public function blacklist(Request $request, User $user)
    {
        // هنا 
        $validated = $request->validate([
            'is_blacklist_reason' => ['required', 'string', 'min:10', 'max:500'],
        ]);

        $user->update(['is_blacklisted' => true, 'blacklist_reason' => $validated['is_blacklist_reason']]);

        return successResponse(
            message: 'User has been blacklisted successfully.'
        );
    }

    public function unblacklist(User $user)
    {
        $user->update(['is_blacklisted' => false, 'is_blacklist_reason' => null]);
        return successResponse(
            message: 'User has been reactivated successfully.'
        );
    }

    public function updateStatus(Request $request, User $user)
    {
        $validated = $request->validate([
            'is_active' => ['required', Rule::in(['active', 'inactive'])],
            'is_active_reason' => ['required', 'string', 'min:10', 'max:500'],
        ]);

        $user->is_active = $validated['is_active'] == 'active' ? true : false;
        $user->is_active_reason = $validated['is_active_reason'] ?? null;
        // يمكنك حفظ السبب في سجل (log) أو في جدول خاص بالتغييرات
        // UserActivityLog::create(['user_id' => $user->id, 'action' => 'status_changed', 'details' => ['new_status' => $user->status, 'reason' => $validated['reason']]]);
        $user->save();

        return successResponse(message: 'User status updated successfully.');
        // return response()->json(['message' => 'User status updated successfully.']);
    }
}
