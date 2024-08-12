<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $registeredUser = Role::findOrCreate('registered_user');
        $contributor = Role::findOrCreate('contributor');
        $associateEditor = Role::findOrCreate('associate_editor');
        $managingEditor = Role::findOrCreate('managing_editor');
        $admin = Role::findOrCreate('admin');

        $createUser = Permission::findOrCreate('create user');
        $readUser = Permission::findOrCreate('read user');
        $editUser = Permission::findOrCreate('edit user');
        $deleteUser = Permission::findOrCreate('delete user');

        $createRecord = Permission::findOrCreate('create record');
        $readRecord = Permission::findOrCreate('read record');
        $editRecord = Permission::findOrCreate('edit record');
        $deleteRecord = Permission::findOrCreate('delete record');
        $publishRecord = Permission::findOrCreate('publish record');
        $commentRecord = Permission::findOrCreate('comment record');

        $createComment = Permission::findOrCreate('create comment');
        $readComment = Permission::findOrCreate('read comment');
        $editComment = Permission::findOrCreate('edit comment');
        $deleteComment = Permission::findOrCreate('delete comment');

        $suggestTerm = Permission::findOrCreate('suggest term');
        $approveTerm = Permission::findOrCreate('approve term');
        $rejectTerm = Permission::findOrCreate('reject term');

        $registeredUser->givePermissionTo($commentRecord);

        $contributor->givePermissionTo($createRecord)
            ->givePermissionTo($editRecord)
            ->givePermissionTo($suggestTerm);

        $associateEditor->givePermissionTo($deleteComment);

        $managingEditor->givePermissionTo($approveTerm)
            ->givePermissionTo($rejectTerm)
            ->givePermissionTo($createUser)
            ->givePermissionTo($editUser)
            ->givePermissionTo($deleteUser)
            ->givePermissionTo($publishRecord);

        $rafaelSchwemmer = User::where('email', 'rafael.schwemmer@textandbytes.com')->first();
        if($rafaelSchwemmer) {
            $rafaelSchwemmer->assignRole($admin);
        }

        $douglasKim = User::where('email', 'dougkim@15solutions.com')->first();
        if($douglasKim) {
            $douglasKim->assignRole($admin);
        }

        $lukasMaerki = User::where('email', 'lukas@inventic.ch')->first();
        if($lukasMaerki) {
            $lukasMaerki->assignRole($admin);
        }

        $dawnChildress = User::where('email', 'dchildress@library.ucla.edu')->first();
        if($dawnChildress) {
            $dawnChildress->assignRole($managingEditor);
        }

        $williamPotter = User::where('email', 'williampotter@library.ucla.edu')->first();
        if($williamPotter) {
            $williamPotter->assignRole($managingEditor);
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        // Even though there is a findByName method, we will findOrCreate the role
        // since an error will be automatically be thrown, if the role isn't found
        // and would stop the processing of the deletion of the roles.

        $createUser = Permission::findOrCreate('create user');
        $createUser->delete();

        $readUser = Permission::findOrCreate('read user');
        $readUser->delete();

        $editUser = Permission::findOrCreate('edit user');
        $editUser->delete();

        $deleteUser = Permission::findOrCreate('delete user');
        $deleteUser->delete();

        $createRecord = Permission::findOrCreate('create record');
        $createRecord->delete();

        $readRecord = Permission::findOrCreate('read record');
        $readRecord->delete();

        $editRecord = Permission::findOrCreate('edit record');
        $editRecord->delete();

        $deleteRecord = Permission::findOrCreate('delete record');
        $deleteRecord->delete();

        $publishRecord = Permission::findOrCreate('publish record');
        $publishRecord->delete();

        $commentRecord = Permission::findOrCreate('comment record');
        $commentRecord->delete();

        $createComment = Permission::findOrCreate('create comment');
        $createComment->delete();

        $readComment = Permission::findOrCreate('read comment');
        $readComment->delete();

        $editComment = Permission::findOrCreate('edit comment');
        $editComment->delete();

        $deleteComment = Permission::findOrCreate('delete comment');
        $deleteComment->delete();

        $suggestTerm = Permission::findOrCreate('suggest term');
        $suggestTerm->delete();

        $approveTerm = Permission::findOrCreate('approve term');
        $approveTerm->delete();

        $rejectTerm = Permission::findOrCreate('reject term');
        $rejectTerm->delete();

        $registeredUser = Role::findOrCreate('registered_user');
        $registeredUser->delete();

        $contributor = Role::findOrCreate('contributor');
        $contributor->delete();

        $associateEditor = Role::findOrCreate('associate_editor');
        $associateEditor->delete();

        $managingEditor = Role::findOrCreate('managing_editor');
        $managingEditor->delete();

        $admin = Role::findOrCreate('admin');
        $admin->delete();


    }
};
