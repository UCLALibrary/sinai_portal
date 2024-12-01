<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $frontendUser = Role::findOrCreate('Frontend user');
        $editor = Role::findOrCreate('Editor');
        $admin = Role::findOrCreate('Admin');

        $createUser = Permission::findOrCreate('create user');
        $readUser = Permission::findOrCreate('read user');
        $updateUser = Permission::findOrCreate('update user');
        $deleteUser = Permission::findOrCreate('delete user');

        $createRecord = Permission::findOrCreate('create record');
        $readRecord = Permission::findOrCreate('read record');
        $updateRecord = Permission::findOrCreate('update record');
        $deleteRecord = Permission::findOrCreate('delete record');
        $uploadRecord = Permission::findOrCreate('upload record');
        $downloadRecords = Permission::findOrCreate('download records');

        $viewCms = Permission::findOrCreate('view cms');  

        $frontendUser->givePermissionTo($downloadRecords);

        $editor->givePermissionTo($createRecord)
            ->givePermissionTo($readRecord)
            ->givePermissionTo($updateRecord)
            ->givePermissionTo($deleteRecord)
            ->givePermissionTo($downloadRecords)
            ->givePermissionTo($viewCms);

        $admin->givePermissionTo($createRecord)
            ->givePermissionTo($readRecord)
            ->givePermissionTo($updateRecord)
            ->givePermissionTo($deleteRecord)
            ->givePermissionTo($uploadRecord)
            ->givePermissionTo($downloadRecords)
            ->givePermissionTo($createUser)
            ->givePermissionTo($readUser)
            ->givePermissionTo($updateUser)
            ->givePermissionTo($deleteUser)
            ->givePermissionTo($viewCms);

        $rafaelSchwemmer = User::where('email', 'rafael.schwemmer@textandbytes.com')->first();
        if ($rafaelSchwemmer) {
            $rafaelSchwemmer->assignRole($admin);
        }

        $douglasKim = User::where('email', 'dougkim@15solutions.com')->first();
        if ($douglasKim) {
            $douglasKim->assignRole($admin);
        }

        $lukasMaerki = User::where('email', 'lukas@inventic.ch')->first();
        if ($lukasMaerki) {
            $lukasMaerki->assignRole($admin);
        }

        $dawnChildress = User::where('email', 'dchildress@library.ucla.edu')->first();
        if ($dawnChildress) {
            $dawnChildress->assignRole($admin);
        }

        $williamPotter = User::where('email', 'williampotter@library.ucla.edu')->first();
        if ($williamPotter) {
            $williamPotter->assignRole($admin);
        }
    }
}
