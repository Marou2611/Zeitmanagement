@extends('layouts.layouttimetable')

@section('content')
<h1 class="my-4 h3">Hallo {{ $lecturer->firstname }} {{ $lecturer->lastname }}!</h1>

<!-- Card mit allen wichtigen Infos -->
<div class="card mt-2 mb-4">
    <div class="card-body">
        <h5 class="card-title">Stundenplanerfassung für das Semester <b>{{ $semester->name }}</b></h5>
        <h6 class="card-subtitle mb-2 text-muted">Jetzt noch einfacher und schneller!</h6>
        <p class="card-text">
        <ul>
            <li>Trage deinen aktuellen Lehrumfang in SWS ein.</li>
            <li>Wähle die maximale Anzahl an SWS pro Tag.</li>
            <li>Entscheide, ob du CN0003 und Gruppentischräume/Stuhlkreis nutzen möchtest.</li>
            <li>Trage Anmerkungen ein, wenn du möchtest.</li>
            <li>Wähle für jede Zeit, ob du sie bevorzugst, neutral oder keinesfalls möchtest.</li>
            <li>Speichere deine Eingaben.</li>
        </ul>
        </p>
    </div>
</div>

@include('snippets.error')

<!-- Bedingte Anzeige des Schalters zum Laden der vorherigen Daten -->
@if(!empty($previousTimetable))
<div class="alert alert-info" role="alert">
    <b>Hinweis: </b>Du kannst einfach deinen Zeitplan aus dem letzten Semester übernehmen.
</div>
<div class="form-check form-switch mb-5">
    <input class="form-check-input" type="checkbox" id="usePreviousData" checked>
    <label class="form-check-label" for="usePreviousData">Zeiten aus letztem Semester übernehmen</label>
</div>
@endif

<form method="POST" action="{{ url('timetables/save') }}">
    @csrf
    <input type="hidden" name="semester_id" value="{{ $semester->id }}">
    <input type="hidden" name="lecturer_id" value="{{ $lecturer->id }}">

    <!-- Allgemeine Informationen -->
    <div class="row">
        <div class="col-md mb-3">
            <div class="form-floating">
                <input type="number" name="teaching_load" class="form-control" id="teaching_load" placeholder="Lehrumfang in SWS" value="{{ old('teaching_load', $previousTimetable->teaching_load ?? '') }}">
                <label for="teaching_load">Aktueller Lehrumfang in SWS</label>
            </div>
        </div>
        <div class="col-md mb-3">
            <div class="form-floating">
                <input type="number" name="max_hours_per_day" class="form-control" id="max_hours_per_day" placeholder="Maximale SWS-Zahl pro Tag" value="{{ old('max_hours_per_day', $previousTimetable->max_hours_per_day ?? '') }}">
                <label for="max_hours_per_day">Maximale SWS-Zahl pro Tag</label>
            </div>
        </div>
    </div>

    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="use_cn0003" id="use_cn0003" value="1" {{ old('use_cn0003', $previousTimetable->use_cn0003 ?? false) ? 'checked' : '' }}>
        <label class="form-check-label" for="use_cn0003">Nutzung CN0003 für Lehrveranstaltung</label>
    </div>

    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="use_group_rooms" id="use_group_rooms" value="1" {{ old('use_group_rooms', $previousTimetable->use_group_rooms ?? false) ? 'checked' : '' }}>
        <label class="form-check-label" for="use_group_rooms">Nutzung Gruppentischräume/Stuhlkreis für Lehrveranstaltung</label>
    </div>

    <div class="form-floating mb-5">
        <textarea name="comments" class="form-control" style="height: 8rem" id="comments" placeholder="Anmerkungen">{{ old('comments', $previousTimetable->comments ?? '') }}</textarea>
        <label for="comments">Anmerkungen</label>
    </div>

    <!-- Wochenkalender für Bachelorzeiten -->
    <h3>Bachelor Zeiten</h3>
    <div class="table-responsive mb-3">
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
                        <select name="bachelor[{{ str_replace(':', '_', str_replace(' ', '', $time)) }}][{{ $i }}]" class="form-select">
                            <option value="preferred" {{ old('bachelor.'.str_replace(':', '_', str_replace(' ', '', $time)).'.'.$i, $previousTimetable->bachelor[str_replace(':', '_', str_replace(' ', '', $time))][$i] ?? 'neutral') == 'preferred' ? 'selected' : '' }}>Bevorzugt</option>
                            <option value="neutral" {{ old('bachelor.'.str_replace(':', '_', str_replace(' ', '', $time)).'.'.$i, $previousTimetable->bachelor[str_replace(':', '_', str_replace(' ', '', $time))][$i] ?? 'neutral') == 'neutral' ? 'selected' : '' }}>Nachrangig</option>
                            <option value="not_possible" {{ old('bachelor.'.str_replace(':', '_', str_replace(' ', '', $time)).'.'.$i, $previousTimetable->bachelor[str_replace(':', '_', str_replace(' ', '', $time))][$i] ?? 'neutral') == 'not_possible' ? 'selected' : '' }}>Keinesfalls</option>
                        </select>
                        </td>
                        @endfor
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Wochenkalender für Masterzeiten -->
    <h3>Master Zeiten</h3>
    <div class="table-responsive mb-3">
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
                        <select name="master[{{ str_replace(':', '_', str_replace(' ', '', $time)) }}][{{ $i }}]" class="form-select">
                            <option value="preferred" {{ old('master.'.str_replace(':', '_', str_replace(' ', '', $time)).'.'.$i, $previousTimetable->master[str_replace(':', '_', str_replace(' ', '', $time))][$i] ?? 'neutral') == 'preferred' ? 'selected' : '' }}>Bevorzugt</option>
                            <option value="neutral" {{ old('master.'.str_replace(':', '_', str_replace(' ', '', $time)).'.'.$i, $previousTimetable->master[str_replace(':', '_', str_replace(' ', '', $time))][$i] ?? 'neutral') == 'neutral' ? 'selected' : '' }}>Nachrangig</option>
                            <option value="not_possible" {{ old('master.'.str_replace(':', '_', str_replace(' ', '', $time)).'.'.$i, $previousTimetable->master[str_replace(':', '_', str_replace(' ', '', $time))][$i] ?? 'neutral') == 'not_possible' ? 'selected' : '' }}>Keinesfalls</option>
                        </select>
                        </td>
                        @endfor
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <button type="submit" class="btn btn-success mb-5">Speichern</button>
</form>

@endsection

@section('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var usePreviousDataCheckbox = document.getElementById('usePreviousData');

        if (usePreviousDataCheckbox) {
            usePreviousDataCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    // reload page to load previous data
                    window.location.reload();
                } else {
                    document.querySelectorAll('select').forEach(function(select) {
                        select.value = 'neutral';
                    });

                    // reset input teaching_load
                    document.getElementById('teaching_load').value = '';

                    // reset input max_hours_per_day
                    document.getElementById('max_hours_per_day').value = '';

                    // reset input use_cn0003
                    document.getElementById('use_cn0003').checked = false;

                    // reset input use_group_rooms
                    document.getElementById('use_group_rooms').checked = false;

                    // reset input comments
                    document.getElementById('comments').value = '';

                }
            });
        }
    });
</script>

@endsection