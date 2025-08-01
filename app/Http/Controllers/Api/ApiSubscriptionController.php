<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ApiSubscriptionController extends Controller
{
    public function searchUsers(Request $request)
    {
        $searchTerm = $request->input('q');
        if (empty($searchTerm)) {
            return response()->json([]);
        }

        $users = User::where(function ($query) use ($searchTerm) {

            $query->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$searchTerm}%")
                ->orWhere('email', 'like', "%{$searchTerm}%")
                ->orWhere('mobile', 'like', "%{$searchTerm}%");
        })
            ->where('id', '!=', Auth::id()) // استبعاد المدير نفسه
            ->select('id', 'first_name', 'last_name', 'email', 'mobile', 'image')
            ->take(10)
            ->get();
        // FIX: Add a 'name' attribute for TomSelect to use
        // ->map(function ($user) {
        //     $user->name = trim($user->first_name . ' ' . $user->last_name);
        //     return $user;
        // });

        return successResponse(data: $users);
    }

    public function getSubscriptionHistory(User $user)
    {
        $subscriptions = $user->subscriptions()->with('admin:id,first_name,last_name')->get();
        return successResponse(data: $subscriptions);
    }

    // public function store(Request $request, User $user)
    // {
    //     $validated = $request->validate([
    //         'package_name' => ['required', 'string'],
    //         'duration_in_days' => ['required', 'integer', 'min:1'],
    //         'start_date' => ['required', 'date_format:Y-m-d'], // Validate start date
    //         'price' => ['nullable', 'numeric'],
    //         'payment_method' => ['nullable', 'string'],
    //         'notes' => ['nullable', 'string'],
    //     ]);

    //     try {
    //         DB::beginTransaction();

    //         // Use the provided start date
    //         $startDate = Carbon::parse($validated['start_date']);
    //         $endDate = $startDate->copy()->addDays($validated['duration_in_days']);

    //         // Create new subscription record
    //         Subscription::create([
    //             'user_id' => $user->id,
    //             'admin_id' => Auth::id(),
    //             'package_name' => $validated['package_name'],
    //             'duration_in_days' => $validated['duration_in_days'],
    //             'start_date' => $startDate->toDateString(),
    //             'end_date' => $endDate->toDateString(),
    //             'price' => $validated['price'],
    //             'payment_method' => $validated['payment_method'] ?? null,
    //             'notes' => $validated['notes'] ?? null,
    //         ]);

    //         // Update the user's main subscription dates
    //         // We should check if this new subscription is the one that extends the furthest
    //         $currentEndDate = $user->subscription_end ? Carbon::parse($user->subscription_end) : null;
    //         if (!$currentEndDate || $endDate->isAfter($currentEndDate)) {
    //             $user->subscription_end = $endDate->toDateString();
    //             // Optionally update start date if it's the very first subscription
    //             if (!$user->subscription_start) {
    //                 $user->subscription_start = $startDate->toDateString();
    //             }
    //             $user->save();
    //         }

    //         DB::commit();

    //         return response()->json(['message' => 'Subscription added successfully.'], 201);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json(['message' => 'An error occurred.', 'error' => $e->getMessage()], 500);
    //     }
    // }

    public function store(Request $request, User $user)
    {
        // 1. التحقق من صحة المدخلات
        $validated = $request->validate([
            'package_name'     => ['required', 'string', 'max:255'],
            'duration_in_days' => ['required', 'integer', 'min:1'],
            'start_date'       => ['required', 'date_format:Y-m-d'],
            'price'            => ['nullable', 'numeric', 'min:0'],
            'payment_method'   => ['nullable', 'string', 'max:255'],
            'notes'            => ['nullable', 'string'],
        ]);

        try {
            // 2. استخدام Transaction لضمان سلامة البيانات
            // إما أن تنجح كل العمليات، أو تفشل كلها.
            $result = DB::transaction(function () use ($validated, $user) {

                // 3. تحويل التواريخ إلى كائنات Carbon لسهولة التعامل معها
                $newSubscriptionStartDateFromInput = Carbon::parse($validated['start_date']);
                $currentSubscriptionEndDate = $user->subscription_end ? Carbon::parse($user->subscription_end) : null;
                $durationInDays = $validated['duration_in_days'];

                // 4. المنطق الأساسي: تحديد تاريخ البدء والانتهاء الفعلي للاشتراك
                $finalStartDate = null;

                // الحالة أ: إذا كان المستخدم لديه اشتراك سارٍ (تمديد)
                if ($currentSubscriptionEndDate && $currentSubscriptionEndDate->isFuture()) {
                    // تاريخ البدء الجديد هو اليوم التالي لانتهاء الاشتراك الحالي
                    $finalStartDate = $currentSubscriptionEndDate->copy()->addDay();
                }
                // الحالة ب: إذا كان الاشتراك منتهيًا أو لا يوجد (اشتراك جديد)
                else {
                    // نستخدم تاريخ البدء الذي أدخله المدير
                    $finalStartDate = $newSubscriptionStartDateFromInput;
                }

                // حساب تاريخ الانتهاء بناءً على تاريخ البدء الصحيح
                $finalEndDate = $finalStartDate->copy()->addDays($durationInDays);

                // 5. إنشاء سجل جديد في جدول `subscriptions`
                Subscription::create([
                    'user_id'          => $user->id,
                    'admin_id'         => Auth::id(),
                    'package_name'     => $validated['package_name'],
                    'duration_in_days' => $durationInDays,
                    'start_date'       => $finalStartDate->toDateString(),
                    'end_date'         => $finalEndDate->toDateString(),
                    'price'            => $validated['price'],
                    'payment_method'   => $validated['payment_method'] ?? null,
                    'notes'            => $validated['notes'] ?? null,
                ]);

                // 6. تحديث الحقول الرئيسية في جدول `users`
                $user->subscription_end = $finalEndDate->toDateString();

                // نحدّث تاريخ بدء الاشتراك في جدول المستخدمين فقط إذا كان الاشتراك يبدأ من جديد
                if (!$currentSubscriptionEndDate || $currentSubscriptionEndDate->isPast()) {
                    $user->subscription_start = $finalStartDate->toDateString();
                }

                $user->save();

                // إرجاع بيانات المستخدم المحدثة للواجهة الأمامية إذا لزم الأمر
                return $user->fresh();
            });

            // 7. إرجاع استجابة نجاح مع بيانات المستخدم المحدثة
            return response()->json([
                'message' => 'Subscription added successfully.',
                'user' => $result // إرجاع كائن المستخدم بعد التحديث
            ], 201);
        } catch (\Exception $e) {
            // 8. في حالة حدوث أي خطأ، Transaction ستقوم بالتراجع تلقائيًا
            // إرجاع رسالة خطأ مفصلة للمطور
            return response()->json([
                'message' => 'An error occurred while adding the subscription.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
