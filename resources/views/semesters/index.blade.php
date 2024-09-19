@extends('layouts.layout')

@section('content')
<div class="card my-3">
    <div class="card-header">
        Stundenplanverwaltung
    </div>
    <div class="card-body">
        <h5 class="card-title">So funktionierts:</h5>
        <p class="card-text">Semester anlegen > Durchlauf starten > Bestätigen</p>
    </div>
</div>
<h1>Alle Semester</h1>

@include('snippets.error')

{{ $entities->links() }}
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th class="text-end">Bearbeiten</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entities as $index=>$semester)
            <tr>
                <td>{{ $semester->name }}</td>
                <td class="text-end">
                    <div class="btn-group">
                        <a href="{{url('semesters/show/'.$semester->id)}}" class="btn btn-info"><i class="fa fa-eye"></i></a>
                        <a href="{{url('semesters/edit/'.$semester->id)}}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                        <a href="{{url('semesters/timetables/'.$semester->id)}}" class="btn btn-success"><i class="fa fa-list"></i></a>
                        <a href="{{url('semesters/delete/'.$semester->id)}}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </div>
                    <!-- Durchlauf starten button -->
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#startRunModal" data-url="{{url('semesters/run/'.$semester->id)}}">
                        <i class="fa fa-play"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td></td>
                <td class="text-end">
                    <a href="{{url('semesters/add')}}" class="btn btn-success"><i class="fa fa-add"></i></a>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
{{ $entities->links() }}

<!-- Modal für Durchlauf starten -->
<div class="modal fade" id="startRunModal" tabindex="-1" aria-labelledby="startRunModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="startRunModalLabel">Durchlauf starten?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Möchtest du den Durchlauf <b>wirklich starten?</b> Hierbei werden alle Dozenten benachrichtigt und können ihre Stundenpläne einreichen.<br>
                Dieser Schritt kann nicht rückgängig gemacht werden.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                <a href="#" id="confirmStartRun" class="btn btn-danger">Ja, starten</a>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.getElementById('startRunModal').addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget; // Button that triggered the modal
        var url = button.getAttribute('data-url'); // Extract the URL from the data-* attributes
        var confirmButton = document.getElementById('confirmStartRun');
        confirmButton.setAttribute('href', url); // Set the URL on the confirm button
    });
</script>
@endsection