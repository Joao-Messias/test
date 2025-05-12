<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Actions\User\CreateUserAction;
use App\Actions\User\UpdateUserAction;
use App\Actions\User\DeleteUserAction;

class UserController extends Controller
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
     * Exibe a lista de usuários.
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);
        $users = User::all();
        return view('users.index', compact('users'));
    }

    /**
     * Exibe o formulário de criação de usuário.
     */
    public function create()
    {
        $this->authorize('create', User::class);
        return view('users.create');
    }

    /**
     * Armazena um novo usuário.
     */
    public function store(StoreUserRequest $request, CreateUserAction $action)
    {
        $this->authorize('create', User::class);
        
        $action->execute($request->validated());

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuário criado com sucesso!');
    }

    /**
     * Exibe o formulário de edição de usuário.
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    /**
     * Atualiza um usuário existente.
     */
    public function update(UpdateUserRequest $request, User $user, UpdateUserAction $action)
    {
        $this->authorize('update', $user);
        
        $action->execute($user, $request->validated());

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Remove um usuário.
     */
    public function destroy(User $user, DeleteUserAction $action)
    {
        $this->authorize('delete', $user);
        
        try {
            $action->execute($user);
            return redirect()
                ->route('users.index')
                ->with('success', 'Usuário excluído com sucesso!');
        } catch (\Exception $e) {
            return redirect()
                ->route('users.index')
                ->with('error', $e->getMessage());
        }
    }
} 