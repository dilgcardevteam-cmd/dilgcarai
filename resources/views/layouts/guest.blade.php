<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'NoteGov AI DILG') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|space-grotesk:400,500,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-10 sm:px-6">
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(56,189,248,0.16),transparent_34%),radial-gradient(circle_at_bottom_right,rgba(245,158,11,0.18),transparent_30%)]"></div>
                <div class="absolute inset-0 floating-grid opacity-30"></div>
            </div>

            <div class="relative grid w-full max-w-6xl overflow-hidden rounded-[32px] border border-white/10 bg-slate-950/70 shadow-2xl shadow-black/30 backdrop-blur-2xl lg:grid-cols-[1.1fr_.9fr]">
                <div class="hidden flex-col justify-between bg-[linear-gradient(145deg,rgba(13,26,46,0.96),rgba(7,17,31,0.94))] p-10 lg:flex">
                    <div>
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-4">
                            <x-application-logo class="h-14 w-14" />
                            <div>
                                <p class="text-xs uppercase tracking-[0.28em] text-sky-200/80">Government AI</p>
                                <p class="text-2xl font-bold text-white">NoteGov AI DILG</p>
                            </div>
                        </a>
                        <h1 class="mt-10 max-w-md text-4xl font-bold leading-tight text-white">An AI notebook for governance work, policy research, and operational clarity.</h1>
                        <p class="mt-5 max-w-lg text-sm leading-7 text-slate-300">Collect sources, ask grounded questions, generate policy briefs, and keep every notebook organized for DILG teams and local government workflows.</p>
                    </div>

                    <div class="panel space-y-4 p-6">
                        <div class="chip">NotebookLM-inspired</div>
                        <div class="grid gap-3">
                            <div class="panel-muted px-4 py-4">
                                <p class="text-xs uppercase tracking-[0.22em] text-slate-500">Smart Workflow</p>
                                <p class="mt-2 text-sm text-slate-200">Upload PDFs, websites, DOCX, text, audio, and video for a single searchable AI workspace.</p>
                            </div>
                            <div class="panel-muted px-4 py-4">
                                <p class="text-xs uppercase tracking-[0.22em] text-slate-500">Grounded Answers</p>
                                <p class="mt-2 text-sm text-slate-200">Citations, summaries, action items, and governance insights stay tied to your notebook sources.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-center p-6 sm:p-10">
                    <div class="w-full max-w-md">
                        <div class="mb-8 flex items-center gap-3 lg:hidden">
                            <x-application-logo class="h-12 w-12" />
                            <div>
                                <p class="text-xs uppercase tracking-[0.24em] text-slate-400">NoteGov AI DILG</p>
                                <p class="text-lg font-bold text-white">Secure access</p>
                            </div>
                        </div>
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
