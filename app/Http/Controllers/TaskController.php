<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\AddUserToTaskRequest;
use App\Http\Requests\RemoveUserFromTaskRequest;
use App\Actions\Task\CreateTaskAction;
use App\Actions\Task\UpdateTaskAction;
use App\Actions\Task\DeleteTaskAction;
use App\Actions\Task\AddUserToTaskAction;
use App\Actions\Task\RemoveUserFromTaskAction;
use App\Actions\Task\CompleteTaskAction;

class TaskController extends Controller
{
    /**
     * Construtor do controller.
     * Aplica o middleware de autenticação em todos os métodos.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Exibe a lista de tarefas.
     */
    public function index(Request $request)
    {
        $query = Task::with(['user', 'assignedUsers']);
        
        if ($request->filled('user_id')) {
            $query->where('owner_id', $request->user_id);
        }

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('assigned_user_id')) {
            $query->whereHas('assignedUsers', function ($q) use ($request) {
                $q->where('user_id', $request->assigned_user_id);
            });
        }

        $tasks = $query->get();
        $users = User::all();

        return view('tasks.index', compact('tasks', 'users'));
    }

    /**
     * Exibe o formulário de criação de tarefa.
     */
    public function create()
    {
        $users = User::all();
        return view('tasks.create', compact('users'));
    }

    /**
     * Armazena uma nova tarefa.
     */
    public function store(StoreTaskRequest $request, CreateTaskAction $action)
    {
        $action->execute($request->validated(), auth()->id());

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Tarefa criada com sucesso!');
    }

    /**
     * Exibe o formulário de edição de tarefa.
     */
    public function edit(Task $task)
    {
        $users = User::all();
        return view('tasks.edit', compact('task', 'users'));
    }

    /**
     * Atualiza uma tarefa existente.
     */
    public function update(UpdateTaskRequest $request, Task $task, UpdateTaskAction $action)
    {
        $action->execute($task, $request->validated());

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Tarefa atualizada com sucesso!');
    }

    /**
     * Remove uma tarefa.
     */
    public function destroy(Task $task, DeleteTaskAction $action)
    {
        try {
            $action->execute($task);
            return redirect()
                ->route('tasks.index')
                ->with('success', 'Tarefa excluída com sucesso!');
        } catch (\Exception $e) {
            return redirect()
                ->route('tasks.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Adiciona um usuário à tarefa.
     */
    public function addUser(AddUserToTaskRequest $request, Task $task, AddUserToTaskAction $action)
    {
        $action->execute($task, $request->validated('user_id'));

        return redirect()
            ->back()
            ->with('success', 'Usuário adicionado à tarefa com sucesso!');
    }

    /**
     * Remove um usuário da tarefa.
     */
    public function removeUser(RemoveUserFromTaskRequest $request, Task $task, RemoveUserFromTaskAction $action)
    {
        if (!$task->assignedUsers()->where('user_id', $request->validated('user_id'))->exists()) {
            return back()->withErrors(['user_id' => 'Este usuário não está atribuído à tarefa.']);
        }

        $action->execute($task, $request->validated('user_id'));

        return back()->with('success', 'Usuário removido da tarefa com sucesso!');
    }

    /**
     * Marca uma tarefa como concluída.
     */
    public function complete(Task $task, CompleteTaskAction $action)
    {
        $action->execute($task);

        return redirect()
            ->back()
            ->with('success', 'Tarefa marcada como concluída com sucesso!');
    }
} 