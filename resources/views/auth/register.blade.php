@extends('layouts.app')
@section('title', 'Crear cuenta')
@section('content')
<div class="auth-shell"><section class="panel auth-card">
    <div class="auth-heading"><span class="brand-mark">SU</span><h1>Crea tu cuenta</h1><p>Completa tus datos para comenzar.</p></div>
    <form method="POST" action="/register">@csrf
        <div class="field"><label for="name">Nombre completo</label><input id="name" name="name" class="control" placeholder="Tu nombre" value="{{ old('name') }}" autocomplete="name" required>@error('name')<span class="error-text">{{ $message }}</span>@enderror</div>
        <div class="field"><label for="email">Correo electrónico</label><input id="email" name="email" type="email" class="control" placeholder="nombre@correo.com" value="{{ old('email') }}" autocomplete="email" required>@error('email')<span class="error-text">{{ $message }}</span>@enderror</div>
        <div class="field"><label for="password">Contraseña</label><input id="password" name="password" type="password" class="control" placeholder="Crea una contraseña" autocomplete="new-password" required>@error('password')<span class="error-text">{{ $message }}</span>@enderror</div>
        <div class="field"><label for="password_confirmation">Confirmar contraseña</label><input id="password_confirmation" name="password_confirmation" type="password" class="control" placeholder="Repite tu contraseña" autocomplete="new-password" required></div>
        <button class="button button-primary button-wide" type="submit">Crear cuenta</button>
    </form>
    <p class="auth-footer">¿Ya tienes cuenta? <a href="/login">Iniciar sesión</a></p>
</section></div>
@endsection
