@extends('layouts.auth')

@section('content')
    <div class="auth-shell">
        <div class="auth-container">
            <div class="auth-card info-card">
                <div class="auth-icon-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="auth-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 0h10.5A2.25 2.25 0 0 1 19.5 12.75v6A2.25 2.25 0 0 1 17.25 21h-10.5A2.25 2.25 0 0 1 4.5 18.75v-6a2.25 2.25 0 0 1 2.25-2.25Z" />
                    </svg>
                </div>
                <h1 class="auth-brand" style="font-size: 26px;">Lupa Kata Sandi?</h1>
                <p class="info-message">
                    Silakan hubungi administrator sistem untuk mereset kata sandi Anda.
                </p>
                <p class="contact-line">Hubungi: admin@physio.com</p>

                <a href="{{ route('login') }}" class="outline-button" style="margin-top: 28px;">
                    Kembali ke Login
                </a>
            </div>

            <div class="auth-footer">
                <p class="auth-footer-links">Kebijakan Privasi · Bantuan Teknis</p>
                <p class="auth-footer-copy">© 2024 PhysioAdmin Management System. v2.4.0</p>
            </div>
        </div>
    </div>
@endsection
