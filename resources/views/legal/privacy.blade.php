<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Privacy Policy - NoteGov AI DILG</title>
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
            <h1>Privacy Policy</h1>
            <p>Last updated: May 19, 2026</p>
        </div>

        <div class="content">
            <div class="section">
                <h2>1. Introduction</h2>
                <p>NoteGov AI DILG ("we", "us", or "our") respects your privacy and is committed to protecting your personal data. This Privacy Policy explains how we collect, use, and safeguard your information when you use our platform.</p>
            </div>

            <div class="section">
                <h2>2. Information We Collect</h2>
                <p>We collect the following types of information:</p>
                <ul>
                    <li><strong>Personal Information:</strong> Name, email address, and other account details</li>
                    <li><strong>Usage Data:</strong> Information about how you use the platform, including notebook activity and access logs</li>
                    <li><strong>Content Data:</strong> Documents, files, and other content you upload to your notebooks</li>
                    <li><strong>Technical Data:</strong> IP address, browser type, device information, and operating system</li>
                </ul>
            </div>

            <div class="section">
                <h2>3. How We Use Your Information</h2>
                <p>We use your information to:</p>
                <ul>
                    <li>Provide and maintain our service</li>
                    <li>Improve and optimize user experience</li>
                    <li>Communicate with you about service updates</li>
                    <li>Ensure security and prevent unauthorized access</li>
                    <li>Comply with legal obligations</li>
                </ul>
            </div>

            <div class="section">
                <h2>4. Data Security</h2>
                <p>We implement appropriate technical and organizational security measures to protect your personal data from unauthorized access, disclosure, alteration, or destruction. However, no method of transmission over the Internet is 100% secure.</p>
            </div>

            <div class="section">
                <h2>5. Data Sharing</h2>
                <p>We do not sell, trade, or rent your personal information to third parties. We may share your information with:</p>
                <ul>
                    <li>Service providers who assist in operating our platform</li>
                    <li>Government authorities when required by law</li>
                    <li>Other team members within your organization for collaboration purposes</li>
                </ul>
            </div>

            <div class="section">
                <h2>6. Your Rights</h2>
                <p>You have the right to:</p>
                <ul>
                    <li>Access your personal data</li>
                    <li>Correct inaccurate data</li>
                    <li>Request deletion of your data</li>
                    <li>Restrict or object to processing</li>
                    <li>Data portability</li>
                </ul>
            </div>

            <div class="section">
                <h2>7. Data Retention</h2>
                <p>We retain your personal data only for as long as necessary to provide our services and comply with legal obligations. When data is no longer needed, we will securely delete or anonymize it.</p>
            </div>

            <div class="section">
                <h2>8. Children's Privacy</h2>
                <p>Our service is not intended for use by individuals under the age of 18. We do not knowingly collect personal information from children.</p>
            </div>

            <div class="section">
                <h2>9. Changes to This Policy</h2>
                <p>We may update this Privacy Policy from time to time. We will notify you of any material changes by posting the new policy on this page and updating the "Last updated" date.</p>
            </div>

            <div class="section">
                <h2>10. Contact Us</h2>
                <p>If you have any questions about this Privacy Policy or our data practices, please contact us at privacy@notegov.gov.ph</p>
            </div>
        </div>
    </div>
</body>
</html>
