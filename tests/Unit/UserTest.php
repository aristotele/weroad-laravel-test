<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertInstanceOf;

test('it may have many roles associated', function () {
    // Given a user
    $user = User::factory()->create();

    // and some roles
    $adminRole = Role::factory()->create(['name' => Role::ADMIN]);
    $editorRole = Role::factory()->create(['name' => Role::EDITOR]);

    DB::table('role_user')->insert([
        ['role_id' => $adminRole->id, 'user_id' => $user->id],
        ['role_id' => $editorRole->id, 'user_id' => $user->id],
    ]);

    // When calling relationships
    // Then instances of Roles are returned
    tap($user->roles, function ($roles) {
        assertInstanceOf(Collection::class, $roles);
        assertCount(2, $roles);
        assertInstanceOf(Role::class, $roles->first());
    });
});
