@extends('layouts.auth')

@section('content')
    <div class="auth-shell">
        <div class="auth-container">
            <div class="auth-header">
                <div class="auth-icon-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="auth-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.11 1.5 2.081v8.657a2.25 2.25 0 0 1-2.25 2.25H4.5a2.25 2.25 0 0 1-2.25-2.25v-8.657c0-.97.616-1.797 1.5-2.081m16.5 0A2.25 2.25 0 0 0 18 6.75H6a2.25 2.25 0 0 0-2.25 1.761m16.5 0v.739a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 9.25v-.739m8.25 3v6m3-3h-6" />
                    </svg>
                </div>
                <h1 class="auth-brand">PhysioAdmin</h1>
                <p class="auth-subtitle">Klinik Fisioterapi - Rekam Medis Digital</p>
            </div>

            <div class="auth-card">
                <h2 class="auth-card-title">Masuk ke Sistem</h2>
                <p class="auth-card-subtitle">
                    Silakan masukkan kredensial admin Anda untuk melanjutkan.
                </p>

                <form method="POST" action="{{ route('login') }}" class="auth-form">
                    @csrf

                    <div class="form-group">
                        <label for="email" class="field-label">EMAIL</label>
                        <div class="input-wrap">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="input-icon">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                            <input
                                id="email"
                                type="text"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Masukkan email admin"
                                class="text-input"
                            >
                        </div>
                        @error('email')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="form-row">
                            <label for="password" class="field-label" style="margin-bottom: 0;">KATA SANDI</label>
                            <a href="/forgot-password" class="forgot-link">LUPA SANDI?</a>
                        </div>
                        <div class="input-wrap">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="input-icon">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 0h10.5A2.25 2.25 0 0 1 19.5 12.75v6A2.25 2.25 0 0 1 17.25 21h-10.5A2.25 2.25 0 0 1 4.5 18.75v-6a2.25 2.25 0 0 1 2.25-2.25Z" />
                            </svg>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                placeholder="Masukkan kata sandi"
                                class="text-input"
                            >
                            <button type="button" data-password-toggle data-target="password" class="toggle-password" aria-label="Tampilkan atau sembunyikan kata sandi">
                                <svg data-eye="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <svg data-eye="hide" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="display:none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18M10.477 10.486a3 3 0 0 0 4.037 4.037m2.474-2.473a3 3 0 0 0-4.037-4.037M6.228 6.235C4.137 7.527 2.63 9.55 2.036 11.683a1.012 1.012 0 0 0 0 .639C3.423 16.49 7.36 19.5 12 19.5a9.96 9.96 0 0 0 5.272-1.5M6.228 6.235A9.956 9.956 0 0 1 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639a11.053 11.053 0 0 1-4.675 5.683M6.228 6.235 3 3" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="remember-row">
                        <input type="checkbox" name="remember" id="remember">
                        <label for="remember">Ingat perangkat ini</label>
                    </div>

                    <button type="submit" class="submit-button">
                        <span>Masuk</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.75 21 12m0 0-3.75 3.25M21 12H3" />
                        </svg>
                    </button>

                    <hr class="divider">

                    <div class="security-box">
                        <div class="security-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m6 2.25c0 5.385-3.438 10.143-8.25 11.826C7.938 22.143 4.5 17.385 4.5 12V5.741A2.25 2.25 0 0 1 5.91 3.65l5.59-2.236a1.125 1.125 0 0 1 .998 0l5.59 2.236A2.25 2.25 0 0 1 19.5 5.741V12Z" />
                            </svg>
                        </div>
                        <div>
                            <p class="security-title">AKSES TERPROTEKSI</p>
                            <p class="security-body">
                                Sistem ini memantau semua aktivitas login untuk tujuan audit keamanan rekam medis pasien.
                            </p>
                        </div>
                    </div>
                </form>
            </div>

            <div class="auth-footer">
                <p class="auth-footer-links">Kebijakan Privasi - Bantuan Teknis</p>
                <p class="auth-footer-copy">(c) 2026 PhysioAdmin Management System. v2.4.0</p>
            </div>
        </div>
    </div>
@endsection
