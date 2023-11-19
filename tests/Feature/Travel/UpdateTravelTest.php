<?php

use App\Models\Role;
use App\Models\User;
use App\Models\Travel;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\patchJson;
use function Pest\Laravel\postJson;
use function PHPUnit\Framework\assertCount;

beforeEach(function () {
    $adminRole = Role::factory()->create(['name' => Role::ADMIN]);
    $editorRole = Role::factory()->create(['name' => Role::EDITOR]);

    $this->admin = User::factory()->hasAttached(Role::all())->create();
    $this->editor = User::factory()->hasAttached($editorRole)->create();
});

test('authorized users can update an existing travel', function ($roleName) {
    $travel = Travel::factory()->create();
    assertCount(1, Travel::all());

    Sanctum::actingAs(
        $this->$roleName
    );

    $response = patchJson(
        route('api.v1.travels.update', $travel),
        [
            'isPublic' => false,
            "name" => "changed-name",
        ]
    )
        ->assertOk()
        ->assertJsonStructure([
            'id',
            'slug',
            'name',
            'description',
            'numberOfDays',
            'moods'
        ]);

    assertCount(1, Travel::all());
    assertDatabaseHas(
        'travels',
        [
            'isPublic' => 0,
            "name" => "changed-name",
        ]
    );
})->with([
    'editor',
    'admin',
]);
