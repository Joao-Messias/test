<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'status' => 'boolean',
        'is_admin' => 'boolean',
    ];

    /**
     * Obtém as tarefas criadas pelo usuário.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'owner_id');
    }

    /**
     * Obtém as tarefas atribuídas ao usuário.
     */
    public function assignedTasks()
    {
        return $this->belongsToMany(Task::class)
            ->withTimestamps();
    }
}