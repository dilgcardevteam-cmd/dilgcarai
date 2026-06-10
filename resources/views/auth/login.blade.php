<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NoteGov AI DILG - Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html, body {
            height: 100%;
        }

        body {
            margin: 0;
            overflow-x: hidden;
            background: #f7f8fc;
            font-family: 'Inter', sans-serif;
        }

        .hero-dots {
            background-image: radial-gradient(circle, rgba(15, 74, 204, 0.98) 0 2.25px, transparent 2.5px);
            background-size: 14px 14px;
        }

        .hero-dots-fade {
            background-image: radial-gradient(circle, rgba(15, 74, 204, 0.8) 0 2px, transparent 2.25px);
            background-size: 14px 14px;
        }

        .hero-diagonals {
            background-image:
                linear-gradient(45deg, transparent 47.5%, rgba(20, 35, 80, 0.035) 48.2%, rgba(20, 35, 80, 0.035) 51.8%, transparent 52.5%),
                linear-gradient(-45deg, transparent 47.5%, rgba(20, 35, 80, 0.025) 48.2%, rgba(20, 35, 80, 0.025) 51.8%, transparent 52.5%);
        }

        .hero-shadow {
            box-shadow: 0 30px 90px rgba(15, 23, 42, 0.06);
        }

        .card-shadow {
            box-shadow: 0 26px 70px rgba(0, 0, 0, 0.22);
        }
    </style>
