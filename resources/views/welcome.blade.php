@extends('layouts.app')
@section('title', 'Bienvenido')
@section('content')
<section class="welcome-card"><div><p class="eyebrow">Sistema universitario</p><h1>Tu vida académica, en un solo lugar.</h1><p>Consulta carreras, estudiantes e inscripciones desde una experiencia clara y sencilla.</p><div class="actions">@auth<a href="/" class="button button-primary">Ir al panel</a>@else<a href="/login" class="button button-primary">Iniciar sesión</a><a href="/register" class="button button-secondary">Crear cuenta</a>@endauth</div></div><div class="welcome-visual" aria-hidden="true">SU</div></section>
@endsection
