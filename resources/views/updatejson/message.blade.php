@extends('layouts.app')
@section('content')
    <div class="container-main">
      <div class="subcontainer-main">
        <h3>
            {{ $message }}
        </h3>
        <a href="/" class="btn-form btn-return">Regresar</a>
       </div>
    </div>
@endsection