@if($errors->any())
<div class="alert alert-warning" role="alert">
    <b>Hoppla!</b> Es gab ein Problem mit deiner Eingabe.
</div>
<ul>
    @foreach($errors->all() as $error)
    <li>{{$error}}</li>
    @endforeach
</ul>
@endif

<!-- Print error message for one line -->
@if(session('error'))
<div class="alert alert-danger" role="alert">
    <b>Hoppla!</b> {{ session('error') }}
</div>
@endif

<!-- Print success message for one line -->
@if(session('success'))
<div class="alert alert-success" role="alert">
    {{ session('success') }}
</div>
@endif

<!-- Print warning message for one line -->
@if(session('warning'))
<div class="alert alert-warning" role="alert">
    <b>Hoppla!</b> {{ session('warning') }}
</div>
@endif