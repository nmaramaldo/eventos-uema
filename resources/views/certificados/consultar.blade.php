@extends('layouts.app') 
{{-- Se o seu layout principal tiver menu de login, talvez prefira criar um layout 'guest' ou usar um HTML simples se quiser esconder o topo --}}

@section('title', 'Validar Certificado')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5 text-center">
                    
                    <div class="mb-4 text-primary">
                        {{-- Ícone de selo/certificado --}}
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-patch-check" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M10.354 6.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7 8.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
                            <path d="M10.067.87a2.89 2.89 0 0 0-4.134 0l-.622.638-.896.011a2.89 2.89 0 0 0-2.924 2.924l.01.896-.636.622a2.89 2.89 0 0 0 0 4.134l.638.622-.011.896a2.89 2.89 0 0 0 2.924 2.924l.896-.01.622.636a2.89 2.89 0 0 0 4.134 0l.622-.638.896-.011a2.89 2.89 0 0 0 2.924-2.924l-.01-.896.636-.622a2.89 2.89 0 0 0 0-4.134l-.638-.622.011-.896a2.89 2.89 0 0 0-2.924-2.924l-.896.01-.622-.636zM8 4c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995A.905.905 0 0 1 8 4zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                        </svg>
                    </div>

                    <h2 class="mb-3">Validar Certificado</h2>
                    <p class="text-muted mb-4">
                        Digite o código de verificação presente no certificado para confirmar sua autenticidade.
                    </p>

                    <form action="{{ route('certificados.buscar') }}" method="POST">
                        @csrf
                        <div class="mb-3 text-start">
                            <label for="hash" class="form-label fw-bold small text-uppercase">Código do Certificado</label>
                            <input type="text" name="hash" id="hash" 
                                   class="form-control form-control-lg" 
                                   placeholder="Ex: a1b2-c3d4-e5f6..." required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                            Verificar Autenticidade
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection