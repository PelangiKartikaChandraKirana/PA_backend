<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-bold text-slate-700 mb-2">{{ __('Alamat Email') }}</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-blue-500 text-slate-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                </div>
                <input id="email" class="input-modern block w-full pl-11 pr-4 py-3.5 rounded-2xl text-sm font-medium text-slate-800 focus:outline-none placeholder-slate-400" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="email@instansi.go.id" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-rose-500 font-medium text-xs" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <label for="password" class="block text-sm font-bold text-slate-700">{{ __('Kata Sandi') }}</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs font-bold text-blue-600 hover:text-indigo-600 transition-colors">
                        {{ __('Lupa sandi?') }}
                    </a>
                @endif
            </div>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-blue-500 text-slate-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <input id="password" class="input-modern block w-full pl-11 pr-4 py-3.5 rounded-2xl text-sm font-medium text-slate-800 focus:outline-none placeholder-slate-400" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-rose-500 font-medium text-xs" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input id="remember_me" type="checkbox" class="h-4.5 w-4.5 rounded-[6px] border-slate-300 text-blue-600 focus:ring-blue-500 bg-slate-50 cursor-pointer" name="remember">
            <label for="remember_me" class="ml-2.5 block text-sm text-slate-600 font-semibold cursor-pointer select-none">
                {{ __('Ingat sesi saya') }}
            </label>
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full flex justify-center py-3.5 px-4 rounded-2xl shadow-lg shadow-blue-500/30 text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 focus:outline-none focus:ring-4 focus:ring-blue-500/20 transform hover:-translate-y-1 transition-all duration-300">
                {{ __('Masuk') }}
            </button>
        </div>
    </form>
</x-guest-layout>
