<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPassword;
use App\Http\Requests\Auth\Login;
use App\Http\Requests\Auth\Register;
use App\Http\Requests\Auth\ResetPassword;
use App\Http\Requests\Auth\UpdateInfoRequest;
use App\Http\Requests\Auth\UpdatePasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    /**
     * login handler.
     */
    public function login(Login $request)
    {
        $credentials = $request->only('password');
        $loginIdentifier = $request->input('login_identifier');

        if (filter_var($loginIdentifier, FILTER_VALIDATE_EMAIL)) {
            $credentials['email'] = $loginIdentifier;
        } else {
            $credentials['mobile'] = $loginIdentifier;
        }

        if (Auth::attempt($credentials)) {

            /** @var User $user */
            $user = Auth::user();
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'message' => __('messages.auth.logged_in_successfully'),
                'token' => $token,
                'user' => $user->only('id', 'name', 'email')
            ]);
        }

        return response()->json([
            'message' => __('messages.auth.credentials_do_not_match')
        ], 401);
    }

    /**
     * register handler.
     */
    public function register(Register $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'message' => __('messages.auth.registration_successful_logged_in'),
                'token' => $token,
                'user' => $user->only('id', 'name', 'email')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('messages.generic.generic_error_try_again')
            ], 500);
        }
    }

    /**
     * تسجيل الخروج - يعمل مع كلا النوعين
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json([
            'message' => __('messages.auth.logged_out_successfully')
        ]);
    }


    public function updateInfo(UpdateInfoRequest $request)
    {
        /** @var User $user user.  */
        $user = Auth::user();

        $data = $request->validated();

        try {
            $updated = $this->userRepository->update($user, $data);

            return successResponse(message: trans('messages.profile.success_message'));
        } catch (\Exception $e) {
            return errorResponse(
                message: trans('messages.generic.generic_error_try_again'),
                state: 401,
            );
        }
    }

    public function updateAvatar(UpdateInfoRequest $request)
    {
        try {

            /** @var User $user user.  */
            $user = Auth::user();

            $validated = $request->validated();

            $hasOldImage = is_null($user->image);
            if (isset($validated['image']) && !is_null(isset($validated['image']))) {
                // new or update
                $fileName = $this->userRepository->proceedImage(file: $validated['image'], oldFileName: $user->image);
                $validated['image'] = $fileName;
            } else if ($hasOldImage && is_null(isset($validated['image']))) {
                // delete image.
                $this->userRepository->proceedImageDelete($user->image);
                $validated['image'] = null;
            }
            $categoryModel = $this->userRepository->update($user, $validated);

            return successResponse(message: trans('messages.profile.success_message'));
        } catch (\Exception $e) {
            return errorResponse(
                message: trans('messages.generic.generic_error_try_again'),
                state: 401,
            );
        }
    }

    public function deleteAvatar()
    {
        try {
            /** @var User $user user.  */
            $user = Auth::user();
            if (!is_null($user->image)) {
                Storage::disk('public')->delete($user->image);
                $user->image = null;
                $user->save();
                return successResponse(
                    message: trans('messages.profile.success_message'),
                    data: [
                        'default_avatar_url' => $user->image_url
                    ]
                );
            }
        } catch (\Exception $e) {
            return errorResponse(
                message: trans('messages.generic.generic_error_try_again'),
                state: 401,
            );
        }
    }

    public function unauthenticated(Request $request)
    {

        $redirectTo = route('home', ['locale' => config('app.locale')]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return redirect($redirectTo);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = Auth::user();

        try {
            // if (!$this->userRepository->checkOldPassword($user, $request->old_password)) {
            //     return errorResponse(
            //         message: trans('messages.auth.old_password_not_match'),
            //         state: 401,
            //     );
            // }

            $this->userRepository->updatePassword($user, $request->new_password);

            return successResponse(message: trans('messages.profile.success_message'));
        } catch (\Exception $e) {
            return errorResponse(
                message: trans('messages.generic.generic_error_try_again'),
                state: 401,
            );
        }
    }

    /**
     * إنشاء توكن API إذا كان الطلب من نوع API
     */
    protected function createApiTokenIfNeeded(Request $request, User $user): ?string
    {
        if ($request->is('api/*') || $request->expectsJson()) {
            // حذف التوكنات القديمة إذا لزم الأمر
            $user->tokens()->delete();

            // إنشاء توكن جديد مع الصلاحيات المناسبة
            return $user->createToken('api-token')->plainTextToken;
            // return $user->createToken('api-token', [
            //     'view-properties',
            //     'manage-favorites'
            // ])->plainTextToken;
        }

        return null;
    }

    /**
     * إبطال توكن API
     */
    protected function revokeApiToken(Request $request, ?User $user): void
    {
        if ($user && ($request->is('api/*') || $request->expectsJson())) {
            $user->tokens()->delete();
        }
    }

    /**
     * create redirect url after login.
     */
    protected function determineRedirectUrl(Request $request, bool $isRegister = false): string
    {
        $previousUrl = url()->previous();

        if ($isRegister) {
            return in_array($previousUrl, [route('auth.login'), route('auth.register')])
                ? route('home')
                : $previousUrl;
        }

        return ($previousUrl === route('auth.login'))
            ? route('home', ['locale' => app()->getLocale()])
            : $previousUrl;
    }

    /**
     * تحديد عنوان التحويل بعد تسجيل الخروج
     */
    protected function determineLogoutRedirectUrl(Request $request): string
    {
        $previousUrl = url()->previous();
        $path = parse_url($previousUrl, PHP_URL_PATH);

        if (Str::contains($path, '/dashboard')) {
            return route('home');
        }

        return wasProtectedRoute($previousUrl) ? route('home') : $previousUrl;
    }

    /**
     * إعداد الاستجابة للمصادقة الناجحة
     */
    protected function prepareAuthResponse(
        Request $request,
        string $message,
        string $redirectUrl,
        ?string $token = null
    ) {
        $responseData = ['redirect' => $redirectUrl];
        if ($token) {
            $responseData['token'] = $token;
            $responseData['user'] = Auth::user()->only('id', 'name', 'email');
        }

        if ($request->is('api/*') || $request->expectsJson()) {
            return successResponse($message, $responseData);
        }

        return successResponse($message, $responseData);
    }

    /**
     * إعداد الاستجابة لتسجيل الخروج
     */
    protected function prepareLogoutResponse(Request $request, string $redirectUrl)
    {
        $message = __('messages.auth.logged_out_successfully');

        if ($request->is('api/*') || $request->expectsJson()) {
            return successResponse($message, ['redirect' => $redirectUrl]);
        }

        return redirect($redirectUrl);
    }

    /**
     * إعداد استجابة الخطأ
     */
    protected function prepareErrorResponse(Request $request, string $message, int $status = 400)
    {
        if ($request->is('api/*') || $request->expectsJson()) {
            return errorResponse($message, $status);
        }

        return back()->withErrors(['general' => $message]);
    }

    public function sendResetLinkEmail(ForgotPassword $request)
    {

        $user = User::where('email', $request->login_identifier)
            ->orWhere('mobile', $request->login_identifier)
            ->first();

        if (!$user || !$user->email) {
            return response()->json([
                'message' => __('passwords.user'),
            ], 422);
        }

        $status = Password::sendResetLink(['email' => $user->email]);

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)], 200)
            : response()->json(['message' => __($status)], 422);
    }


    public function resetPassword(ResetPassword $request)
    {

        $validatedData = $request->validated();

        $credentials = [
            'token' => $validatedData['token'],
            'password' => $validatedData['password'],
            'password_confirmation' => $validatedData['password_confirmation'],
        ];

        try {
            // 3. Decrypt the email
            $credentials['email'] = Crypt::decryptString($validatedData['email']);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // If decryption fails, the link was tampered with or invalid.
            return response()->json(['message' => __('passwords.token')], 422);
        }

        $status = Password::broker()->reset(
            $credentials,
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ?    successResponse(__($status))
            : errorResponse(__($status), 422);
    }

    public function resetPasswordPage(Request $request, string $locale, $token, $email)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email
        ]);
    }

    public function updateNotifications(Request $request)
    {
        $user = Auth::user();

        // $validatedData = $request->validate([
        //     'email_notifications' => 'nullable|boolean',
        //     'newsletter_notifications' => 'nullable|boolean',
        // ]);

        $dataToUpdate = [
            'email_notifications' => $request->boolean('email_notifications'),
            'newsletter_notifications' => $request->boolean('newsletter_notifications'),
        ];

        try {
            $this->userRepository->update($user, $dataToUpdate);

            return successResponse(message: trans('messages.profile.success_message'));
        } catch (\Exception $e) {
            return errorResponse(
                message: trans('messages.generic.generic_error_try_again')
            );
        }
    }
}
