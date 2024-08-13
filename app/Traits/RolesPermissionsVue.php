<?php

namespace App\Traits;

trait RolesPermissionsVue
{
    public function getRolesPermissionsAsJson()
    {
        return json_encode([
            'roles'       => $this->getRoleNames(),
            'permissions' => $this->getAllPermissions()->pluck('name'),
        ]);
    }
}