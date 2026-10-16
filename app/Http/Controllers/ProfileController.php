<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
            $file = $request->file('avatar');
            if ($file->isValid()) {
                $targetDir = public_path('storage/avatars');
                if (!is_dir($targetDir)) {
                    @mkdir($targetDir, 0777, true);
                }

                // Hapus file lama jika ada
                if (!empty($user->avatar)) {
                    $cleanOld = ltrim(str_replace('storage/', '', $user->avatar), '/');
                    $oldPath = public_path('storage/' . $cleanOld);
                    if (is_file($oldPath)) {
                        @unlink($oldPath);
                    }
                    if (Storage::disk('public')->exists($cleanOld)) {
                        Storage::disk('public')->delete($cleanOld);
                    }
                }

                // Simpan foto baru ke public/storage/avatars
                $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $file->move($targetDir, $filename);

                $user->avatar = 'avatars/' . $filename;
            }
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
