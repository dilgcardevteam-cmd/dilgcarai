<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NoteGov LM DILG</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Poppins', sans-serif;
            background-color: #ffffff;
            color: #000000;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 32px 0;
            animation: fadeIn 0.8s ease-out;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .dilg-logo {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            object-fit: contain;
        }

        .logo-text {
            font-size: 18px;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        nav {
            display: flex;
            gap: 40px;
        }

        nav a {
            font-size: 16px;
            font-weight: 500;
            color: #000000;
            text-decoration: none;
            position: relative;
            padding: 4px 0;
            transition: color 0.3s ease;
        }

        nav a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #4ade80, #22d3ee, #60a5fa);
            transition: width 0.3s ease;
        }

        nav a:hover::after {
            width: 100%;
        }

        main {
            padding: 80px 0;
        }

        .hero-section {
            text-align: center;
            padding: 60px 0;
            animation: fadeInUp 1s ease-out;
        }

        .hero-headline {
            font-size: 80px;
            font-weight: 700;
            line-height: 1.1;
            margin-bottom: 24px;
            letter-spacing: -0.02em;
        }

        .gradient-text {
            background: linear-gradient(90deg, #4ade80, #22d3ee, #60a5fa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: 20px;
            font-weight: 400;
            color: #666666;
            max-width: 900px;
            margin: 0 auto 48px;
            line-height: 1.6;
        }

        .cta-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #000000;
            color: #ffffff;
            font-size: 16px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 18px 36px;
            border-radius: 16px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-decoration: none;
        }

        .cta-button:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }

        .additional-section {
            text-align: center;
            padding: 100px 0 80px;
            animation: fadeInUp 1.2s ease-out;
        }

        .additional-heading {
            font-size: 36px;
            font-weight: 600;
            color: #000000;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 1024px) {
            .hero-headline {
                font-size: 60px;
            }
        }

        @media (max-width: 768px) {
            header {
                flex-direction: column;
                gap: 24px;
            }

            nav {
                gap: 24px;
            }

            .hero-headline {
                font-size: 44px;
            }

            .hero-subtitle {
                font-size: 18px;
            }

            .additional-heading {
                font-size: 28px;
            }
        }

        @media (max-width: 480px) {
            .hero-headline {
                font-size: 36px;
            }

            .cta-button {
                width: 100%;
                padding: 16px 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo-section">
                <x-application-logo class="dilg-logo" />
                <span class="logo-text">NOTEGOV LM DILG</span>
            </div>
            <nav>
                <a href="#overview">Overview</a>
                <a href="#plans">Plans</a>
            </nav>
        </header>

        <main>
            <section class="hero-section">
                <h1 class="hero-headline">
                    Understand <span class="gradient-text">Anything</span>
                </h1>
                <p class="hero-subtitle">
                    Your research and thinking partner, grounded in the information you trust, built with the latest Gemini models.
                </p>
                <a href="{{ route('login') }}" class="cta-button">TRY NOTEGOV LM</a>
            </section>

            <section class="additional-section">
                <h2 class="additional-heading">Your AI-Powered Research Partner</h2>
            </section>
        </main>
    </div>
</body>
</html>