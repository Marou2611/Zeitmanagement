@extends('layouts.layout')

@section('content')
<h1 class="my-4">Semester hinzufügen</h1>
@include('snippets.error')

<form method="POST" action="{{ url('/semesters/save') }}">
    @csrf
    <div class="form-floating mb-3">
        <input type="text" name="name" class="form-control" id="name" placeholder="WiSe 2025">
        <label for="name">Kurzbezeichnung</label>
    </div>
    <div class="form-floating mb-3">
        <input type="date" name="start_date" class="form-control" id="start_date" placeholder="01.10.2025">
        <label for="start_date">Startdatum</label>
    </div>

    <div class="form-floating mb-3">
        <input type="date" name="end_date" class="form-control" id="end_date" placeholder="31.03.2026">
        <label for="end_date">Enddatum</label>
    </div>

    <button type="submit" class="btn btn-success">Speichern</button>
    <a href="{{ url('semesters') }}" class="btn btn-danger">Abbrechen</a>
</form>
@endsection