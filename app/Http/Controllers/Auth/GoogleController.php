<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleController extends Controller
{
    /**
     * Redirect the user to Google's OAuth screen.
     */
    public function redirect(): RedirectResponse
    {
        return $this->driver()
            ->scopes(['openid', 'profile', 'email'])
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    /**
     * Handle the callback from Google.
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = $this->driver()->stateless()->user();
        } catch (Throwable $exception) {
            Log::warning('Google OAuth failed', ['error' => $exception->getMessage()]);

            return redirect()->route('login')->with('error', 'Unable to authenticate with Google.');
        }

        $user = User::query()
            ->where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if (! $user) {
            $user = User::create([
                'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'Unknown DJ',
                'email' => $googleUser->getEmail(),
                'password' => Str::random(32),
                'password_set' => false,
            ]);
        }

        $user->forceFill([
            'google_id' => $googleUser->getId(),
            'google_avatar' => $googleUser->getAvatar(),
            'google_token' => $googleUser->token ?? null,
            'google_refresh_token' => $googleUser->refreshToken ?? null,
        ])->save();

        Auth::login($user, true);

        return redirect()->intended(route('home'));
    }

    protected function driver()
    {
        return Socialite::driver('google')
            ->redirectUrl(config('services.google.redirect'));
    }
}
