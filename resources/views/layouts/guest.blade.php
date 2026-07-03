<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SIAPMAN') }} - Login</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                font-family: 'Outfit', sans-serif;
                background-color: #f8fafc;
            }
            .mesh-bg {
                background-color: #ffffff;
                background-image: 
                    radial-gradient(at 100% 0%, hsla(217, 100%, 75%, 0.3) 0px, transparent 50%),
                    radial-gradient(at 0% 100%, hsla(228, 100%, 74%, 0.3) 0px, transparent 50%),
                    radial-gradient(at 50% 50%, hsla(211, 100%, 82%, 0.3) 0px, transparent 50%);
            }
            .premium-card {
                background: rgba(255, 255, 255, 0.85);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.4);
                box-shadow: 
                    0 20px 40px -10px rgba(30, 58, 138, 0.1),
                    0 0 0 1px rgba(255, 255, 255, 0.5) inset;
            }
            .animate-float {
                animation: float 6s ease-in-out infinite;
            }
            @keyframes float {
                0% { transform: translateY(0px); }
                50% { transform: translateY(-10px); }
                100% { transform: translateY(0px); }
            }
            .input-modern {
                background: #f1f5f9;
                border: 2px solid transparent;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .input-modern:focus {
                background: #ffffff;
                border-color: #3b82f6;
                box-shadow: 0 4px 20px -5px rgba(59, 130, 246, 0.15);
                transform: translateY(-1px);
            }
        </style>
    </head>
    <body class="antialiased min-h-screen flex items-center justify-center relative overflow-hidden mesh-bg">
        
        <!-- Animated Background Blobs -->
        <div class="absolute top-[-20%] left-[-10%] w-[500px] h-[500px] bg-blue-400/20 rounded-full mix-blend-multiply filter blur-[80px] animate-pulse" style="animation-duration: 7s;"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[600px] h-[600px] bg-indigo-400/20 rounded-full mix-blend-multiply filter blur-[100px] animate-pulse" style="animation-duration: 9s; animation-delay: 2s;"></div>

        <div class="w-full max-w-[440px] px-6 py-12 relative z-10">
            
            <div class="animate-float">
                <!-- Branding -->
                <div class="flex flex-col items-center justify-center mb-10">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-[24px] flex items-center justify-center shadow-xl shadow-blue-900/20 mb-5 transform hover:scale-105 hover:rotate-3 transition-all duration-300">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h1 class="text-4xl font-black tracking-tight text-slate-800">SIAPMAN</h1>
                    <p class="text-blue-600 text-sm mt-1.5 font-bold tracking-wide uppercase">Sistem Absensi Mandiri</p>
                </div>

                <!-- Card -->
                <div class="premium-card rounded-[32px] p-8 sm:p-10">
                    {{ $slot }}
                </div>
                
                <p class="text-center text-slate-400 text-xs mt-8 font-semibold uppercase tracking-wider">
                    &copy; {{ date('Y') }} Dinas Kominfo. All rights reserved.
                </p>
            </div>
        </div>
    </body>
</html>
