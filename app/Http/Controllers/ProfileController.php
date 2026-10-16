<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Tampilkan form pengaturan profil pengguna.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Perbarui data profil dan upload foto avatar pengguna.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'   => ['required', 'string', 'max:100'],
            'email'  => ['required', 'email', 'max:150', Rule::unique('users')->ignore($user->id)],
            'role'   => ['nullable', 'string', 'max:100'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'name.required'   => 'Nama lengkap wajib diisi.',
            'email.required'  => 'Alamat email wajib diisi.',
            'email.unique'    => 'Alamat email sudah digunakan oleh pengguna lain.',
            'avatar.image'    => 'File avatar harus berupa gambar.',
            'avatar.mimes'    => 'Format gambar yang didukung: JPG, PNG, WEBP.',
            'avatar.max'      => 'Ukuran gambar maksimal adalah 2 MB.',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (isset($validated['role'])) {
            $user->role = $validated['role'];
        }

        // Proses Upload Foto Avatar Baru
        if ($request->hasFile('avatar')) {
            // Hapus file lama jika ada di storage
            if (!empty($user->avatar) && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Simpan foto baru ke direktori storage/app/public/avatars
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return redirect()->route('profile.edit')
            ->with('success', 'Profil dan foto avatar berhasil diperbarui!');
    }

    /**
     * Perbarui kata sandi akun pengguna.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password'         => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.required'         => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini tidak cocok dengan data akun Anda.',
            'password.required'                 => 'Kata sandi baru wajib diisi.',
            'password.min'                      => 'Kata sandi baru minimal harus 6 karakter.',
            'password.confirmed'                => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()->route('profile.edit')
            ->with('success_password', 'Kata sandi akun Anda berhasil diperbarui!');
    }
}
