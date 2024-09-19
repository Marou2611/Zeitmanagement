@extends('layouts.layout')

@section('content')
<h1 class="my-3">Semester bearbeiten</h1>
@include('snippets.error')

<form method="POST" action="{{ url('semesters/update/'.$entity->id) }}">
    @csrf
    @method('POST')

    <div class="form-floating mb-3">
        <input type="text" name="name" class="form-control" id="name" placeholder="Kurzbezeichnung" value="{{ old('name', $entity->name) }}">
        <label for="name">Kurzbezeichnung</label>
    </div>
    <div class="form-floating mb-3">
        <input type="date" name="start_date" class="form-control" id="start_date" value="{{ old('start_date', $entity->start_date) }}">
        <label for="start_date">Startdatum</label>
    </div>
    <div class="form-floating mb-3">
        <input type="date" name="end_date" class="form-control" id="end_date" value="{{ old('end_date', $entity->end_date) }}">
        <label for="end_date">Enddatum</label>
    </div>
    <button type="submit" class="btn btn-success">Speichern</button>
    <a href="{{ url('semesters') }}" class="btn btn-danger">Abbrechen</a>
</form>
@endsection