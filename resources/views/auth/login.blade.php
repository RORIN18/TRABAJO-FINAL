@extends('layouts.app')
@section('title', 'Iniciar sesión')
@section('content')
<div class="auth-shell"><section class="panel auth-card">
    <div class="auth-heading"><span class="brand-mark">SU</span><h1>Qué bueno verte</h1><p>Ingresa a tu cuenta para continuar.</p></div>
    <form method="POST" action="/login">@csrf
        <div class="field"><label for="email">Correo electrónico</label><input id="email" name="email" type="email" class="control" placeholder="nombre@correo.com" value="{{ old('email') }}" autocomplete="email" required>@error('email')<span class="error-text">{{ $message }}</span>@enderror</div>
        <div class="field"><label for="password">Contraseña</label><input id="password" name="password" type="password" class="control" placeholder="Ingresa tu contraseña" autocomplete="current-password" required></div>
        <label class="field-label" style="display:flex;align-items:center;gap:9px;font-weight:500"><input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}> Mantener sesión iniciada</label>
        <button class="button button-primary button-wide" type="submit">Entrar a mi cuenta</button>
    </form>
    <p class="auth-footer">¿Aún no tienes cuenta? <a href="/register">Crear una cuenta</a></p>
</section></div>
@endsection
