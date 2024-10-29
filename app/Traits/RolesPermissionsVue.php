<?php

namespace App\Traits;

trait RolesPermissionsVue
{
    public function getRolesPermissions()
    {
        return [
            'roles'       => $this->getRoleNames(),
            'permissions' => $this->getAllPermissions()->pluck('name'),
        ];
    }
}