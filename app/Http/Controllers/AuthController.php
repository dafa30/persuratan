<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // 🔹 Tampilkan halaman login
    public function showLogin()
    {
        return view('auth.login');
    }

    // 🔹 Proses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => [
                'required',
                'string',
                Password::min(8)->letters()->numbers()->symbols()->mixedCase(),
            ],
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',

            'password.required' => 'Kata sandi wajib diisi.',
            'password.min'      => 'Kata sandi minimal harus terdiri dari :min karakter.',
            'password.letters'  => 'Kata sandi harus mengandung setidaknya satu huruf.',
            'password.numbers'  => 'Kata sandi harus mengandung setidaknya satu angka.',
            'password.symbols'  => 'Kata sandi harus mengandung setidaknya satu simbol.',
            'password.mixed'    => 'Kata sandi harus mengandung huruf besar dan huruf kecil.',
        ]);

        $throttleKey = 'login:' . hash('sha256', Str::lower($credentials['email']));
        $lockoutKey = $throttleKey . ':lockout';

        if (RateLimiter::tooManyAttempts($lockoutKey, 1)) {
            $minutes = (int) ceil(RateLimiter::availableIn($lockoutKey) / 60);

            return back()->withErrors([
                'email' => "Akun ditangguhkan sementara. Coba lagi dalam {$minutes} menit.",
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials)) {
            RateLimiter::clear($throttleKey);
            RateLimiter::clear($lockoutKey);
            $request->session()->regenerate();
            $user = Auth::user();

            // 🔍 Pastikan role_id terhubung dengan tabel roles
            if (!$user->role_id) {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun ini belum memiliki role.']);
            }

            // Pesan sukses global pakai nama user
            $successMessage = 'Login berhasil, selamat datang ' . $user->name . '!';

            switch ($user->role_id) {
        case 1: // Admin
            return redirect()
                ->route('surat.index')
                ->with('success', $successMessage);

        case 2: // Sekretariat
        case 3: // Caraka
        case 4: // User
        case 5: // Bagian TU
        case 6: // Kearsipan
            return redirect()
                ->route('halaman_muka')
                ->with('success', $successMessage);
                default:
                    Auth::logout();
                    return back()->withErrors(['email' => 'Role tidak dikenali.']);
            }
        }

        RateLimiter::hit($throttleKey, 600);

        if (RateLimiter::attempts($throttleKey) > 3) {
            RateLimiter::hit($lockoutKey, 600);
            RateLimiter::clear($throttleKey);

            return back()
            ->withErrors(['email' => 'Terlalu banyak percobaan login. Akun ditangguhkan selama 10 menit.'])
            ->onlyInput('email');
        }

        return back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->onlyInput('email');
    }

    // 🔹 Tampilkan halaman register
    public function showRegister()
    {
        return view('auth.register');
    }

    // 🔹 Proses register
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'string',
                'max:20',
                Password::min(8)->letters()->numbers()->symbols()->mixedCase(),
            ],
        ], [
            'name.required'     => 'Nama wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email sudah terdaftar.',

            'password.required' => 'Kata sandi wajib diisi.',
            'password.min'      => 'Kata sandi minimal harus terdiri dari :min karakter.',
            'password.max'      => 'Kata sandi tidak boleh lebih dari 20 karakter.',
            'password.letters'  => 'Kata sandi harus mengandung setidaknya satu huruf.',
            'password.numbers'  => 'Kata sandi harus mengandung setidaknya satu angka.',
            'password.symbols'  => 'Kata sandi harus mengandung setidaknya satu simbol.',
            'password.mixed'    => 'Kata sandi harus mengandung huruf besar dan huruf kecil.',
        ]);

        // 🔸 Default role_id = 4 (User)
        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id'  => 4,
        ]);

        return redirect()
            ->route('register') // balik lagi ke halaman register (untuk menampilkan modal)
            ->with('register_success', true)
            ->with('success', 'Akun berhasil dibuat. Silakan login.');
    }

    // 🔹 Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Anda telah logout.');
    }

    // 🔹 Halaman pengaturan role (hanya admin)
    public function index()
    {
        $users = User::with('role')->get();
        $roles = Role::all();

        return view('dashboard.setting_role', compact('users', 'roles'));
    }

    // 🔹 Update role user
    public function update_role(Request $request, $id_users)
    {
        // (opsional) batasi hanya admin
        abort_unless(auth()->user()?->role_id === 1, 403);

        $request->validate([
            'role_id' => 'required|exists:roles,id_roles',
        ], [
            'role_id.required' => 'Silakan pilih role.',
            'role_id.exists'   => 'Role tidak valid.',
        ]);

        $user = User::findOrFail($id_users);
        $user->role_id = (int) $request->role_id;
        $user->save();

        return redirect()
            ->route('setting.role.index')
            ->with('success', 'Data pengguna diperbarui!');
    }

    // 🔹 Halaman pengaturan role (admin)
    public function setting_role()
    {
        abort_unless(auth()->user()?->role_id === 1, 403);

        $users = User::with('role')->get();
        $roles = Role::all();

        return view('dashboard.setting_role', compact('users', 'roles'));
    }

    // 🔹 Hapus user
    public function destroy($id)
    {
        abort_unless(auth()->user()?->role_id === 1, 403);

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()
            ->route('setting.role.index')
            ->with('success', 'Pengguna berhasil dihapus!');
    }
}