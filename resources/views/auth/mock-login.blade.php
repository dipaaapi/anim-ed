@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto my-12 bg-[#12121a] border border-gray-800/80 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
    <!-- Glow -->
    <div class="absolute -top-24 -right-24 w-48 h-48 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="text-center mb-8">
        <span class="bg-amber-500/15 text-amber-400 font-extrabold text-[10px] px-3 py-1 rounded-full uppercase tracking-wider mb-3 inline-block border border-amber-500/25">
            OAuth Sandbox Mode
        </span>
        <h2 class="text-2xl font-bold text-white">Mock {{ ucfirst($provider) }} Login</h2>
        <p class="text-gray-400 text-sm mt-1">Provide any email and name to simulate OAuth authentication.</p>
    </div>

    <form action="{{ route('auth.mock', ['provider' => $provider]) }}" method="POST" class="space-y-4">
        @csrf
        
        <div>
            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Full Name</label>
            <input 
                type="text" 
                name="name" 
                value="Otaku Developer"
                placeholder="e.g. John Doe" 
                required
                class="bg-gray-900 border border-gray-800 rounded-xl px-4 py-3 w-full text-white text-sm focus:border-indigo-500/50 outline-none focus:ring-0 transition-all"
            />
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Email Address</label>
            <input 
                type="email" 
                name="email" 
                value="developer@anim-ed.io"
                placeholder="e.g. johndoe@gmail.com" 
                required
                class="bg-gray-900 border border-gray-800 rounded-xl px-4 py-3 w-full text-white text-sm focus:border-indigo-500/50 outline-none focus:ring-0 transition-all"
            />
        </div>

        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-4 rounded-xl transition-all shadow-lg shadow-indigo-600/20 active:scale-[0.98] mt-6">
            Authorize & Sign In
        </button>
    </form>

    <div class="text-center mt-6">
        <a href="{{ route('home') }}" class="text-xs text-gray-500 hover:text-gray-400 transition-colors">Cancel and return home</a>
    </div>
</div>
@endsection
