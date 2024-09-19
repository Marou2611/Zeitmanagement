@extends('layouts.layout')

@section('content')
<h1 class="my-4 h3">Fehlende Zeitpläne: <b>{{ $currentSemester->name }}</b></h1>

@if($pendingLecturers->isEmpty())
<div class="alert alert-success">
    <strong>Alle Dozent:innen haben ihren Stundenplan eingereicht.</strong>
</div>
@else
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Vorname</th>
                <th>Nachname</th>
                <th>E-Mail</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pendingLecturers as $lecturer)
            <tr>
                <td>{{ $lecturer->firstname }}</td>
                <td>{{ $lecturer->lastname }}</td>
                <td>{{ $lecturer->email }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<!-- Button zur Rückkehr -->
<a href="{{ url()->previous() }}" class="btn btn-primary">Zurück</a>
@endsection