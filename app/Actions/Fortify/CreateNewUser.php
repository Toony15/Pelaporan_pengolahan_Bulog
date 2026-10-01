<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, mixed>  $input
     */
    public function create(array $input): User
    {
        // Username selalu disimpan huruf kecil (Fortify juga melowercase saat login).
        $input['username'] = is_string($input['username'] ?? null) ? Str::lower(trim($input['username'])) : '';
        $input['phone'] = is_string($input['phone'] ?? null) ? preg_replace('/[\s\-]/', '', $input['phone']) : '';

        Validator::make($input, [
            'email' => $this->emailRules(),
            'phone' => ['required', 'string', 'regex:/^(\+62|62|0)8\d{7,12}$/'],
            'username' => ['required', 'string', 'min:4', 'max:30', 'alpha_dash:ascii', Rule::unique(User::class)],
            'password' => ['required', 'string', Password::default()],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'phone.required' => 'No HP wajib diisi.',
            'phone.regex' => 'No HP tidak valid. Contoh: 081234567890.',
            'username.required' => 'Username wajib diisi.',
            'username.min' => 'Username minimal 4 karakter.',
            'username.max' => 'Username maksimal 30 karakter.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, garis bawah, dan strip.',
            'username.unique' => 'Username sudah dipakai.',
            'password.required' => 'Password wajib diisi.',
        ])->validate();

        return User::create([
            'name' => $input['username'],
            'username' => $input['username'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'password' => $input['password'],
        ]);
    }
}
