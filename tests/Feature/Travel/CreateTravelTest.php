<?php

use App\Models\Role;
use App\Models\Travel;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;
use function PHPUnit\Framework\assertCount;

beforeEach(function () {
    $adminRole = Role::factory()->create(['name' => Role::ADMIN]);
    $editorRole = Role::factory()->create(['name' => Role::EDITOR]);

    $this->admin = User::factory()->hasAttached(Role::all())->create();
    $this->editor = User::factory()->hasAttached($editorRole)->create();
});

test('authorized users can create new travels', function () {
    assertCount(0, Travel::all());

    Sanctum::actingAs(
        $this->admin
    );

    $response = postJson(
        route('api.v1.travels.store'),
        [
            'isPublic' => true,
            'slug' => 'united-arab-emirates',
            'name' => 'United Arab Emirates => from Dubai to Abu Dhabi',
            'description' => 'At Dubai and Abu Dhabi',
            'numberOfDays' => 7,
            'moods' => [
                'nature' => 30,
                'relax' => 40,
                'history' => 20,
                'culture' => 80,
                'party' => 70,
            ],
        ]
    )->assertCreated();

    assertCount(1, Travel::all());
    assertDatabaseHas(
        'travels',
        [
            'isPublic' => 1,
            'slug' => 'united-arab-emirates',
            'name' => 'United Arab Emirates => from Dubai to Abu Dhabi',
            'description' => 'At Dubai and Abu Dhabi',
            'numberOfDays' => 7,
        ]
    );
});

test('unauthorized users cannot create new travels', function () {
    assertCount(0, Travel::all());

    Sanctum::actingAs(
        $this->editor
    );

    $response = postJson(
        route('api.v1.travels.store'),
        []
    )->assertForbidden();

    assertCount(0, Travel::all());
});
