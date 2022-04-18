@if($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first() }}
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">
        {{ session()->get('success') }}
    </div>
@endif
