<?php

use App\Models\Role;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertTrue;

beforeEach(function () {
    $adminRole = Role::factory()->create(['name' => Role::ADMIN]);
    $editorRole = Role::factory()->create(['name' => Role::EDITOR]);

    $this->admin = User::factory()->hasAttached(Role::all())->create();
    $this->editor = User::factory()->hasAttached($editorRole)->create();
});

test('authorized users can create new users', function () {
    assertCount(2, User::all());

    Sanctum::actingAs($this->admin);

    $response = postJson(
        route('api.v1.users.store'),
        [
            'email' => 'foo@mail.com',
            'password' => 'password',
            'roles' => [
                Role::ADMIN, Role::EDITOR,
            ],
        ]
    )->assertCreated();

    assertCount(3, User::all());
    assertDatabaseHas('users', [
        'email' => 'foo@mail.com',
    ]);

    tap(User::where('email', 'foo@mail.com')->first(), function ($user) {
        assertTrue($user->hasRole(Role::ADMIN));
        assertTrue($user->hasRole(Role::EDITOR));
    });
});

test('unauthorized users cannot create new users', function () {
    assertCount(2, User::all());

    Sanctum::actingAs($this->editor);

    $response = postJson(
        route('api.v1.users.store'),
        []
    )->assertForbidden();

    assertCount(2, User::all());
});
