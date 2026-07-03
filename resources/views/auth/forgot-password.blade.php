<x-guest-layout>
    <div class="mb-6 text-sm text-slate-500 font-medium leading-relaxed text-center px-4">
        {{ __('Lupa kata sandi? Tidak masalah. Masukkan alamat email Anda di bawah ini dan kami akan mengirimkan tautan untuk mereset kata sandi Anda.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-bold text-slate-700 mb-2">{{ __('Alamat Email') }}</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-blue-500 text-slate-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <input id="email" class="input-modern block w-full pl-11 pr-4 py-3.5 rounded-2xl text-sm font-medium text-slate-800 focus:outline-none placeholder-slate-400" type="email" name="email" :value="old('email')" required autofocus placeholder="Masukkan email Anda" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-rose-500 font-medium text-xs" />
        </div>

        <div class="pt-4 flex flex-col space-y-3">
            <button type="submit" class="w-full flex justify-center py-3.5 px-4 rounded-2xl shadow-lg shadow-blue-500/30 text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 focus:outline-none focus:ring-4 focus:ring-blue-500/20 transform hover:-translate-y-1 transition-all duration-300">
                {{ __('Kirim Tautan Reset Sandi') }}
            </button>
            <a href="{{ route('login') }}" class="w-full flex justify-center py-3.5 px-4 border-2 border-slate-200 rounded-2xl shadow-sm text-sm font-bold text-slate-600 bg-white hover:bg-slate-50 hover:border-slate-300 focus:outline-none focus:ring-4 focus:ring-slate-200 transform hover:-translate-y-1 transition-all duration-300">
                {{ __('Kembali ke Login') }}
            </a>
        </div>
    </form>
</x-guest-layout>
