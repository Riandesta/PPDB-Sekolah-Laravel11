<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Panitia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class PanitiaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Remove the checkRole line
        $list_panitia = Panitia::with('user')->get();
        return view('panitia.index', ['list_panitia' => $list_panitia]);
    }

    public function create()
    {
        $objPanitia = new Panitia();
        return view('panitia.form', ['panitia' => $objPanitia]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'jabatan' => 'required',
            'unit' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required',
            'email' => 'required|email|unique:panitias,email', // Hanya validasi pada tabel panitias
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'username' => 'required|unique:users,username',
            'password' => 'required|min:6',
        ]);

        $data = $request->all();

        // Handle foto upload
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('public/foto_panitia');
            $data['foto'] = Storage::url($path);
        }

        DB::beginTransaction();
        try {
            // Create user account (tanpa email)
            $user = User::create([
                'nama' => $data['nama'],
                'username' => $data['username'],
                'password' => Hash::make($data['password']),
                'role' => 'panitia'
            ]);

            // Create panitia
            $panitia = Panitia::create([
                'nama' => $data['nama'],
                'jabatan' => $data['jabatan'],
                'unit' => $data['unit'],
                'alamat' => $data['alamat'],
                'no_hp' => $data['no_hp'],
                'email' => $data['email'], // Simpan email di tabel panitias
                'foto' => $data['foto'] ?? null,
                'user_id' => $user->id,
                'username' => $data['username'],
                'password' => bcrypt($data['password']),
            ]);

            $message = "Data panitia berhasil ditambahkan.\nInformasi akun:\nUsername: {$data['username']}\nPassword: {$data['password']}";

            DB::commit();
            return redirect()->route('panitia.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            // Controller
            session()->flash('success', 'Data berhasil disimpan.');
            return redirect()->route('panitia.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function update(Request $request, $id)
    {
        $panitia = Panitia::findOrFail($id);

        $rules = [
            'nama' => 'required',
            'jabatan' => 'required',
            'unit' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required',
            'email' => 'required|email|unique:panitias,email,' . $panitia->id,
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'username' => 'required|unique:users,username,' . $panitia->user_id,
        ];


        // Only validate password if it's provided
        if ($request->filled('password')) {
            $rules['password'] = 'min:6';
        }

        $request->validate($rules);

        DB::beginTransaction();
        try {
            $data = $request->all();

            // Handle foto upload
            if ($request->hasFile('foto')) {
                $path = $request->file('foto')->store('public/foto_panitia');
                $data['foto'] = Storage::url($path);
            }

            // Update panitia
            $panitia->update([
                'nama' => $data['nama'],
                'jabatan' => $data['jabatan'],
                'unit' => $data['unit'],
                'alamat' => $data['alamat'],
                'no_hp' => $data['no_hp'],
                'email' => $data['email'],
                'foto' => $data['foto'] ?? $panitia->foto,
                'username' => $data['username'],
            ]);

            // Update user if password is provided
            $updateData = [
                'nama' => $data['nama'],
                'username' => $data['username'],
            ];

            if ($request->filled('password')) {
                $updateData['password'] = bcrypt($data['password']);
            }

            $panitia->user->update($updateData);

            DB::commit();
            return redirect()->route('panitia.index')
                ->with('success', 'Data panitia berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollback();
            // Controller
            session()->flash('success', 'Data berhasil disimpan.');
            return redirect()->route('panitia.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function edit($id)
    {
        $objPanitia = Panitia::with('user')->findOrFail($id);
        return view('panitia.form', ['panitia' => $objPanitia]);
    }

    public function destroy($id): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $panitia = Panitia::with('user')->find($id);

            if ($panitia) {
                // Delete user account if exists
                if ($panitia->user) {
                    $panitia->user->delete();
                }

                // Delete panitia record
                $panitia->delete();

                DB::commit();
                return redirect()->route('panitia.index')
                    ->with('success', 'Data panitia berhasil dihapus');
            }

            DB::rollback();
            return redirect()->route('panitia.index')
                ->with('error', 'Data tidak ditemukan');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('panitia.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
