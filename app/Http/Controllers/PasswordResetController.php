<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class PasswordResetController extends Controller
{
    protected $adminWhatsApp = '081299478297';

    public function showLinkRequestForm()
    {
        return view('auth.passwords.wa');
    }

    public function sendResetLinkWhatsApp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Email tidak ditemukan dalam sistem.']);
        }

        // Format pesan WhatsApp
        $message = "Halo Admin PPDB, saya ingin reset password untuk akun:\n\n"
                . "Email: " . $request->email . "\n"
                . "Nama: " . $user->name . "\n\n"
                . "Mohon bantuan untuk reset password. Terima kasih.";

        // Encode pesan untuk URL WhatsApp
        $encodedMessage = urlencode($message);

        // Generate URL WhatsApp
        $whatsappUrl = "https://wa.me/{$this->adminWhatsApp}?text={$encodedMessage}";

        return redirect()->away($whatsappUrl);
    }
}
