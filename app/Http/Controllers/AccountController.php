<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function edit(): View
    {
        return view('settings.account');
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $validated = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($request->user()->id),
            ],
        ], [
            'email.unique' => 'Email ini sudah dipakai akun lain.',
            'email.email' => 'Format email tidak valid.',
        ])->validateWithBag('profile');

        $request->user()->update($validated);

        return redirect()
            ->route('account.edit')
            ->with('status', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = Validator::make($request->all(), [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.current_password' => 'Kata sandi saat ini salah.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
        ])->validateWithBag('password');

        $request->user()->update(['password' => $validated['password']]);

        $request->session()->regenerate();

        return redirect()
            ->route('account.edit')
            ->with('status', 'Kata sandi berhasil diubah.');
    }
}
