<x-guest-layout>
    <div class="login-title">Sign in to your account</div>

    @if (session('status'))
        <div class="session-status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="you@example.com">
            @error('email') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input id="password" class="form-input" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
            @error('password') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="remember-row">
            <input id="remember_me" type="checkbox" name="remember">
            <label for="remember_me">Remember me</label>
        </div>

        <button type="submit" class="login-btn">Sign In</button>

        @if (Route::has('password.request'))
            <a class="forgot-link" href="{{ route('password.request') }}">Forgot your password?</a>
        @endif
    </form>
</x-guest-layout>
