<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdateUserAction
{
    /**
     * Executa a ação de atualizar um usuário existente
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function execute(User $user, array $data): User
    {
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'status' => $data['status'] === 'active',
        ];

        if (!empty($data['password'])) {
            $userData['password'] = Hash::make($data['password']);
        }

        $user->update($userData);

        return $user;
    }
} 