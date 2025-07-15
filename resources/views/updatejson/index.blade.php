@extends('layouts.app')
@section('content')
    <div class="container-main">
      <div class="subcontainer-main">
          <h3 class="message-error">
            {{ $message }}
          </h3>
          <div>
            <h3>Actualización de archivos</h3>
          </div>
          <div>
            <form action="{{ url('upload-folder') }}" method="POST" enctype="multipart/form-data">
                @csrf
              <p>Adjunte la carpeta que contiene los archivos a modificar en formato ZIP.</p>
              <div>
                  <label for="folder">Selecione la carpeta (en formato ZIP):</label>
                  <input type="file" name="folder" id="folder" accept=".zip" required>
              </div>
              <button type="submit" class="btn-form" onclick="viewLoader()">Subir Carpeta</button>
              <div class="loader" id="loader"></div>
          </div>
      </div>
  </div>
  <script>
    window.addEventListener( "pageshow", function ( event ) {
      var historyTraversal = event.persisted || ( typeof window.performance != "undefined" && window.performance.navigation.type === 2 );
      if ( historyTraversal ) {
        window.location.reload();
        <?php $message = ''; ?>
      }
    });
    function viewLoader(params) {
      setTimeout(function(){
        document.getElementById("btn-form").disabled = true;
        document.getElementById("btn-form").style.backgroundColor="#507ba1";
      }, 1000);
      document.getElementById("loader").style.display="block";
    }
  </script>
@endsection