<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserAuthVerifyRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function verify(UserAuthVerifyRequest $request): RedirectResponse
    {
        $credentials = $request->only('username', 'password');

        // Check admin login
        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        // Check panitia login
        if (Auth::guard('panitia')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        // Check regular user login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        // Check if username exists in any guard
        $adminExists = Auth::guard('admin')->getProvider()->retrieveByCredentials(['username' => $credentials['username']]);
        $panitiaExists = Auth::guard('panitia')->getProvider()->retrieveByCredentials(['username' => $credentials['username']]);
        $userExists = Auth::guard('web')->getProvider()->retrieveByCredentials(['username' => $credentials['username']]);

        if (!$adminExists && !$panitiaExists && !$userExists) {
            return back()
                ->withErrors(['username' => 'Username tidak terdaftar dalam sistem'])
                ->withInput($request->except('password'));
        }

        return back()
            ->withErrors(['password' => 'Password yang Anda masukkan salah'])
            ->withInput($request->except('password'));
    }

    public function logout(Request $request): RedirectResponse
    {
        // Determine which guard was used for authentication
        $guardName = null;
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
            $guardName = 'Admin';
        } elseif (Auth::guard('panitia')->check()) {
            Auth::guard('panitia')->logout();
            $guardName = 'Panitia';
        } elseif (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
            $guardName = 'User';
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')
            ->with('status', ($guardName ? "$guardName berhasil logout" : 'Berhasil logout') . ' dari sistem');
    }
}






// <?php
// namespace App\Http\Controllers\Auth;

// use App\Http\Controllers\Controller;
// use App\Http\Requests\UserAuthVerifyRequest;
// use Illuminate\Http\RedirectResponse;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Http\Request;

// class AuthController extends Controller
// {
//     public function index()
//     {
//         return view('auth.login');
//     }

//     public function verify(UserAuthVerifyRequest $request): RedirectResponse
//     {
//         $credentials = $request->only('username', 'password');

//         // Check admin login
//         if (Auth::guard('admin')->attempt($credentials)) {
//             $request->session()->regenerate();
//             return redirect()->route('dashboard');
//         }

//         // Check panitia login
//         if (Auth::guard('panitia')->attempt($credentials)) {
//             $request->session()->regenerate();
//             return redirect()->route('dashboard');
//         }

//         // Check regular user login
//         if (Auth::attempt($credentials)) {
//             $request->session()->regenerate();
//             return redirect()->route('dashboard');
//         }

//         // Specific error messages for each guard
//         $errorMessage = 'Username atau password salah';

//         // Check if username exists in any guard
//         $userAdmin = Auth::guard('admin')->getProvider()->retrieveByCredentials(['username' => $request->username]);
//         $userPanitia = Auth::guard('panitia')->getProvider()->retrieveByCredentials(['username' => $request->username]);
//         $userWeb = Auth::guard('web')->getProvider()->retrieveByCredentials(['username' => $request->username]);

//         // Check if username exists in each guard to provide more specific error messages
//         if ($userAdmin && !Auth::guard('admin')->validate($credentials)) {
//             $errorMessage = 'Password untuk akun admin salah';
//         } elseif ($userPanitia && !Auth::guard('panitia')->validate($credentials)) {
//             $errorMessage = 'Password untuk akun panitia salah';
//         } elseif ($userWeb && !Auth::validate($credentials)) {
//             $errorMessage = 'Password untuk akun pengguna salah';
//         } elseif (!$userAdmin && !$userPanitia && !$userWeb) {
//             $errorMessage = 'Username tidak ditemukan';
//         }

//         return back()
//             ->withErrors(['username' => $errorMessage])
//             ->withInput($request->except('password'));
//     }

//     public function logout(Request $request): RedirectResponse
//     {
//         // Logout from all guards
//         if (Auth::guard('admin')->check()) {
//             Auth::guard('admin')->logout();
//         }
//         if (Auth::guard('panitia')->check()) {
//             Auth::guard('panitia')->logout();
//         }
//         if (Auth::guard('web')->check()) {
//             Auth::guard('web')->logout();
//         }

//         $request->session()->invalidate();
//         $request->session()->regenerateToken();

//         return redirect('/login');
//     }
// }
