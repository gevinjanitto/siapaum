<?php

namespace App\Http\Controllers;

use App\Models\OtpFailed;
use App\Models\OtpSuccess;
use Illuminate\Http\Request;

class LogSistemController extends Controller
{
    public function otpGagalIndex()
    {
        $otp_faileds = OtpFailed::all()->sortByDesc('created_at');
        return view('pages.admin.log-sistem.otp-gagal.index', compact(
            'otp_faileds'
        ));
    }

    public function otpSuksesIndex()
    {
        $otp_successes = OtpSuccess::all()->sortByDesc('created_at');
        return view('pages.admin.log-sistem.otp-sukses.index', compact(
            'otp_successes'
        ));
    }
}
