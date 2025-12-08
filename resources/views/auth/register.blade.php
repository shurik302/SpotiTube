{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.auth')

@section('title', 'Register · ' . config('app.name', 'SpotiTube'))

@section('card')
<section class="auth-section" aria-label="Create account form">
  <h1 class="auth-title">SPOTITUBE</h1>
  <p class="auth-subtitle">Create your account and start listening.</p>

  @if ($errors->any())
  <div class="auth-alert" role="alert">
    {{ $errors->first() }}
  </div>
  @endif

  <form method="POST" action="{{ route('register.store') }}" class="auth-form">
    @csrf
    <div class="auth-field">
      <label class="auth-label" for="register-name">Username</label>
      <input class="auth-input" id="register-name" name="name" type="text" placeholder="Your username"
        value="{{ old('name') }}" required autocomplete="name">
    </div>

    <div class="auth-field">
      <label class="auth-label" for="register-email">Email</label>
      <input class="auth-input" id="register-email" name="email" type="email" placeholder="your@email.com"
        value="{{ old('email') }}" required autocomplete="email">
    </div>

    <div class="auth-field">
      <label class="auth-label" for="register-password">Password</label>
      <input class="auth-input" id="register-password" name="password" type="password" placeholder="Min. 8 characters" required
        autocomplete="new-password">
    </div>

    <div class="auth-field">
      <label class="auth-label" for="register-password-confirm">Confirm Password</label>
      <input class="auth-input" id="register-password-confirm" name="password_confirmation" type="password"
        placeholder="Repeat your password" required autocomplete="new-password">
    </div>

    <button type="submit" class="auth-button auth-button-primary">Create Account</button>
  </form>

  <div class="auth-divider">
    <span>or</span>
  </div>

  <a class="auth-button auth-button-google" href="{{ route('auth.google.redirect') }}">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
      <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
      <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
      <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
      <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
    </svg>
    Continue with Google
  </a>

  <p class="auth-footer">
    Already have an account? <a href="{{ route('login') }}">Sign in</a>
  </p>
</section>
@endsection
