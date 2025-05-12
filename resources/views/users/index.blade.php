@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Usuários') }}</span>
                    @can('create', App\Models\User::class)
                        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Novo Usuário
                        </a>
                    @endcan
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>E-mail</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if ($user->status)
                                                <span class="badge bg-success">Ativo</span>
                                            @else
                                                <span class="badge bg-danger">Inativo</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->id !== auth()->id())
                                                <div class="btn-group" role="group">
                                                    @can('update', $user)
                                                        <a href="{{ route('users.edit', $user) }}" 
                                                            class="btn btn-sm btn-primary" 
                                                            title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endcan

                                                    @can('delete', $user)
                                                        <button type="button" 
                                                            class="btn btn-sm btn-danger delete-user" 
                                                            title="Excluir"
                                                            data-user-id="{{ $user->id }}"
                                                            data-user-name="{{ $user->name }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endcan
                                                </div>
                                            @else
                                                <span class="text-muted">Seu perfil</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Nenhum usuário cadastrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('.delete-user').on('click', function() {
        const button = $(this);
        const userId = button.data('user-id');
        const userName = button.data('user-name');
        
        Swal.fire({
            title: 'Tem certeza?',
            text: `Deseja realmente excluir o usuário ${userName}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = $('<form>', {
                    'method': 'POST',
                    'action': `/users/${userId}`
                });

                const csrfToken = $('meta[name="csrf-token"]').attr('content');
                
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': csrfToken
                }));

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_method',
                    'value': 'DELETE'
                }));

                $('body').append(form);
                form.submit();
            }
        });
    });
});
</script>
@endpush
@endsection 