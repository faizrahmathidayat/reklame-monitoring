<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') — Reklame Monitoring</title>

    {{-- Bootstrap 5 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    {{-- Font Awesome 6 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Inter', sans-serif; }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        /* Floating orbs background decoration */
        body::before, body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            pointer-events: none;
        }
        body::before {
            width: 400px; height: 400px;
            background: rgba(99, 102, 241, 0.15);
            top: -100px; left: -100px;
        }
        body::after {
            width: 350px; height: 350px;
            background: rgba(139, 92, 246, 0.12);
            bottom: -80px; right: -80px;
        }

        .auth-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            background: rgba(30, 41, 59, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(99, 102, 241, 0.25);
            border-radius: 1.25rem;
            padding: 2.5rem 2rem;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255,255,255,0.06);
        }

        .brand-icon {
            width: 56px; height: 56px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 0.875rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; color: white;
            margin: 0 auto 1rem;
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.4);
        }

        .auth-title {
            color: #f1f5f9;
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 0.25rem;
        }

        .auth-subtitle {
            color: #94a3b8;
            font-size: 0.875rem;
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-label {
            color: #cbd5e1;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.4rem;
        }

        .form-control {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(99, 102, 241, 0.2);
            color: #f1f5f9;
            border-radius: 0.625rem;
            padding: 0.65rem 1rem;
            font-size: 0.95rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control:focus {
            background: rgba(15, 23, 42, 0.8);
            border-color: #6366f1;
            color: #f1f5f9;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }
        .form-control::placeholder { color: #475569; }

        .input-group-text {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(99, 102, 241, 0.2);
            color: #64748b;
        }

        .btn-login {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            color: white;
            font-weight: 600;
            padding: 0.75rem;
            border-radius: 0.625rem;
            font-size: 0.95rem;
            letter-spacing: 0.02em;
            transition: opacity 0.2s, transform 0.15s;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.35);
        }
        .btn-login:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            color: white;
        }
        .btn-login:active { transform: translateY(0); }

        .form-check-input {
            background-color: rgba(15, 23, 42, 0.6);
            border-color: rgba(99, 102, 241, 0.3);
        }
        .form-check-input:checked {
            background-color: #6366f1;
            border-color: #6366f1;
        }
        .form-check-label { color: #94a3b8; font-size: 0.875rem; }

        .invalid-feedback { font-size: 0.8rem; }

        .alert-danger {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            border-radius: 0.625rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
