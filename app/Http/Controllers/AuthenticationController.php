<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use App\Mail\Auth\OtpRegisterEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class AuthenticationController extends Controller
{

    public function login(Request $request)
    {
        return view('pages.landing.login');
    }

    public function profile()
    {
        return view('pages.profile');
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'telepon' => 'required|string',
            'nik' => 'required|string',
            'jenis_kelamin' => 'required|string|in:0,1',
            'alamat' => 'required|string',
        ]);

        $user = auth()->user();
        $user->update($validated);

        if (!$user->is_filled_data_register) {
            $user->is_filled_data_register = true;
            $user->save();
            return back()->with('success', 'Profil berhasil disimpan. Anda sudah dapat mengakses dashboard');
        } else {
            return back()->with('success', 'Profil berhasil diperbarui');
        }
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'password_lama' => 'required|string',
            'password_baru' => 'required|string|min:8',
            'konfirmasi_password_baru' => 'required|same:password_baru',
        ]);

        $user = auth()->user();
        if (password_verify($validated['password_lama'], $user->password)) {
            $user->password = bcrypt($validated['password_baru']);
            $user->save();
            return back()->with('success', 'Password berhasil diperbarui');
        } else {
            return back()->with('error', 'Password lama tidak sesuai');
        }
    }

    public function authenticate(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required|string',
        ];

        if (config('recaptcha.enabled')) {
            $rules['g-recaptcha-response'] = 'recaptcha';
        }

        $request->validate($rules);

        if (auth()->attempt($request->only('email', 'password'))) {
            return redirect()->intended('dashboard');
        } else {
            return back()->with('error', 'Email atau password salah');
        }
    }
    public function register()
    {
        return view('pages.landing.register');
    }

    public function store(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
            'password_verify' => 'required|same:password',
            'otp_code' => 'required|digits:6',
        ];

        if (config('recaptcha.enabled')) {
            $rules['g-recaptcha-response'] = 'recaptcha';
        }

        $request->validate($rules);

        try {
            $otp_service = new OtpService();
            if ($otp_service->validate($request->email, 'register', $request->otp_code, $request->ip())) {
                $user = User::create([
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                    'role_id' => Role::where('nama', 'Public')->first()->id,
                ]);

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'redirect' => route('login.index'),
                    ],
                    'message' => 'Registrasi berhasil',
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kode OTP tidak valid',
                ]);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan, silahkan coba lagi nanti'
            ]);
        }
    }
    public function generateOtp(Request $request)
    {
        $rules = [
            'email' => 'required|email'
        ];

        if (config('recaptcha.enabled')) {
            $rules['g-recaptcha-response'] = 'recaptcha';
        }

        $request->validate($rules);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            // TODO : set rate limiter
            $otp = OtpService::generate($request->email, 'register', $request->ip());
            Mail::to($request->email)->queue(new OtpRegisterEmail($request->email, $otp::$plain_token));
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'resend_after' => 60 * 5,
                'last_otp_at' => isset($otp) ? $otp::$expired_at->setTimezone('GMT+8')->format('Y-m-d H:i:s') : now()->addDay()->setTimezone('GMT+8')->format('Y-m-d H:i:s'),
            ],
            'message' => 'Kode OTP berhasil dikirim ke email',
        ]);
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->route('login.index')->with('success', 'Berhasil logout');
    }

    // FORGOT PASSWORD
    public function forgotPassword()
    {
        return view('pages.landing.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $rules = [
            'email' => 'required|email',
        ];

        if (config('recaptcha.enabled')) {
            $rules['g-recaptcha-response'] = 'recaptcha';
        }

        $request->validate($rules);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $status = Password::sendResetLink(
                $request->only('email')
            );

            return $status === Password::RESET_LINK_SENT
                ? back()->with('success', 'Tautan reset password telah dikirim ke email Anda')
                : back()->withErrors(['email' => $this->passwordStatusMessage($status)]);
        } else {
            return back()->with('error', 'Email tidak terdaftar');
        }
    }

    public function resetPassword(Request $request, $token)
    {
        return view('pages.landing.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function updatePasswordReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
            'password_verify' => 'required|same:password',
            'token' => 'required|string',
        ], [
            'password.min' => 'Password minimal 8 karakter',
            'password_verify.same' => 'Konfirmasi password tidak sama',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_verify', 'token'),
            function ($user, $password) {
                $user->password = bcrypt($password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login.index')->with('success', 'Password berhasil diubah, silakan login dengan password baru')
            : back()->withErrors(['email' => $this->passwordStatusMessage($status)]);
    }

    private function passwordStatusMessage(string $status): string
    {
        return match ($status) {
            Password::INVALID_TOKEN => 'Tautan reset password tidak valid atau sudah kedaluwarsa',
            Password::RESET_THROTTLED => 'Mohon tunggu sebentar sebelum meminta tautan lagi',
            Password::INVALID_USER => 'Email tidak terdaftar',
            default => __($status),
        };
    }
}
