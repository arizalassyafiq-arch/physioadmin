<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PhysioAdmin - Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg: #eaf2f7;
            --card: #ffffff;
            --navy: #1e3a8a;
            --navy-hover: #1e40af;
            --text: #1e293b;
            --muted: #64748b;
            --border: #d1d5db;
            --green-bg: #f0fdf4;
            --green-border: #bbf7d0;
            --green-text: #15803d;
            --danger: #ef4444;
            --shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
        }

        * {
            box-sizing: border-box;
        }

        [x-cloak] {
            display: none !important;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .auth-shell {
            position: relative;
            isolation: isolate;
            overflow: hidden;
            width: 100%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            background:
                linear-gradient(117deg, rgba(12, 74, 110, 0.96) 0%, rgba(14, 116, 144, 0.88) 34%, rgba(219, 234, 254, 0.94) 34.2%, rgba(241, 248, 251, 0.96) 100%),
                linear-gradient(180deg, #e6f4f1 0%, #eef6ff 100%);
        }

        .auth-shell::before,
        .auth-shell::after {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            z-index: -1;
        }

        .auth-shell::before {
            background-image:
                repeating-linear-gradient(90deg, rgba(255, 255, 255, 0.18) 0 1px, transparent 1px 72px),
                repeating-linear-gradient(0deg, rgba(255, 255, 255, 0.14) 0 1px, transparent 1px 72px);
            mask-image: linear-gradient(90deg, rgba(0, 0, 0, 0.82), rgba(0, 0, 0, 0.22) 54%, transparent 82%);
        }

        .auth-shell::after {
            background:
                linear-gradient(145deg, transparent 0 49%, rgba(255, 255, 255, 0.38) 49.1% 50.6%, transparent 50.7%),
                linear-gradient(25deg, transparent 0 57%, rgba(20, 184, 166, 0.18) 57.1% 58.4%, transparent 58.5%);
            opacity: 0.95;
        }

        .auth-container {
            position: relative;
            z-index: 1;
            width: calc(100vw - 32px);
            max-width: 420px;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .auth-icon-wrap {
            display: flex;
            justify-content: center;
            color: var(--navy);
        }

        .auth-icon {
            width: 48px;
            height: 48px;
        }

        .auth-brand {
            margin: 16px 0 8px;
            font-size: 28px;
            font-weight: 700;
            color: var(--navy);
            line-height: 1.1;
        }

        .auth-subtitle {
            margin: 0;
            font-size: 14px;
            color: var(--muted);
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.94);
            border: 1px solid rgba(255, 255, 255, 0.72);
            border-radius: 16px;
            box-shadow: var(--shadow);
            padding: 40px;
            backdrop-filter: blur(14px);
        }

        .auth-card-title {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            color: var(--text);
        }

        .auth-card-subtitle {
            margin: 10px 0 0;
            font-size: 13px;
            color: var(--muted);
            line-height: 1.6;
        }

        .auth-form {
            margin-top: 28px;
        }

        .form-group + .form-group,
        .form-group + .form-row,
        .form-row + .remember-row,
        .remember-row + .submit-button,
        .submit-button + .divider,
        .divider + .security-box {
            margin-top: 20px;
        }

        .field-label {
            display: block;
            margin-bottom: 6px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.18em;
            color: #4b5563;
        }

        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .forgot-link {
            font-size: 12px;
            font-weight: 700;
            color: #2563eb;
            text-decoration: none;
        }

        .forgot-link:hover {
            color: var(--navy-hover);
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            width: 20px;
            height: 20px;
            pointer-events: none;
        }

        .text-input {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 14px 44px 14px 42px;
            font-size: 14px;
            color: var(--text);
            background: #fff;
            outline: none;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .text-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.18);
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            width: 24px;
            height: 24px;
            border: 0;
            background: transparent;
            color: #6b7280;
            padding: 0;
            cursor: pointer;
        }

        .toggle-password svg {
            width: 20px;
            height: 20px;
            display: block;
        }

        .remember-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .remember-row input {
            width: 16px;
            height: 16px;
        }

        .remember-row label {
            font-size: 14px;
            color: #374151;
        }

        .submit-button,
        .outline-button {
            width: 100%;
            border-radius: 10px;
            padding: 14px 18px;
            font-size: 15px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            transition: background-color .2s ease, color .2s ease, border-color .2s ease;
            cursor: pointer;
        }

        .submit-button {
            border: 0;
            background: var(--navy);
            color: #fff;
        }

        .submit-button:hover {
            background: var(--navy-hover);
        }

        .outline-button {
            border: 1px solid var(--navy);
            background: #fff;
            color: var(--navy);
        }

        .outline-button:hover {
            background: #eff6ff;
        }

        .submit-button svg,
        .outline-button svg,
        .security-icon {
            width: 18px;
            height: 18px;
        }

        .divider {
            border: 0;
            border-top: 1px solid #e5e7eb;
            margin: 20px 0 0;
        }

        .security-box {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            background: var(--green-bg);
            border: 1px solid var(--green-border);
            border-radius: 10px;
            padding: 16px;
        }

        .security-icon {
            flex: 0 0 auto;
            color: #16a34a;
            margin-top: 2px;
        }

        .security-title {
            margin: 0 0 4px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.18em;
            color: var(--green-text);
        }

        .security-body {
            margin: 0;
            font-size: 12px;
            color: #4b5563;
            line-height: 1.6;
        }

        .error-text {
            margin: 6px 0 0;
            font-size: 12px;
            color: var(--danger);
        }

        .auth-footer {
            margin-top: 28px;
            text-align: center;
            color: #64748b;
        }

        .auth-footer-links {
            margin: 0;
            font-size: 14px;
        }

        .auth-footer-copy {
            margin: 14px 0 0;
            font-size: 12px;
        }

        .info-card {
            text-align: center;
        }

        .info-message {
            margin: 12px 0 0;
            font-size: 14px;
            color: var(--muted);
            line-height: 1.7;
        }

        .contact-line {
            margin: 12px 0 0;
            font-size: 14px;
            font-weight: 600;
            color: #334155;
        }

        @media (max-width: 480px) {
            .auth-shell {
                align-items: flex-start;
                padding-top: 28px;
                background:
                    linear-gradient(180deg, rgba(12, 74, 110, 0.94) 0 188px, rgba(225, 240, 248, 0.98) 188px 100%),
                    linear-gradient(180deg, #e6f4f1 0%, #eef6ff 100%);
            }

            .auth-shell::before {
                mask-image: linear-gradient(180deg, rgba(0, 0, 0, 0.72), transparent 260px);
            }

            .auth-container,
            .auth-card,
            .auth-form,
            .form-group,
            .input-wrap {
                min-width: 0;
            }

            .auth-container {
                max-width: calc(100vw - 32px);
            }

            .form-row {
                flex-wrap: wrap;
            }

            .auth-card {
                padding: 28px 22px;
            }
        }
    </style>
</head>
<body>
    @yield('content')
    <script>
        document.addEventListener('click', function (event) {
            const button = event.target.closest('[data-password-toggle]');
            if (!button) {
                return;
            }

            const targetId = button.getAttribute('data-target');
            const input = document.getElementById(targetId);

            if (!input) {
                return;
            }

            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';

            const showIcon = button.querySelector('[data-eye="show"]');
            const hideIcon = button.querySelector('[data-eye="hide"]');

            if (showIcon && hideIcon) {
                showIcon.style.display = isHidden ? 'none' : 'block';
                hideIcon.style.display = isHidden ? 'block' : 'none';
            }
        });
    </script>
</body>
</html>
