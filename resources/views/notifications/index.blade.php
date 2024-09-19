@extends('layouts.layout')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Übersicht</h1>
    <!-- Aktuelles Semester ausgeben -->
    <div class="alert alert-light" role="alert">
        <h4 class="alert-heading">Aktuelles Semester</h4>
        <p>Das aktuelle Semester ist das <strong>{{ $currentSemester->name }}</strong>.</p>
    </div>

    <div class="row">
        <!-- Eingereichte Stundenpläne -->
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Eingereichte Stundenpläne</h5>
                    <p class="card-text display-4">{{ $submittedTimetables }}</p>
                    <a href="{{url('semesters/timetables/'.$currentSemester->id)}}" class="btn btn-light">Details ansehen</a>
                </div>
            </div>
        </div>

        <!-- Ausstehende Antworten -->
        <div class="col-md-4">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Ausstehende Antworten</h5>
                    <p class="card-text display-4">{{ $pendingResponses }}</p>
                    <a href="{{ url('/notifications/pending') }}" class="btn btn-light">Details ansehen</a>
                </div>
            </div>
        </div>

        <!-- Ausstehende Emails -->
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Ausstehende E-Mails</h5>
                    <p class="card-text display-4">{{ $pendingEmails }}</p>
                    <p>
                        E-Mails werden automatisch versendet.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection