<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Log in with email and password.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email', 'form'))
                ->withErrors(['email' => 'The provided credentials do not match our records.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'))->with('success', 'Welcome back, ' . Auth::user()->name . '!');
    }

    /**
     * Register a new account and log it in.
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=' . urlencode($data['name']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home')->with('success', 'Account created. Welcome to anim-ed, ' . $user->name . '!');
    }

    /**
     * Redirect to the provider login page.
     */
    public function redirectToProvider(string $provider)
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            abort(404);
        }

        // Mock mode for local testing if credentials are default
        if (config("services.{$provider}.client_id") === "{$provider}-client-id") {
            return view('auth.mock-login', ['provider' => $provider]);
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle provider callback.
     */
    public function handleProviderCallback(string $provider)
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            abort(404);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Authentication failed.');
        }

        $user = User::updateOrCreate([
            'email' => $socialUser->getEmail(),
        ], [
            'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'Social User',
            'avatar' => $socialUser->getAvatar(),
            $provider . '_id' => $socialUser->getId(),
        ]);

        Auth::login($user, true);

        return redirect()->route('home');
    }

    /**
     * Mock login for development and testing without real credentials.
     */
    public function handleMockLogin(Request $request, string $provider)
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            abort(404);
        }

        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
        ]);

        $user = User::updateOrCreate([
            'email' => $request->email,
        ], [
            'name' => $request->name,
            'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=' . urlencode($request->name),
            $provider . '_id' => 'mock_' . Str::random(10),
        ]);

        Auth::login($user, true);

        return redirect()->route('home')->with('success', 'Logged in via mock ' . ucfirst($provider));
    }

    /**
     * Log user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
