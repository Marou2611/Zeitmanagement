@extends('layouts.layout')

@section('content')
<h1 class="my-4">Dozent:innen importieren</h1>

@include('snippets.error')

<div class="card mb-4">
    <div class="card-header">
        So funktioniert's
    </div>
    <div class="card-body">
        <p>Die Datei muss folgendes Format haben:</p>
        <ul>
            <li><strong>firstname:</strong> Vorname</li>
            <li><strong>lastname:</strong> Nachname</li>
            <li><strong>email:</strong> E-Mail-Adresse (wird verwendet, um Duplikate zu vermeiden)</li>
            <li><strong>phone:</strong> Telefonnummer</li>
        </ul>
        <p>Die Reihenfolge der Spalten spielt keine Rolle. Importierte Dozent:innen sind standardmäßig aktiviert.<br>
            Sollten Dozent:innen bereits existieren, werden diese nicht erneut importiert.
        </p>
    </div>
</div>

<!-- CSV-Upload-Formular -->
<form method="POST" action="{{ url('lecturers/import') }}" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label for="csv_file" class="form-label">CSV-Datei</label>
        <input type="file" name="csv_file" class="form-control" id="csv_file" accept=".csv" required>
    </div>
    <button type="submit" class="btn btn-primary">Importieren</button>
</form>
@endsection