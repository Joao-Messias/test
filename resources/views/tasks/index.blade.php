@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Tarefas') }}</span>
                    <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nova Tarefa
                    </a>
                </div>

                <div class="card-body">
                    <!-- Filtro por usuário -->
                    <div class="mb-3">
                        <form action="{{ route('tasks.index') }}" method="GET" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <input type="text" name="title" class="form-control" placeholder="Pesquisar por título" value="{{ request('title') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-select">
                                    <option value="">Todos os status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendente</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Concluída</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="assigned_user_id" class="form-select">
                                    <option value="">Atribuída a qualquer pessoa</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('assigned_user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="user_id" class="form-select">
                                    <option value="" {{ !request('user_id') ? 'selected' : '' }}>Todas as tarefas</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Descrição</th>
                                    <th>Status</th>
                                    <th>Usuário</th>
                                    <th>Usuários Atribuídos</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tasks as $task)
                                    <tr>
                                        <td>{{ $task->title }}</td>
                                        <td>
                                            @if(strlen($task->description) > 50)
                                                <span title="{{ $task->description }}">
                                                    {{ Str::limit($task->description, 50, '...') }}
                                                </span>
                                            @else
                                                {{ $task->description }}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($task->status === 'completed')
                                                <span class="badge bg-success">Concluída</span>
                                            @else
                                                <span class="badge bg-warning">Pendente</span>
                                            @endif
                                        </td>
                                        <td>{{ $task->user->name }}</td>
                                        <td>
                                            @foreach($task->assignedUsers as $user)
                                                <span class="badge bg-secondary">{{ $user->name }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @if ($task->status !== 'completed')
                                                    <button type="button" 
                                                        class="btn btn-sm btn-success complete-task" 
                                                        title="Marcar como concluída"
                                                        data-task-id="{{ $task->id }}"
                                                        data-task-title="{{ $task->title }}">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif

                                                <a href="{{ route('tasks.edit', $task) }}" 
                                                    class="btn btn-sm btn-primary" 
                                                    title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <button type="button" 
                                                    class="btn btn-sm btn-danger delete-task" 
                                                    title="Excluir"
                                                    data-task-id="{{ $task->id }}"
                                                    data-task-title="{{ $task->title }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Nenhuma tarefa encontrada.</td>
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
    // Confirmação de exclusão com SweetAlert2
    $('.delete-task').on('click', function() {
        const button = $(this);
        const taskId = button.data('task-id');
        const taskTitle = button.data('task-title');
        
        Swal.fire({
            title: 'Tem certeza?',
            text: `Deseja realmente excluir a tarefa "${taskTitle}"?`,
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
                    'action': `/tasks/${taskId}`
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

    // Marcar tarefa como concluída
    $('.complete-task').on('click', function() {
        const button = $(this);
        const taskId = button.data('task-id');
        const taskTitle = button.data('task-title');
        
        Swal.fire({
            title: 'Confirmar conclusão',
            text: `Deseja marcar a tarefa "${taskTitle}" como concluída?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, concluir!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = $('<form>', {
                    'method': 'POST',
                    'action': `/tasks/${taskId}/complete`
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
                    'value': 'PATCH'
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