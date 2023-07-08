<?php

namespace App;

trait HasRoles
{
    public function role()
    {
        return $this->hasOne(Role::class, 'id', 'role_id' );
    }

    public function assignRole(Role $role)
    {
        $this->role_id = $role->id;
        return $this->save;
    }

    public function hasRole($roles)
    {
        if (is_string($roles)) 
        {
            return $this->role->contains('name', $roles);
        }
        $result = false;
        foreach($roles as $role)
        {
            if($this->role_id == $role->id)
                $result = true;
        }
        return $result;
    }
}
