@extends('layouts.layout')

@section('content')
<h1 class="my-3">Dozent bearbeiten</h1>
@include('snippets.error')

<form method="POST" action="{{ url('lecturers/update/'.$entity->id) }}">
    @csrf
    @method('POST')

    <div class="form-floating mb-3">
        <input type="text" name="firstname" class="form-control" id="firstname" placeholder="Vorname" value="{{ old('firstname', $entity->firstname) }}">
        <label for="firstname">Vorname</label>
    </div>
    <div class="form-floating mb-3">
        <input type="text" name="lastname" class="form-control" id="lastname" placeholder="Nachname" value="{{ old('lastname', $entity->lastname) }}">
        <label for="lastname">Nachname</label>
    </div>
    <div class="form-floating mb-3">
        <input type="email" name="email" class="form-control" id="email" placeholder="E-Mail" value="{{ old('email', $entity->email) }}">
        <label for="email">E-Mail</label>
    </div>
    <div class="form-floating mb-3">
        <input type="text" name="phone" class="form-control" id="phone" placeholder="Telefon" value="{{ old('phone', $entity->phone) }}">
        <label for="phone">Telefon</label>
    </div>
    <div class="form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox" id="active" name="active" value="1" {{ $entity->active ? 'checked' : '' }}>
        <label class="form-check-label" for="active">Dozent:in ist aktiv</label>
    </div>
    <button type="submit" class="btn btn-success">Speichern</button>
    <a href="{{ url('lecturers') }}" class="btn btn-danger">Abbrechen</a>
</form>
@endsection