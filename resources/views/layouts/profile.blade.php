@extends('layouts.app')

@section('title', 'Mon profil')

@section('content')
    <h1>Mon profil</h1>

    <p><strong>Nom :</strong> {{ Auth::user()->name }}</p>
    <p><strong>Email :</strong> {{ Auth::user()->email }}</p>
@endsection
