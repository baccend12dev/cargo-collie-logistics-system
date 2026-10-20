<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Pastikan hanya Super User yang dapat mengakses aksi ini.
     */
    protected function checkSuperUser(): void
    {
        if (!Auth::check() || !Auth::user()->isSuperUser()) {
            abort(403, 'Akses ditolak. Fitur Manajemen User hanya dapat diakses oleh Super User.');
        }
    }

    /**
     * Tambah akun pengguna baru ke sistem.
     */
    public function store(Request $request)
    {
        $this->checkSuperUser();

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:150', 'unique:users,email'],
            'role'     => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required'     => 'Nama lengkap pengguna wajib diisi.',
            'email.required'    => 'Alamat email wajib diisi.',
            'email.unique'      => 'Alamat email ini sudah terdaftar di sistem.',
            'role.required'     => 'Role / hak akses wajib ditentukan.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min'      => 'Kata sandi minimal 6 karakter.',
            'password.confirmed'=> 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $newUser = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'role'     => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.edit')
            ->with('success_user', "Pengguna baru \"{$newUser->name}\" ({$newUser->role}) berhasil ditambahkan!");
    }

    /**
     * Perbarui data akun pengguna (Nama, Email, Role, dan Password opsional).
     */
    public function update(Request $request, $id)
    {
        $this->checkSuperUser();

        $targetUser = User::findOrFail($id);

        $rules = [
            'name'  => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($targetUser->id)],
            'role'  => ['required', 'string', 'max:100'],
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['required', 'string', 'min:6', 'confirmed'];
        }

        $validated = $request->validate($rules, [
            'name.required'     => 'Nama lengkap pengguna wajib diisi.',
            'email.required'    => 'Alamat email wajib diisi.',
            'email.unique'      => 'Alamat email ini sudah digunakan pengguna lain.',
            'role.required'     => 'Role / hak akses wajib ditentukan.',
            'password.min'      => 'Kata sandi baru minimal 6 karakter.',
            'password.confirmed'=> 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $targetUser->name = $validated['name'];
        $targetUser->email = $validated['email'];
        $targetUser->role = $validated['role'];

        if ($request->filled('password')) {
            $targetUser->password = Hash::make($validated['password']);
        }

        $targetUser->save();

        return redirect()->route('profile.edit')
            ->with('success_user', "Data pengguna \"{$targetUser->name}\" berhasil diperbarui!");
    }

    /**
     * Hapus akun pengguna dari sistem.
     */
    public function destroy($id)
    {
        $this->checkSuperUser();

        // Cegah menghapus akun sendiri yang sedang login
        if ((int)$id === (int)Auth::id()) {
            return redirect()->route('profile.edit')
                ->with('error_user', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan.');
        }

        $targetUser = User::findOrFail($id);
        $name = $targetUser->name;
        $targetUser->delete();

        return redirect()->route('profile.edit')
            ->with('success_user', "Pengguna \"{$name}\" berhasil dihapus dari sistem.");
    }
}
