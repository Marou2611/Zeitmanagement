@extends('layouts.layout')

@section('content')
<h1 class="my-4">Ausgewähltes Semester: {{ $entity->name}}</h1>
<table class="table table-striped">
    <tbody>
        <tr>
            <td>Kurzbezeichnung</td>
            <td>{{ $entity->name }}</td>
        </tr>
        <tr>
            <td>Startdatum</td>
            <td>{{ \Carbon\Carbon::parse($entity->start_date)->format('d.m.Y') }}</td>
        </tr>
        <tr>
            <td>Enddatum</td>
            <td>{{ \Carbon\Carbon::parse($entity->end_date)->format('d.m.Y') }}</td>
        </tr>
        <tr>
            <td>Erstellt am</td>
            <td>{{ $entity->created_at }}</td>
        </tr>
    </tbody>
</table>
<a href="{{url('semesters')}}" class="btn btn-danger">Zurück</a>
@endsection