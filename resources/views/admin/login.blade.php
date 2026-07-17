<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Control Portal | anim-ed</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-[#0b0b0f] text-gray-200 min-h-screen flex items-center justify-center p-4 selection:bg-indigo-600 selection:text-white relative overflow-hidden">
    <!-- Glow -->
    <div class="absolute -top-32 -left-32 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-64 h-64 bg-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md bg-[#12121a] border border-gray-800/85 rounded-3xl p-8 shadow-2xl relative">
        
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white font-black text-2xl mx-auto shadow-lg shadow-indigo-600/20 mb-3">
                A
            </div>
            <h2 class="text-xl font-bold text-white tracking-tight">System Control Portal</h2>
            <p class="text-gray-500 text-xs mt-1 uppercase tracking-widest font-semibold">Authorized Admin Personnel Only</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 bg-rose-500/10 border border-rose-500/30 text-rose-400 p-4 rounded-xl text-xs">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="/{{ env('ADMIN_LOGIN_PATH', 'secret-portal-admin') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Admin Email</label>
                <input 
                    type="email" 
                    name="email" 
                    placeholder="admin@anim-ed.io"
                    required 
                    class="bg-gray-900 border border-gray-800 rounded-xl px-4 py-3 w-full text-white text-sm focus:border-indigo-500/50 outline-none focus:ring-0 transition-all"
                />
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Password</label>
                <input 
                    type="password" 
                    name="password" 
                    placeholder="••••••••"
                    required 
                    class="bg-gray-900 border border-gray-800 rounded-xl px-4 py-3 w-full text-white text-sm focus:border-indigo-500/50 outline-none focus:ring-0 transition-all"
                />
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-4 rounded-xl transition-all shadow-lg shadow-indigo-600/20 active:scale-[0.98] mt-6">
                Authenticate Session
            </button>
        </form>

        <div class="text-center mt-6">
            <a href="{{ route('home') }}" class="text-xs text-gray-600 hover:text-gray-500 transition-colors">Return to public site</a>
        </div>
    </div>
</body>
</html>
