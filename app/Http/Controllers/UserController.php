<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // authorize
        if ($request->user()->cannot('create', User::class)) {
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        // validate
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required',  'string', 'min:8', 'max:255'],
            'roles' => ['required', 'array', Rule::in(Role::ADMIN, Role::EDITOR)],
        ]);

        // save
        DB::beginTransaction();

        /** @var User */
        $user = User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        // ensure admin also has editor role
        $roles = collect($request->roles)->contains(Role::ADMIN)
            ? [Role::ADMIN, Role::EDITOR]
            : $request->roles;

        $user->roles()->sync(Role::whereIn('name', $roles)->get());
        $token = $user->createToken('consumer-token')->plainTextToken;

        DB::commit();

        // response
        return response()->json(
            [
                'user' => new UserResource($user),
                'token' => $token,
            ],
            Response::HTTP_CREATED,
            // [
            //     'Location' => route('users.show', ['id' => $user->id])
            // ]
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
