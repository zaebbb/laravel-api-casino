<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserCRUDController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $authRequest = $request->header('authorization');
        $findAuthBearer = User::where("remember_token", '=', "$authRequest")->get();
        if(count($findAuthBearer) == 0){
            return response([
                'message' => "Доступ запрещен"
            ],403);
        }

        $allUsers = User::all();

        if(count($allUsers) == 0){
            return response([
                'users_all' => "Пользователи отсуствуют"
            ], 404);
        }

        return response([
            'users' => [
                $allUsers
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function store(Request $request)
    {

        $authRequest = $request->header('authorization');
        $findAuthBearer = User::where("remember_token", '=', "$authRequest")->get();
        if(count($findAuthBearer) == 0){
            return response([
                "message" => "Доступ запрещен"
            ], 403);
        }

        $userName = $request->name;
        $userEmail = $request->email;
        $userPassword = $request->password;

        $errors = [];

        if(empty($userName)){
            array_push($errors, array("name_required" => "Поле имя обязательно к заполнению"));
        }
        if(empty($userEmail)){
            array_push($errors, array("email_required" => "Поле почта обязательно к заполнению"));
        }
        if(empty($userPassword)){
            array_push($errors, array("password_required" => "Поле пароль обязательно к заполнению"));
        }
        $userSearchName = User::where('name', '=', "$userName")->get();
        $userSearchEmail = User::where('email', '=', "$userEmail")->get();

        if(count($userSearchName) !== 0){
            array_push($errors, array("name_fail" => "Пользователь с таким именем уже зарегистрирован"));
        }
        if(count($userSearchEmail) !== 0){
            array_push($errors, array("email_fail" => "Пользователь с такой почтой уже зарегистрирован"));
        }

        if(count($errors) !== 0){
            return response([
                'bad request' => true,
                'errors' => [
                    $errors
                ]
            ], 401);
        }


        $newUser = User::create([
            'name' => $userName,
            'email' => $userEmail,
            'password' => $userPassword
        ]);

        $idUser = User::where('name', '=', "$userName")->get();

        return response([
            "complete create user" => true,
            "user_id" => [$idUser[0]["id"]]
        ],201);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param \App\Models\User $user
     * @param $id
     * @return Response
     */
    public function show(Request $request, User $user, $id)
    {
        $authRequest = $request->header('authorization');
        $searchToken = User::where('remember_token', '=', "$authRequest")->get();
        if(count($searchToken) == 0){
            return response([
                'message' => "Доступ запрещен"
            ],403);
        }

        $userSearch = User::find($id);
        if($userSearch == null){
            return response([
                'users_error' => "Пользователь не обнаружен"
            ], 404);
        }

        return response([
            'users_search' => "Пользователь обнаружен",
            'user' => [
                $userSearch
            ]
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return Response
     */
    public function update(Request $request, User $user, $id)
    {
        $userSearch = User::find($id);

        if($userSearch == null){
            return response(
                [
                    'not found' => 'Пользователь не обнаружен'
                ], 404
            );
        }

        $userName = $request->name;
        $userEmail = $request->email;
        $userPassword = $request->password;

        $errors = [];

        if(empty($userName)){
            array_push($errors, array("name_required" => "Поле имя обязательно к заполнению"));
        }
        if(empty($userEmail)){
            array_push($errors, array("email_required" => "Поле почта обязательно к заполнению"));
        }
        if(empty($userPassword)) {
            array_push($errors, array("password_required" => "Поле пароль обязательно к заполнению"));
        }

        if(count($errors) !== 0){
            return response([
                "bad request" => true,
                'errors' =>[
                    $errors
                ]
            ], 401);
        }

        $userSearch->update([
            'name' => $userName,
            'email' => $userEmail,
            'password' => $userPassword
        ]);

        return response([
            'update complete' => true,
            'data' => [
                $userSearch
            ]
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return Response
     */
    public function destroy(User $user, $id)
    {
        $userSearch = User::find($id);

        if($userSearch == null){
            return response([
                'delete false' => true,
                'message' => "Пользователь не обнаружен"
            ], 400);
        }

        $userSearch->delete();

        return response([
            'delete complete' => true,
            'Пользователь удален'
        ], 201);
    }
}
