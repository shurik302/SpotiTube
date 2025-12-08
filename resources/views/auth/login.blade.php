{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.auth')

@section('title', 'Sign in · ' . config('app.name', 'SpotiTube'))

@section('card')
<section class="auth-section" aria-label="Sign in form">
  <h1 class="auth-title">SPOTITUBE</h1>
  <p class="auth-subtitle">Welcome back! Sign in to continue.</p>

  @if ($errors->any())
  <div class="auth-alert" role="alert">
    {{ $errors->first() }}
  </div>
  @endif

  <form method="POST" action="{{ route('login.store') }}" class="auth-form">
    @csrf
    <div class="auth-field">
      <label class="auth-label" for="login-email">Email</label>
      <input class="auth-input" id="login-email" name="email" type="email" placeholder="your@email.com"
        value="{{ old('email') }}" required autocomplete="email">
    </div>

    <div class="auth-field">
      <label class="auth-label" for="login-password">Password</label>
      <input class="auth-input" id="login-password" name="password" type="password" placeholder="Enter your password" required
        autocomplete="current-password">
    </div>

    <div class="auth-checkbox">
      <input id="login-remember" name="remember" type="checkbox" value="1" {{ old('remember') ? 'checked' : '' }}>
      <label for="login-remember">Remember me</label>
    </div>

    <button type="submit" class="auth-button auth-button-primary">Sign In</button>
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
    Don't have an account? <a href="{{ route('register') }}">Create one</a>
  </p>
</section>
@endsection
