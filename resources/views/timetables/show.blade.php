@extends('layouts.layout')

@section('content')
<h1 class="my-4 h3"><b>{{ $entity->semester->name }}</b>: {{ $entity->lecturer->firstname }} {{ $entity->lecturer->lastname }}</h1>

<!-- Allgemeine Informationen -->
<div class="card my-3">
    <div class="card-header">
        Allgemeine Informationen
    </div>
    <div class="card-body">
        <p><strong>Lehrumfang in SWS:</strong> {{ $entity->teaching_load }}</p>
        <p><strong>Maximale SWS-Zahl pro Tag:</strong> {{ $entity->max_hours_per_day }}</p>
        <p><strong>Nutzung CN0003:</strong> {{ $entity->use_cn0003 ? 'Ja' : 'Nein' }}</p>
        <p><strong>Nutzung Gruppentischräume/Stuhlkreis:</strong> {{ $entity->use_group_rooms ? 'Ja' : 'Nein' }}</p>
        <p><strong>Anmerkungen:</strong> {{ $entity->comments }}</p>
    </div>
</div>

<!-- Bachelor Zeitpräferenzen -->
<div class="card my-3">
    <div class="card-header">
        Bachelor
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>Zeit</th>
                        <th>Montag</th>
                        <th>Dienstag</th>
                        <th>Mittwoch</th>
                        <th>Donnerstag</th>
                        <th>Freitag</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (['8:00 - 11:30', '12:00 - 15:45', '16:15 - 19:45'] as $time)
                    <tr>
                        <td>{{ $time }}</td>
                        @for($i = 0; $i < 5; $i++)
                            <td>
                            @php
                            $preference = $entity->bachelor[str_replace(':', '_', str_replace(' ', '', $time))][$i] ?? 'Keine Angabe';
                            @endphp
                            @if($preference == 'preferred')
                            <span class="badge bg-success">Bevorzugt</span>
                            @elseif($preference == 'neutral')
                            <span class="badge bg-secondary">Nachrangig</span>
                            @elseif($preference == 'not_possible')
                            <span class="badge bg-danger">Keinesfalls</span>
                            @else
                            <span class="badge bg-secondary">Keine Angabe</span>
                            @endif
                            </td>
                            @endfor
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Master Zeitpräferenzen -->
<div class="card my-3">
    <div class="card-header">
        Master
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>Zeit</th>
                        <th>Montag</th>
                        <th>Dienstag</th>
                        <th>Mittwoch</th>
                        <th>Donnerstag</th>
                        <th>Freitag</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (['8:00 - 10:30', '11:00 - 13:30', '14:15 - 16:45', '17:15 - 20:00'] as $time)
                    <tr>
                        <td>{{ $time }}</td>
                        @for($i = 0; $i < 5; $i++)
                            <td>
                            @php
                            $preference = $entity->master[str_replace(':', '_', str_replace(' ', '', $time))][$i] ?? 'Keine Angabe';
                            @endphp
                            @if($preference == 'preferred')
                            <span class="badge bg-success">Bevorzugt</span>
                            @elseif($preference == 'neutral')
                            <span class="badge bg-secondary">Nachrangig</span>
                            @elseif($preference == 'not_possible')
                            <span class="badge bg-danger">Keinesfalls</span>
                            @else
                            <span class="badge bg-secondary">Keine Angabe</span>
                            @endif
                            </td>
                            @endfor
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Button zur Rückkehr -->
<a href="{{ url()->previous() }}" class="btn btn-primary">Zurück</a>

@endsection