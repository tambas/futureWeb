<?php

namespace App\Policies;

use App\Task;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;

class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    public function update(User $user, Task $task)
    {
        return $user->id == $task->user_id;
    }
    public function destroy(User $user, Task $task)
    {
        return $user->id == $task->user_id;
    }
}
