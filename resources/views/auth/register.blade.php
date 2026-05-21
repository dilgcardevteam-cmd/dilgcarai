<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NoteGov AI DILG - Register</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #F5F1EA;
            height: 100vh;
            overflow: hidden;
            color: #1F2937;
            overflow-x: hidden;
        }

        /* Main container */
        .container {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            width: 100%;
            max-width: 1600px;
            margin: 0 auto;
            padding: 0;
            height: 100vh;
        }

        /* Left section */
        .left-section {
            padding: 24px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .logo-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
        }

        .dilg-logo {
            width: 64px;
            height: 64px;
            object-fit: contain;
            border-radius: 50%;
            background: white;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
        }

        .logo-text {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }

        .logo-text .brand-label {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            color: #6B5B4F;
        }

        .logo-text .brand-name {
            font-size: 20px;
            font-weight: 800;
            color: #111827;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 100px;
            background: rgba(184, 134, 11, 0.08);
            border: 1px solid rgba(184, 134, 11, 0.15);
            margin-bottom: 14px;
        }

        .badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #B8860B;
        }

        .badge-text {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #B8860B;
        }

        .hero-text {
            font-size: 24px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 10px;
            color: #111827;
            text-align: center;
            max-width: 520px;
        }

        .hero-text .accent {
            background: linear-gradient(135deg, #B8860B 0%, #C49A6C 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .description {
            font-size: 12px;
            line-height: 1.5;
            color: #6B7280;
            margin-bottom: 20px;
            max-width: 520px;
            text-align: center;
        }

        /* Features grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 18px;
            max-width: 520px;
        }

        .feature-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
        }

        .feature-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, rgba(184, 134, 11, 0.08) 0%, rgba(196, 154, 108, 0.08) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(184, 134, 11, 0.12);
        }

        .feature-icon svg {
            width: 18px;
            height: 18px;
            color: #B8860B;
        }

        .feature-title {
            font-size: 11px;
            font-weight: 700;
            color: #374151;
        }

        .feature-desc {
            font-size: 9px;
            color: #9CA3AF;
            text-align: center;
        }

        /* Trust banner */
        .trust-banner {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            background: rgba(255, 255, 255, 0.85);
            border-radius: 12px;
            border: 1px solid rgba(184, 134, 11, 0.1);
            backdrop-filter: blur(10px);
        }

        .trust-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(184, 134, 11, 0.06);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .trust-icon svg {
            width: 18px;
            height: 18px;
            color: #B8860B;
        }

        .trust-text {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .trust-text h4 {
            font-size: 12px;
            font-weight: 700;
            color: #374151;
        }

        .trust-text p {
            font-size: 10px;
            color: #9CA3AF;
        }

        /* Right section */
        .right-section {
            padding: 24px 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.5);
        }

        /* Register card */
        .register-card {
            width: 100%;
            max-width: 500px;
            background: white;
            border-radius: 20px;
            padding: 28px 28px;
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(184, 134, 11, 0.08);
        }

        .card-header-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(184, 134, 11, 0.1) 0%, rgba(196, 154, 108, 0.1) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
        }

        .card-header-icon svg {
            width: 20px;
            height: 20px;
            color: #B8860B;
        }

        .card-title {
            font-size: 18px;
            font-weight: 800;
            color: #111827;
            text-align: center;
            margin-bottom: 4px;
        }

        .card-subtitle {
            font-size: 12px;
            color: #6B7280;
            text-align: center;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        /* Form styles */
        .form-row {
            display: grid;
            grid-template-columns: 1.5fr 1.5fr 0.7fr;
            gap: 8px;
            margin-bottom: 12px;
        }

        .form-group {
            margin-bottom: 10px;
        }

        .form-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 5px;
            color: #1F2937;
        }

        .form-label .required {
            color: #B8860B;
        }

        .form-input {
            width: 100%;
            padding: 9px 12px 9px 38px;
            background: #FAF8F3;
            border: 1px solid #E5E1DA;
            border-radius: 10px;
            font-size: 13px;
            color: #1F2937;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s ease;
            outline: none;
        }

        .form-input:focus {
            border-color: #B8860B;
            box-shadow: 0 0 0 3px rgba(184, 134, 11, 0.1);
            background: white;
        }

        .form-input option {
            background: white;
            color: #1F2937;
        }

        .form-input::placeholder {
            color: #9CA3AF;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9CA3AF;
        }

        .input-icon svg {
            width: 16px;
            height: 16px;
            display: block;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9CA3AF;
            cursor: pointer;
            padding: 5px;
            transition: color 0.2s ease;
            border-radius: 8px;
        }

        .password-toggle:hover {
            color: #B8860B;
            background: rgba(184, 134, 11, 0.08);
        }

        .password-toggle svg {
            width: 16px;
            height: 16px;
            display: block;
        }

        /* Terms box */
        .terms-box {
            display: flex;
            gap: 8px;
            margin: 12px 0;
        }

        .terms-checkbox {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            border: 2px solid #D1D5DB;
            background: #FAF8F3;
            cursor: pointer;
            accent-color: #B8860B;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .terms-text {
            font-size: 10px;
            color: #6B7280;
            line-height: 1.4;
        }

        .terms-text a {
            color: #B8860B;
            text-decoration: none;
            font-weight: 700;
        }

        .terms-text a:hover {
            text-decoration: underline;
        }

        /* Buttons */
        .btn-primary {
            width: 100%;
            padding: 11px 28px;
            background: linear-gradient(135deg, #B8860B 0%, #9E7A0A 100%);
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            color: white;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 12px 32px rgba(184, 134, 11, 0.25);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 40px rgba(184, 134, 11, 0.3);
            background: linear-gradient(135deg, #C49A6C 0%, #B8860B 100%);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-primary svg {
            width: 16px;
            height: 16px;
        }

        .signin-link {
            text-align: center;
            margin-top: 12px;
            font-size: 12px;
            color: #6B7280;
        }

        .signin-link a {
            color: #B8860B;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.2s ease;
        }

        .signin-link a:hover {
            color: #9E7A0A;
            text-decoration: underline;
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 18px 0;
        }

        .divider-line {
            flex: 1;
            height: 1px;
            background: #E5E1DA;
        }

        .divider-text {
            font-size: 10px;
            color: #9CA3AF;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        /* Google button */
        .btn-google {
            width: 100%;
            padding: 11px 28px;
            background: white;
            border: 1px solid #E5E1DA;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            color: #374151;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-google:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.06);
            border-color: #D1D5DB;
        }

        .btn-google:active {
            transform: translateY(0);
        }

        .google-icon {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .google-icon svg {
            width: 24px;
            height: 24px;
        }

        /* Error messages */
        .error-message {
            background: rgba(239, 68, 68, 0.06);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 18px;
            color: #B91C1C;
            font-size: 13px;
        }

        .status-message {
            background: rgba(34, 197, 94, 0.06);
            border: 1px solid rgba(34, 197, 94, 0.2);
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 18px;
            color: #166534;
            font-size: 13px;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .container {
                grid-template-columns: 1fr;
            }

            .left-section {
                display: none;
            }

            .right-section {
                padding: 48px 24px;
                background: #F5F1EA;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .right-section {
                padding: 32px 16px;
            }

            .register-card {
                padding: 32px 24px;
            }

            .card-title {
                font-size: 20px;
            }
        }

        /* Reduced motion */
        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Left Section -->
        <div class="left-section">
            <div class="logo-area">
                <img src="{{ asset('images/dilg-logo.png') }}" alt="DILG Logo" class="dilg-logo">
                <div class="logo-text">
                    <span class="brand-label">GOVERNMENT AI PLATFORM</span>
                    <span class="brand-name">DILG • NoteGov AI</span>
                </div>
            </div>

            <div class="badge">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#B8860B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                    <path d="M2 17l10 5 10-5"></path>
                    <path d="M2 12l10 5 10-5"></path>
                </svg>
                <span class="badge-text">AI-POWERED GOVERNANCE</span>
            </div>

            <h1 class="hero-text">
                Transform government operations with <span class="accent">AI-powered intelligence</span>
            </h1>

            <p class="description">
                NoteGov AI delivers enterprise-grade policy intelligence, document analysis, and collaborative workflows designed exclusively for Philippine government institutions. Built with the latest AI technology to drive smarter, evidence-based governance.
            </p>

            <!-- Features -->
            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-4-4h-4a4 4 0 00-4 4v2"></path>
                        </svg>
                    </div>
                    <span class="feature-title">Secure</span>
                    <span class="feature-desc">Enterprise-grade security</span>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                    <span class="feature-title">Intelligent</span>
                    <span class="feature-desc">AI-powered policy analysis</span>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <span class="feature-title">Collaborative</span>
                    <span class="feature-desc">Built for government teams</span>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="feature-title">Compliant</span>
                    <span class="feature-desc">PH Data Privacy Act compliant</span>
                </div>
            </div>

            <!-- Trust Banner -->
            <div class="trust-banner">
                <div class="trust-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <div class="trust-text">
                    <h4>Trusted by Philippine Government Institutions</h4>
                    <p>Secure. Compliant. Trusted.</p>
                </div>
            </div>
        </div>

        <!-- Right Section -->
        <div class="right-section">
            <div class="register-card">
                <!-- Session Status -->
                @if (session('status'))
                    <div class="status-message">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="error-message">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="card-header-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>

                <h1 class="card-title">Create your account</h1>
                <p class="card-subtitle">Build smarter, evidence-based governance for your local government unit</p>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Name Row -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="last_name">Last Name <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <span class="input-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </span>
                                <input 
                                    id="last_name" 
                                    class="form-input" 
                                    type="text" 
                                    name="last_name" 
                                    value="{{ old('last_name', (session('google_name') ? explode(' ', session('google_name'))[array_key_last(explode(' ', session('google_name')))] : '')) }}" 
                                    required 
                                    placeholder="Dela Cruz"
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="first_name">First Name <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <span class="input-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </span>
                                <input 
                                    id="first_name" 
                                    class="form-input" 
                                    type="text" 
                                    name="first_name" 
                                    value="{{ old('first_name', (session('google_name') ? explode(' ', session('google_name'))[0] : '')) }}" 
                                    required 
                                    placeholder="Juan"
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="middle_initial">M.I.</label>
                            <div class="input-wrapper">
                                <span class="input-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </span>
                                <input 
                                    id="middle_initial" 
                                    class="form-input" 
                                    type="text" 
                                    name="middle_initial" 
                                    value="{{ old('middle_initial') }}" 
                                    maxlength="1"
                                    placeholder="A"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Office Selection (Full Width) -->
                    <div class="form-group" x-data="{ 
                        officeType: '{{ old('office_type') }}',
                        regions: {{ $regions->toJson() }},
                        provinces: {{ $provinces->toJson() }},
                        cities: {{ $cities->toJson() }},
                        selectedOfficeId: '{{ old('office_id') }}'
                    }">
                        <label class="form-label" for="office_type">Select Office <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </span>
                            <select 
                                id="office_type" 
                                name="office_type" 
                                class="form-input" 
                                required 
                                x-model="officeType"
                                @change="selectedOfficeId = ''"
                            >
                                <option value="" disabled selected>Choose your office type</option>
                                <option value="Regional">Regional</option>
                                <option value="Provincial">Provincial</option>
                                <option value="City/Municipality">City/Municipality</option>
                            </select>
                        </div>

                        <div class="form-group" style="margin-top: 10px;" x-show="officeType">
                            <label class="form-label">
                                <span x-show="officeType === 'Regional'">Select Region</span>
                                <span x-show="officeType === 'Provincial'">Select Province</span>
                                <span x-show="officeType === 'City/Municipality'">Select City/Municipality</span>
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <span class="input-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314-11.314z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </span>
                                <select 
                                    name="office_id" 
                                    class="form-input" 
                                    required 
                                    x-model="selectedOfficeId"
                                >
                                    <option value="" disabled selected>Choose option</option>
                                    <template x-if="officeType === 'Regional'">
                                        <template x-for="region in regions" :key="region.id">
                                            <option :value="region.id" x-text="region.name" :selected="selectedOfficeId == region.id"></option>
                                        </template>
                                    </template>
                                    <template x-if="officeType === 'Provincial'">
                                        <template x-for="province in provinces" :key="province.id">
                                            <option :value="province.id" x-text="province.name" :selected="selectedOfficeId == province.id"></option>
                                        </template>
                                    </template>
                                    <template x-if="officeType === 'City/Municipality'">
                                        <template x-for="city in cities" :key="city.id">
                                            <option :value="city.id" x-text="city.name" :selected="selectedOfficeId == city.id"></option>
                                        </template>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Work Email -->
                    <div class="form-group">
                        <label class="form-label" for="email">Work Email <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </span>
                            <input 
                                id="email" 
                                class="form-input" 
                                type="email" 
                                name="email" 
                                value="{{ old('email', session('google_email')) }}" 
                                required 
                                placeholder="your.name@dilg.gov.ph"
                            >
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label class="form-label" for="password">Password <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-4-4h-4a4 4 0 00-4 4v2"></path>
                                </svg>
                            </span>
                            <input 
                                id="password" 
                                class="form-input"
                                type="password"
                                name="password"
                                required 
                                minlength="12"
                                placeholder="Create a secure password (min. 12 characters)"
                            >
                            <button type="button" class="password-toggle" id="passwordToggle" aria-label="Show password">
                                <svg id="eyeOpen" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg id="eyeClosed" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Confirm Password <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-4-4h-4a4 4 0 00-4 4v2"></path>
                                </svg>
                            </span>
                            <input 
                                id="password_confirmation" 
                                class="form-input"
                                type="password"
                                name="password_confirmation"
                                required 
                                placeholder="Confirm your password"
                            >
                            <button type="button" class="password-toggle" id="confirmPasswordToggle" aria-label="Show password">
                                <svg id="confirmEyeOpen" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg id="confirmEyeClosed" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Terms Box -->
                    <div class="terms-box">
                        <input type="checkbox" id="terms" class="terms-checkbox" name="terms" required>
                        <div class="terms-text">
                            <label for="terms" style="cursor: pointer;">By creating an account, you agree to our <a href="{{ route('terms') }}">Terms of Service</a> and <a href="{{ route('privacy') }}">Privacy Policy</a>.</label>
                        </div>
                    </div>

                    <!-- Create Account Button -->
                    <button type="submit" class="btn-primary">
                        <span>Create Account</span>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </button>
                </form>

                <!-- Sign in Link -->
                <div class="signin-link">
                    Already registered? <a href="{{ route('login') }}">Sign in</a>
                </div>

                <!-- Divider -->
                <div class="divider">
                    <div class="divider-line"></div>
                    <span class="divider-text">or continue with</span>
                    <div class="divider-line"></div>
                </div>

                <!-- Google Button -->
                <a href="{{ route('auth.google') }}" class="btn-google" style="text-decoration: none;">
                    <div class="google-icon">
                        <svg viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                    </div>
                    Sign up with Google
                </a>
            </div>
        </div>
    </div>

    <script>
        // Password toggle
        document.getElementById('passwordToggle').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const eyeOpen = document.getElementById('eyeOpen');
            const eyeClosed = document.getElementById('eyeClosed');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeOpen.style.display = 'none';
                eyeClosed.style.display = 'block';
            } else {
                passwordInput.type = 'password';
                eyeOpen.style.display = 'block';
                eyeClosed.style.display = 'none';
            }
        });

        // Confirm password toggle
        document.getElementById('confirmPasswordToggle').addEventListener('click', function() {
            const passwordInput = document.getElementById('password_confirmation');
            const eyeOpen = document.getElementById('confirmEyeOpen');
            const eyeClosed = document.getElementById('confirmEyeClosed');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeOpen.style.display = 'none';
                eyeClosed.style.display = 'block';
            } else {
                passwordInput.type = 'password';
                eyeOpen.style.display = 'block';
                eyeClosed.style.display = 'none';
            }
        });
    </script>
</body>
</html>
