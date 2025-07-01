@extends('layouts.app')
@section('content')
    <div class="container-main">
      <div class="subcontainer-main">
          <div>
            <h3>Actualización de archivos</h3>
          </div>
          <div>
            <form action="{{ url('update-json') }}" method="POST">
                @csrf
              <p>Agrega el nombre de la carpeta dentro de la unidad C, en el siguiente campo</p>
              <input type="text" name="urlfolder" id="urlfolder" class="input-form">
              <input type="submit" value="Actualizar archivos" name="submitbutton" id="btn-form" class="btn-form" onclick="viewLoader()">
              <div class="loader" id="loader"></div>
          </div>
      </div>
  </div>
  <script>
    window.addEventListener( "pageshow", function ( event ) {
      var historyTraversal = event.persisted || ( typeof window.performance != "undefined" && window.performance.navigation.type === 2 );
      if ( historyTraversal ) {
        window.location.reload();
      }
    });
    function viewLoader(params) {
      setTimeout(function(){
        document.getElementById("btn-form").disabled = true;
        document.getElementById("btn-form").style.backgroundColor="#507ba1";
      }, 1000);
      document.getElementById("loader").style.display="block";
    }
    function addDisplay(){
      document.getElementById("loader").style.display="none";
    }
  </script>
@endsection