@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <i class="fas fa-home me-2"></i>
                    {{ __('Bem-vindo') }}
                </div>
                <div class="card-body">
                    <h5 class="card-title">Olá, {{ Auth::user()->name }}!</h5>
                    <p class="card-text">
                        Use o menu de navegação acima para acessar as funcionalidades do sistema.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
