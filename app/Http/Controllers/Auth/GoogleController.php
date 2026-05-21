<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        try {
            Log::info('Google OAuth redirect initiated');
            return Socialite::driver('google')->redirect();
        } catch (\Exception $e) {
            Log::error('Google OAuth redirect failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('login')->withErrors([
                'email' => 'Failed to redirect to Google. Please try again.',
            ]);
        }
    }

    public function handleGoogleCallback()
    {
        Log::info('Google OAuth callback received');

        try {
            $googleUser = Socialite::driver('google')->user();
            Log::info('Google user retrieved', [
                'id' => $googleUser->id,
                'email' => $googleUser->email,
                'name' => $googleUser->name,
            ]);
        } catch (\Exception $e) {
            Log::error('Google OAuth user retrieval failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('login')->withErrors([
                'email' => 'Google authentication failed: ' . $e->getMessage(),
            ]);
        }

        try {
            $user = User::where('google_id', $googleUser->id)->first();
            Log::info('User search by google_id', ['found' => (bool)$user]);

            if (!$user) {
                $user = User::where('email', $googleUser->email)->first();
                Log::info('User search by email', ['found' => (bool)$user]);

                if ($user) {
                    $user->update([
                        'google_id' => $googleUser->id,
                    ]);
                    Log::info('Updated existing user with google_id', ['user_id' => $user->id]);
                } else {
                    $user = User::create([
                        'name' => $googleUser->name,
                        'email' => $googleUser->email,
                        'google_id' => $googleUser->id,
                        'password' => Hash::make(Str::random(24)),
                        'role' => User::ROLE_USER,
                    ]);
                    Log::info('Created new user via Google', ['user_id' => $user->id]);
                }
            }

            Auth::login($user, true);
            Log::info('User logged in', ['user_id' => $user->id]);

            $redirectTo = $user->isAdmin() ? route('dashboard', absolute: false) : route('notebooks.index', absolute: false);
            Log::info('Redirecting user', ['url' => $redirectTo]);

            return redirect($redirectTo);
        } catch (\Exception $e) {
            Log::error('User login/creation failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('login')->withErrors([
                'email' => 'Failed to login/create user: ' . $e->getMessage(),
            ]);
        }
    }
}
