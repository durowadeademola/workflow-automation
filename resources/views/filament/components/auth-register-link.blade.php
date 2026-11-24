{{-- Under the login form (AUTH_LOGIN_FORM_AFTER in both panel providers). Sign-up is the marketing
     site's own Register page, not a Filament one, so this links out to it. --}}
<p class="bf-auth-register">
    Need an account?
    <a href="{{ route('register') }}" class="bf-auth-register-link">Register</a>
</p>
