@extends('layouts.layout')

@section('content')
<h1 class="my-4">Alle Dozenten</h1>

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
            @foreach($entities as $index=>$lecturers)
            <tr>
                <td>{{ $lecturers->firstname }} {{ $lecturers->lastname }}</td>
                <td class="text-end">
                    <div class="btn-group">
                        <a href="{{url('lecturers/show/'.$lecturers->id)}}" class="btn btn-info"><i class="fa fa-eye"></i></a>
                        <a href="{{url('lecturers/edit/'.$lecturers->id)}}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                        <a href="{{url('lecturers/delete/'.$lecturers->id)}}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td></td>
                <td class="text-end">
                    <a href="{{url('lecturers/add')}}" class="btn btn-success"><i class="fa fa-add"></i></a>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
{{ $entities->links() }}
@endsection