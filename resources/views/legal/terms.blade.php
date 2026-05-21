<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terms of Service - NoteGov AI DILG</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #071028 0%, #0F172A 100%);
            min-height: 100vh;
            color: #FFFFFF;
            position: relative;
        }

        .grid-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 1px 1px, rgba(96, 165, 250, 0.1) 1px, transparent 0),
                linear-gradient(rgba(37, 99, 235, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(37, 99, 235, 0.03) 1px, transparent 1px);
            background-size: 40px 40px, 80px 80px, 80px 80px;
            background-position: -1px -1px, 0 0, 0 0;
            pointer-events: none;
            z-index: 0;
        }

        .glow-1 {
            position: fixed;
            top: -200px;
            right: -200px;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(96, 165, 250, 0.2) 0%, transparent 70%);
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
        }

        .glow-2 {
            position: fixed;
            bottom: -200px;
            left: -200px;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.15) 0%, transparent 70%);
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 60px 24px 120px;
            position: relative;
            z-index: 1;
        }

        .header {
            margin-bottom: 60px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 32px;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: #60A5FA;
        }

        .back-link svg {
            width: 16px;
            height: 16px;
        }

        .header h1 {
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 12px;
            letter-spacing: -0.02em;
        }

        .header p {
            font-size: 15px;
            color: rgba(255,255,255,0.6);
        }

        .content {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(96, 165, 250, 0.2);
            border-radius: 24px;
            padding: 48px;
        }

        .section {
            margin-bottom: 40px;
        }

        .section:last-child {
            margin-bottom: 0;
        }

        .section h2 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 16px;
            color: #FFFFFF;
        }

        .section p {
            font-size: 15px;
            line-height: 1.7;
            color: rgba(255,255,255,0.7);
            margin-bottom: 12px;
        }

        .section p:last-child {
            margin-bottom: 0;
        }

        .section ul {
            margin: 16px 0 16px 24px;
        }

        .section li {
            font-size: 15px;
            line-height: 1.7;
            color: rgba(255,255,255,0.7);
            margin-bottom: 8px;
        }

        .section li:last-child {
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="grid-overlay"></div>
    <div class="glow-1"></div>
    <div class="glow-2"></div>

    <div class="container">
        <div class="header">
            <a href="{{ route('register') }}" class="back-link">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to registration
            </a>
            <h1>Terms of Service</h1>
            <p>Last updated: May 19, 2026</p>
        </div>

        <div class="content">
            <div class="section">
                <h2>1. Acceptance of Terms</h2>
                <p>By accessing and using NoteGov AI DILG, you agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use the platform.</p>
            </div>

            <div class="section">
                <h2>2. Description of Service</h2>
                <p>NoteGov AI DILG is an AI-powered platform designed for government operations, policy research, and document analysis. The service provides tools for creating notebooks, uploading sources, and collaborating with team members.</p>
            </div>

            <div class="section">
                <h2>3. User Accounts</h2>
                <ul>
                    <li>You must provide accurate and complete information when creating an account</li>
                    <li>You are responsible for maintaining the security of your account credentials</li>
                    <li>You are responsible for all activities that occur under your account</li>
                    <li>Notify us immediately of any unauthorized use of your account</li>
                </ul>
            </div>

            <div class="section">
                <h2>4. Acceptable Use</h2>
                <p>You agree not to:</p>
                <ul>
                    <li>Use the service for any illegal or unauthorized purpose</li>
                    <li>Violate any applicable laws or regulations</li>
                    <li>Infringe upon the rights of others</li>
                    <li>Upload malicious content or malware</li>
                    <li>Attempt to gain unauthorized access to the system</li>
                    <li>Interfere with or disrupt the service</li>
                </ul>
            </div>

            <div class="section">
                <h2>5. Intellectual Property</h2>
                <p>All content, features, and functionality of NoteGov AI DILG are owned by us and are protected by international copyright, trademark, and other intellectual property laws.</p>
            </div>

            <div class="section">
                <h2>6. Limitation of Liability</h2>
                <p>To the fullest extent permitted by law, NoteGov AI DILG shall not be liable for any indirect, incidental, special, consequential, or punitive damages arising out of or related to your use of the service.</p>
            </div>

            <div class="section">
                <h2>7. Changes to Terms</h2>
                <p>We reserve the right to modify these Terms of Service at any time. We will notify users of material changes via email or through the platform. Your continued use of the service after changes constitutes acceptance of the new terms.</p>
            </div>

            <div class="section">
                <h2>8. Termination</h2>
                <p>We reserve the right to suspend or terminate your account at our sole discretion, without notice, for conduct that we believe violates these Terms of Service or is harmful to other users.</p>
            </div>

            <div class="section">
                <h2>9. Contact Us</h2>
                <p>If you have any questions about these Terms of Service, please contact us at support@notegov.gov.ph</p>
            </div>
        </div>
    </div>
</body>
</html>
