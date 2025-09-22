@if(session('success'))
  <div class="alert alert-success" role="alert" style="margin-top:15px;">
    {{ session('success') }}
  </div>
@endif

@if(session('error'))
  <div class="alert alert-danger" role="alert" style="margin-top:15px;">
    {{ session('error') }}
  </div>
@endif

@if ($errors->any())
  <div class="alert alert-danger" role="alert" style="margin-top:15px;">
    <strong>Ops!</strong> Verifique os erros abaixo.
    <ul style="margin:10px 0 0 18px;">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif
