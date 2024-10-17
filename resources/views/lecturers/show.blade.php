@extends('layouts.layout')

@section('content')
<h1 class="my-4">Ausgewählter Dozent: {{ $entity->firstname}} {{ $entity->lastname}}</h1>
<!-- Print message if no timetable available -->
@if(count($entity->timetables) == 0)
<div class="alert alert-info" role="alert">
    Der/die Dozent:in hat noch keine Stundenpläne eingereicht.
</div>
@endif

<!-- Print message if lecturer is inactive -->
@if(!$entity->active)
<div class="alert alert-warning" role="alert">
    Der/die Dozent:in ist inaktiv.
</div>
@endif

<table class="table table-striped">
    <tbody>
        <tr>
            <td>Name</td>
            <td>{{ $entity->firstname }} {{ $entity->lastname }}</td>
        </tr>
        <tr>
            <td>E-Mail</td>
            <td>{{ $entity->email }}</td>
        </tr>
        <tr>
            <td>Telefon</td>
            <td>{{ $entity->phone }}</td>
        </tr>
        <tr>
            <td>Erstellt am</td>
            <td>{{ $entity->created_at }}</td>
        </tr>
        <!-- Get the timetables of this lecturer -->
        @if(count($entity->timetables) > 0)
        <tr>
            <td>Eingereichte Stundenpläne</td>
            <td>
                <ul>
                    @foreach($entity->timetables as $timetable)
                    <!-- print the name of the semester via the semester ID -->
                    <li>
                        <a href="{{ url('timetables/show/'.$timetable->id) }}">{{ $timetable->semester->name }}</a>
                    </li>
                    @endforeach
                </ul>
            </td>
        </tr>
        @endif
    </tbody>
</table>
<a href="{{url('lecturers')}}" class="btn btn-danger">Zurück</a>
@endsection