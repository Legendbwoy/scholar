@extends('layouts.portal')

@section('title', 'Institutional Portal — Scholar SMS')

@section('content')
<div class="portal-card p-5 text-center">
    <div class="mb-3">
        <i class="fa-solid fa-graduation-cap text-primary" style="font-size: 64px;"></i>
    </div>
    <h3 class="fw-bold mb-2">Welcome to Scholar SMS</h3>
    <p class="text-muted mx-auto" style="max-width: 500px;">
        You are authenticated as <strong>{{ $user->name }}</strong> (Role: <span class="badge bg-primary text-uppercase">{{ $user->role }}</span>).
    </p>
    <a href="{{ route('home') }}" class="btn btn-outline-primary rounded-pill px-4 me-2">Public Site</a>
    <a href="{{ route('courses.index') }}" class="btn btn-primary rounded-pill px-4">Browse Courses</a>
</div>
@endsection