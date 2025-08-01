<?php

namespace App\Http\Controllers\Web;

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

        if (Auth::guard('web')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();
            // $token = $this->createApiTokenIfNeeded($request, $user);
            // session(['api_token' => $token]);
            $redirectUrl = $this->determineRedirectUrl($request);

            return $this->prepareAuthResponse(
                $request,
                __('messages.auth.logged_in_successfully'),
                $redirectUrl,
                // $token
            );
        }

        return $this->prepareErrorResponse(
            $request,
            __('messages.auth.credentials_do_not_match'),
            401
        );
    }

    /**
     * register handler.
     */
    public function register(Register $request)
    {
        try {
            $user = $this->userRepository->store($request->all());
            Auth::guard('web')->login($user);
            $request->session()->regenerate();

            $token = $this->createApiTokenIfNeeded($request, $user);

            // تحديد صفحة التحويل المناسبة
            $redirectUrl = $this->determineRedirectUrl($request, true);

            return $this->prepareAuthResponse(
                $request,
                __('messages.auth.registration_successful_logged_in'),
                $redirectUrl,
                $token
            );
        } catch (\Exception $e) {
            return $this->prepareErrorResponse(
                $request,
                __('messages.generic.generic_error_try_again'),
                500
            );
        }
    }

    /**
     * تسجيل الخروج - يعمل مع كلا النوعين
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // تحديد صفحة التحويل
        $redirectUrl = $this->determineLogoutRedirectUrl($request);

        return $this->prepareLogoutResponse($request, $redirectUrl);
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
        /** @var User $user user.  */
        $user = Auth::user();

        try {
            if (!$this->userRepository->checkOldPassword($user, $request->old_password)) {
                return errorResponse(
                    message: trans('messages.auth.old_password_not_match'),
                    state: 401,
                );
            }

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

        // if ($isRegister) {
        //     return in_array($previousUrl, [route('auth.login',['locale'=>app()->getLocale()]), route('auth.register')])
        //         ? route('home')
        //         : $previousUrl;
        // }

        // return ($previousUrl === route('auth.login',['locale'=>app()->getLocale()]))
        //     ? route('home', ['locale' => app()->getLocale()])
        //     : $previousUrl;
       
            return $previousUrl;
    }

    /**
     * تحديد عنوان التحويل بعد تسجيل الخروج
     */
    protected function determineLogoutRedirectUrl(Request $request): string
    {
        $previousUrl = url()->previous();
        $path = parse_url($previousUrl, PHP_URL_PATH);

        if (Str::contains($path, '/dashboard')) {
            return route('home', ['locale' => config('app.locale')]);
        }

        return wasProtectedRoute($previousUrl) ?  route('home', ['locale' => config('app.locale')]) : $previousUrl;
    }

    /**
     * إعداد الاستجابة للمصادقة الناجحة
     */
    protected function prepareAuthResponse(
        Request $request,
        string $message,
        string $redirectUrl,
    ) {
        $responseData = ['redirect' => $redirectUrl];
        $responseData['user'] = Auth::user()->only('id', 'name', 'email');
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
}
