<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

Artisan::command('admin:create', function () {
    $name = $this->ask('Administrator name');
    $email = $this->ask('Administrator email');
    if (! filter_var($email, FILTER_VALIDATE_EMAIL) || User::whereEmail($email)->exists()) {
        $this->error('Use a valid, unused email.');

        return 1;
    }
    $password = $this->secret('Password (at least 14 characters)');
    $confirm = $this->secret('Confirm password');
    if (strlen($password) < 14 || $password !== $confirm) {
        $this->error('Passwords must match and contain at least 14 characters.');

        return 1;
    }
    $user = new User;
    $user->name = $name;
    $user->email = $email;
    $user->password = Hash::make($password);
    $user->is_admin = true;
    $user->save();
    $this->info('Administrator created.');
})->purpose('Securely create an administrator using hidden password input');
