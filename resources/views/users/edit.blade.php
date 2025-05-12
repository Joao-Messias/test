@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Editar Usuário') }}</span>
                    <a href="{{ route('users.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('users.update', $user->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Nome') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                                    name="name" value="{{ old('name', $user->name) }}" required autocomplete="name" autofocus>
                                <small class="form-text text-muted">Mínimo de 3 caracteres, máximo de 200</small>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('E-mail') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                    name="email" value="{{ old('email', $user->email) }}" required autocomplete="email">
                                <small class="form-text text-muted">Máximo de 200 caracteres</small>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Nova Senha') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                    name="password" autocomplete="new-password">
                                <small class="form-text text-muted">
                                    Deixe em branco para manter a senha atual. Mínimo de 8 caracteres, incluindo letras maiúsculas, minúsculas, números e símbolos
                                </small>

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="status" class="col-md-4 col-form-label text-md-end">{{ __('Status') }}</label>

                            <div class="col-md-6 d-flex align-items-center">
                                <div class="form-check form-switch mb-0">
                                    <input type="hidden" name="status" value="inactive">
                                    <input class="form-check-input @error('status') is-invalid @enderror" 
                                        type="checkbox" name="status" id="status" value="active" 
                                        {{ $user->status ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status" id="statusLabel">
                                        {{ $user->status ? 'Ativo' : 'Inativo' }}
                                    </label>
                                </div>

                                @error('status')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Atualizar') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Atualiza o texto do status quando o toggle é alterado
    $('#status').on('change', function() {
        const statusLabel = $('#statusLabel');
        if ($(this).is(':checked')) {
            statusLabel.text('Ativo');
            $('input[name="status"]').val('active');
        } else {
            statusLabel.text('Inativo');
            $('input[name="status"]').val('inactive');
        }
    });
});
</script>
@endpush
@endsection 