</head>
<body class="overflow-x-hidden lg:overflow-hidden antialiased text-[#172554]">
    <main class="grid min-h-screen lg:h-screen lg:grid-cols-[1.08fr_.92fr]">
        <section class="relative flex items-center justify-center overflow-hidden bg-[#f7f8fc] px-5 py-6 sm:px-8 lg:px-10 lg:py-6">
            <div class="pointer-events-none absolute inset-0 hero-diagonals opacity-100"></div>
            <div class="pointer-events-none absolute left-0 top-0 h-52 w-52 hero-dots opacity-95"></div>
            <div class="pointer-events-none absolute bottom-0 left-0 h-52 w-52 hero-dots-fade opacity-90"></div>
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_22%,rgba(255,255,255,0.96),transparent_33%),radial-gradient(circle_at_50%_85%,rgba(11,75,179,0.05),transparent_20%)]"></div>

            <div class="relative z-10 flex w-full max-w-[540px] flex-col items-center text-center">
                <div class="mb-4 flex flex-col items-center gap-2">
                    <img src="{{ asset('images/dilg-logo.png') }}" alt="DILG logo" class="h-[58px] w-[58px] rounded-full object-contain">
                    <div class="text-[8px] font-extrabold uppercase tracking-[0.4em] text-[#1d4ed8] sm:text-[9px]">Government AI Platform</div>
                    <div class="text-[18px] font-extrabold leading-none tracking-[-0.03em] text-[#12224f] sm:text-[21px] lg:text-[24px]">DILG • NoteGov AI</div>
                </div>

                <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-[#c6d3f3] bg-white/70 px-4 py-2 text-[8px] font-extrabold uppercase tracking-[0.18em] text-[#1d4ed8] shadow-[0_10px_25px_rgba(15,23,42,0.04)] backdrop-blur-sm sm:text-[9px]">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <path d="M3 7l9-4 9 4-9 4-9-4Z"></path>
                        <path d="M3 12l9 4 9-4"></path>
                        <path d="M3 17l9 4 9-4"></path>
                    </svg>
                    AI-powered governance
                </div>

                <h1 class="max-w-[520px] text-[18px] font-extrabold leading-[1.05] tracking-[-0.04em] text-[#17306b] sm:text-[22px] lg:text-[24px]">
                    Transform government operations with
                    <span class="text-[#1d4ed8]">AI-powered intelligence</span>
                </h1>

                <p class="mt-3 max-w-[520px] text-[9px] leading-[1.5] text-slate-500 sm:text-[10px] lg:text-[11px]">
                    NoteGov AI delivers enterprise-grade policy intelligence, document analysis, and collaborative workflows designed exclusively for Philippine government institutions. Built with the latest AI technology to drive smarter, evidence-based governance.
                </p>

                <div class="mt-5 grid w-full max-w-[520px] grid-cols-2 gap-x-3 gap-y-3 sm:grid-cols-4">
                    <div class="flex flex-col items-center gap-2 text-center">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl border border-[#d8e2fb] bg-white text-[#1d4ed8] shadow-[0_8px_20px_rgba(15,23,42,0.04)] sm:h-10 sm:w-10">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                <path d="M12 15v2"></path>
                                <path d="M6 11V7a6 6 0 1 1 12 0v4"></path>
                                <rect x="4" y="11" width="16" height="10" rx="2"></rect>
                            </svg>
                        </div>
                        <div class="text-[10px] font-extrabold text-[#1f2f63]">Secure</div>
                        <div class="text-[8px] leading-[1.35] text-slate-500">Enterprise-grade security</div>
                    </div>

                    <div class="flex flex-col items-center gap-2 text-center">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl border border-[#d8e2fb] bg-white text-[#1d4ed8] shadow-[0_8px_20px_rgba(15,23,42,0.04)] sm:h-10 sm:w-10">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                <path d="M9.663 17h4.673"></path>
                                <path d="M12 3v1"></path>
                                <path d="M7.05 7.05l-.7-.7"></path>
                                <path d="M18.65 7.05l.7-.7"></path>
                                <path d="M21 12h-1"></path>
                                <path d="M4 12H3"></path>
                                <path d="M12 7a5 5 0 0 0-3 9v2h6v-2a5 5 0 0 0-3-9Z"></path>
                            </svg>
                        </div>
                        <div class="text-[10px] font-extrabold text-[#1f2f63]">Intelligent</div>
                        <div class="text-[8px] leading-[1.35] text-slate-500">AI-powered policy analysis</div>
                    </div>

                    <div class="flex flex-col items-center gap-2 text-center">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl border border-[#d8e2fb] bg-white text-[#1d4ed8] shadow-[0_8px_20px_rgba(15,23,42,0.04)] sm:h-10 sm:w-10">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                <path d="M17 20h5v-2a3 3 0 0 0-5.5-1.7"></path>
                                <path d="M7 20H2v-2a3 3 0 0 1 5.5-1.7"></path>
                                <circle cx="12" cy="8" r="4"></circle>
                                <path d="M12 14a7 7 0 0 0-7 7"></path>
                            </svg>
                        </div>
                        <div class="text-[10px] font-extrabold text-[#1f2f63]">Collaborative</div>
                        <div class="text-[8px] leading-[1.35] text-slate-500">Built for government teams</div>
                    </div>

                    <div class="flex flex-col items-center gap-2 text-center">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl border border-[#d8e2fb] bg-white text-[#1d4ed8] shadow-[0_8px_20px_rgba(15,23,42,0.04)] sm:h-10 sm:w-10">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                <path d="M9 12l2 2 4-4"></path>
                                <path d="M12 2l7 4v6c0 5-3.5 9.5-7 10-3.5-.5-7-5-7-10V6l7-4Z"></path>
                            </svg>
                        </div>
                        <div class="text-[10px] font-extrabold text-[#1f2f63]">Compliant</div>
                        <div class="text-[8px] leading-[1.35] text-slate-500">PH Data Privacy Act compliant</div>
                    </div>
                </div>

                <div class="hero-shadow mt-5 flex w-full max-w-[350px] items-center gap-4 rounded-2xl bg-white/90 px-4 py-3 text-left backdrop-blur-sm">
                    <div class="flex h-8 w-8 flex-none items-center justify-center rounded-xl bg-[#eef3ff] text-[#1d4ed8]">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                            <path d="M9 12l2 2 4-4"></path>
                            <path d="M12 2l7 4v6c0 5-3.5 9.5-7 10-3.5-.5-7-5-7-10V6l7-4Z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-[10px] font-extrabold text-[#22386e] sm:text-[11px]">Trusted by Philippine Government Institutions</div>
                        <div class="text-[8px] text-slate-500 sm:text-[9px]">Secure. Compliant. Trusted.</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="flex items-center justify-center overflow-hidden bg-[#002c76] px-5 py-8 sm:px-8 lg:px-10 lg:py-6">
            <div class="w-full max-w-[410px] rounded-[28px] bg-white px-6 py-6 shadow-[0_30px_70px_rgba(0,0,0,0.22)] sm:px-7 sm:py-7">
                @if (session('status'))
                    <div class="mb-4 rounded-2xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="mb-3 flex justify-center">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-[#eef3ff] text-[#1d4ed8]">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6">
                            <path d="M20 21a8 8 0 1 0-16 0"></path>
                            <circle cx="12" cy="8" r="4"></circle>
                        </svg>
                    </div>
                </div>

                <h1 class="text-center text-[22px] font-extrabold tracking-[-0.03em] text-[#16265a]">Welcome back</h1>
                <p class="mt-1 text-center text-[11px] text-slate-500">Sign in to continue to NoteGov AI DILG</p>

                <form method="POST" action="{{ route('login') }}" class="mt-4">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="mb-2 block text-[11px] font-extrabold text-[#1b2b59] sm:text-[12px]">Work Email <span class="text-orange-500">*</span></label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                    <path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"></path>
                                    <path d="m22 6-10 7L2 6"></path>
                                </svg>
                            </span>
                            <input
                                id="email"
                                class="h-[42px] w-full rounded-2xl border border-[#d5dff1] bg-[#eef4ff] pl-12 pr-4 text-[13px] text-[#203150] outline-none transition focus:border-blue-400 focus:bg-white focus:ring-4 focus:ring-blue-100"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="sorianorahm@gmail.com"
                            >
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="mb-2 block text-[11px] font-extrabold text-[#1b2b59] sm:text-[12px]">Password <span class="text-orange-500">*</span></label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                    <rect x="3" y="11" width="18" height="10" rx="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                            </span>
                            <input
                                id="password"
                                class="h-[42px] w-full rounded-2xl border border-[#d5dff1] bg-[#eef4ff] pl-12 pr-12 text-[13px] text-[#203150] outline-none transition focus:border-blue-400 focus:bg-white focus:ring-4 focus:ring-blue-100"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="Enter your password"
                            >
                            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-xl p-2 text-slate-400 transition hover:bg-blue-50 hover:text-[#1d4ed8]" id="passwordToggle" aria-label="Show password">
                                <svg id="eyeOpen" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                    <path d="M2.5 12s3.8-7 9.5-7 9.5 7 9.5 7-3.8 7-9.5 7-9.5-7-9.5-7Z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <svg id="eyeClosed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hidden h-4 w-4">
                                    <path d="M3 3l18 18"></path>
                                    <path d="M10.6 10.6a3 3 0 0 0 4.24 4.24"></path>
                                    <path d="M9.88 5.09A10.43 10.43 0 0 1 12 5c5.7 0 9.5 7 9.5 7a17.91 17.91 0 0 1-4.2 4.92"></path>
                                    <path d="M6.1 6.1C3.2 8.2 2.5 12 2.5 12s3.8 7 9.5 7c1 0 1.96-.14 2.86-.39"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3 flex items-center justify-between gap-3">
                        <label for="remember" class="flex items-center gap-2 text-[11px] text-slate-500 sm:text-[12px]">
                            <input id="remember" type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-[#1d4ed8] focus:ring-[#1d4ed8]">
                            <span>Remember me</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-[11px] font-extrabold text-[#1d4ed8] hover:underline sm:text-[12px]">Forgot your password?</a>
                        @endif
                    </div>

                    <button type="submit" class="flex h-[42px] w-full items-center justify-center gap-3 rounded-2xl bg-gradient-to-b from-[#1254c8] to-[#0a3ea0] text-[13px] font-extrabold text-white shadow-[0_20px_36px_rgba(10,62,160,0.32)] transition hover:-translate-y-[1px] hover:brightness-105">
                        <span>Log in</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                            <path d="M5 12h14"></path>
                            <path d="m13 6 6 6-6 6"></path>
                        </svg>
                    </button>
                </form>

                <p class="mt-3 text-center text-[10px] text-slate-500 sm:text-[11px]">
                    Don't have an account? <a href="{{ route('register') }}" class="font-extrabold text-[#1d4ed8] hover:underline">Register</a>
                </p>

                <div class="my-4 flex items-center gap-3 text-[11px] font-extrabold uppercase tracking-[0.16em] text-slate-400">
                    <span class="h-px flex-1 bg-slate-200"></span>
                    <span>Or continue with</span>
                    <span class="h-px flex-1 bg-slate-200"></span>
                </div>

                <a href="{{ route('auth.google') }}" class="flex h-12 w-full items-center justify-center gap-3 rounded-2xl border border-slate-200 bg-white text-[14px] font-extrabold text-[#223056] transition hover:-translate-y-[1px] hover:border-slate-300 hover:shadow-[0_10px_24px_rgba(16,36,87,0.08)]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" aria-hidden="true">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <span>Continue with Google</span>
                </a>    
            </div>
        </section>
    </main>

    <script>
        const passwordToggle = document.getElementById('passwordToggle');
        const passwordInput = document.getElementById('password');
        const eyeOpen = document.getElementById('eyeOpen');
        const eyeClosed = document.getElementById('eyeClosed');

        passwordToggle.addEventListener('click', function () {
            const hidden = passwordInput.type === 'password';
            passwordInput.type = hidden ? 'text' : 'password';
            eyeOpen.classList.toggle('hidden', hidden);
            eyeClosed.classList.toggle('hidden', !hidden);
            passwordToggle.setAttribute('aria-label', hidden ? 'Hide password' : 'Show password');
        });
    </script>
</body>
</html>
