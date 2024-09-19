@extends('layouts.layout')

@section('content')
<h1 class="my-4">Dozent hinzufügen</h1>
@include('snippets.error')

<form method="POST" action="{{ url('/lecturers/save') }}">
    @csrf
    <div class="form-floating mb-3">
        <input type="text" name="firstname" class="form-control" id="firstname" placeholder="Vorname">
        <label for="firstname">Vorname</label>
    </div>
    <div class="form-floating mb-3">
        <input type="text" name="lastname" class="form-control" id="lastname" placeholder="Nachname">
        <label for="lastname">Nachname</label>
    </div>
    <div class="form-floating mb-3">
        <input type="email" name="email" class="form-control" id="email" placeholder="E-Mail">
        <label for="email">E-Mail</label>
    </div>
    <div class="form-floating mb-3">
        <input type="text" name="phone" class="form-control" id="phone" placeholder="Telefon">
        <label for="phone">Telefon</label>
    </div>
    <button type="submit" class="btn btn-success">Speichern</button>
    <a href="{{ url('lecturers') }}" class="btn btn-danger">Abbrechen</a>
</form>
@endsection