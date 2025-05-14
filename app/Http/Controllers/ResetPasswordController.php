<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ResetPasswordController extends Controller
{
    public function showResetForm()
    {
        return view('password.reset');
    }

     public function showEmailForm()
    {
        return view('password.reset');
    }

    public function sendCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $roleTable = [
            'pembeli' => 'email_pembeli',
            'penitip' => 'email_penitip',
            'organisasi' => 'email_organisasi',
        ];

        $user = null;
        $role = null;
        $emailColumn = null;

        foreach ($roleTable as $table => $column) {
            $user = DB::table($table)->where($column, $request->email)->first();
            if ($user) {
                $role = $table;
                $emailColumn = $column;
                break;
            }
        }

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan di sistem.']);
        }

        $kode = rand(100000, 999999);
        session([
            'reset_email' => $request->email,
            'reset_kode' => $kode,
            'reset_role' => $role,
            'reset_email_column' => $emailColumn
        ]);

        Mail::raw("Kode verifikasi Anda adalah: $kode", function ($message) use ($request) {
            $message->to($request->email)->subject('Kode Verifikasi Reset Password');
        });

        return back()->with('success', 'Kode verifikasi telah dikirim ke email.');
    }


    public function verifyCode(Request $request)
    {
        $request->validate(['kode' => 'required']);

        if ($request->kode == session('reset_kode')) {
            session(['reset_verified' => true]);
        }

        return back()->withErrors(['kode' => 'Kode salah.']);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        if (!session('reset_verified')) {
            return redirect()->route('reset.form')->withErrors(['error' => 'Akses tidak valid.']);
        }

        $role = session('reset_role');
        $email = session('reset_email');
        $emailColumn = session('reset_email_column');
        $hashed = Hash::make($request->password);

        DB::table($role)->where($emailColumn, $email)->update(['password' => $hashed]);

        session()->forget(['reset_email', 'reset_kode', 'reset_role', 'reset_verified', 'reset_email_column']);

        return redirect('/')->with('success', 'Password berhasil diubah. Silakan login kembali.');
    }
}
