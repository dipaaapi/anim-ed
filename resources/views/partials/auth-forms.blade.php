@php
    // Reopen on the register tab if the registration form had errors
    $authTab = old('form') === 'register' ? 'register' : 'login';
    $inputClass = 'bg-gray-900/60 border border-gray-800/80 rounded-xl px-4 py-2.5 w-full text-white placeholder-gray-500 text-sm focus:border-indigo-500/50 outline-none focus:ring-0 transition-all';
    $labelClass = 'block text-xs font-bold text-gray-400 mb-1.5';
@endphp

<!-- Tabs -->
<div class="relative grid grid-cols-2 gap-1 bg-gray-900/60 border border-gray-800/80 rounded-xl p-1 mb-6 text-sm font-bold">
    <button type="button" data-auth-tab="login" onclick="switchAuthTab('login')" class="py-2 rounded-lg transition-all">Sign In</button>
    <button type="button" data-auth-tab="register" onclick="switchAuthTab('register')" class="py-2 rounded-lg transition-all">Create Account</button>
</div>

@if ($errors->any() && old('form'))
    <div class="relative mb-4 bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs font-medium p-3 rounded-xl space-y-1">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<!-- Login -->
<form id="auth-panel-login" method="POST" action="{{ route('login.attempt') }}" class="relative space-y-4 {{ $authTab === 'login' ? '' : 'hidden' }}">
    @csrf
    <input type="hidden" name="form" value="login">
    <div>
        <label for="login-email" class="{{ $labelClass }}">Email</label>
        <input id="login-email" type="email" name="email" value="{{ old('form') === 'login' ? old('email') : '' }}" required autocomplete="email" class="{{ $inputClass }}" placeholder="you@example.com">
    </div>
    <div>
        <label for="login-password" class="{{ $labelClass }}">Password</label>
        <input id="login-password" type="password" name="password" required autocomplete="current-password" class="{{ $inputClass }}" placeholder="••••••••">
    </div>
    <label class="flex items-center gap-2 text-xs text-gray-400 cursor-pointer">
        <input type="checkbox" name="remember" value="1" class="rounded border-gray-700 bg-gray-900 text-indigo-600 focus:ring-0">
        Remember me
    </label>
    <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold py-3 rounded-xl shadow-lg shadow-indigo-600/20 transition-all active:scale-[0.98]">
        Sign In
    </button>
</form>

<!-- Register -->
<form id="auth-panel-register" method="POST" action="{{ route('register') }}" class="relative space-y-4 {{ $authTab === 'register' ? '' : 'hidden' }}">
    @csrf
    <input type="hidden" name="form" value="register">
    <div>
        <label for="register-name" class="{{ $labelClass }}">Display Name</label>
        <input id="register-name" type="text" name="name" value="{{ old('form') === 'register' ? old('name') : '' }}" required maxlength="255" autocomplete="name" class="{{ $inputClass }}" placeholder="Your name">
    </div>
    <div>
        <label for="register-email" class="{{ $labelClass }}">Email</label>
        <input id="register-email" type="email" name="email" value="{{ old('form') === 'register' ? old('email') : '' }}" required autocomplete="email" class="{{ $inputClass }}" placeholder="you@example.com">
    </div>
    <div>
        <label for="register-password" class="{{ $labelClass }}">Password</label>
        <input id="register-password" type="password" name="password" required minlength="8" autocomplete="new-password" class="{{ $inputClass }}" placeholder="At least 8 characters">
    </div>
    <div>
        <label for="register-password-confirmation" class="{{ $labelClass }}">Confirm Password</label>
        <input id="register-password-confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password" class="{{ $inputClass }}" placeholder="Repeat password">
    </div>
    <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold py-3 rounded-xl shadow-lg shadow-indigo-600/20 transition-all active:scale-[0.98]">
        Create Account
    </button>
</form>

<script>
    function switchAuthTab(tab) {
        ['login', 'register'].forEach(function (name) {
            document.getElementById('auth-panel-' + name).classList.toggle('hidden', name !== tab);
            const btn = document.querySelector('[data-auth-tab="' + name + '"]');
            btn.classList.toggle('bg-indigo-600', name === tab);
            btn.classList.toggle('text-white', name === tab);
            btn.classList.toggle('text-gray-400', name !== tab);
        });
    }
    switchAuthTab(@json($authTab));
</script>
