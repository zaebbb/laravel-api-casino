<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class authController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $nameUser = $request->name;
        $namePassword = $request->password;

        $errors = [];

        if(empty($nameUser)){
            array_push($errors, array("name_required" => "Поле имя обязательно к заполнению"));
        }
        if(empty($namePassword)){
            array_push($errors, array("email_required" => "Поле почта обязательно к заполнению"));
        }

        if(count($errors) !== 0){
            return response([
                'bad request' => true,
                "errors" => [
                    $errors
                ]
            ], 401);
        }

        $userSearchName = User::where('name','=',"$nameUser")->get();

        if(count($userSearchName) == 0){
            return response([
                "auth error" => "неверное имя пользрователя или пароль"
            ], 401);
        }

        $userSearchPassword = $userSearchName->where('password','=',"$namePassword");

        if(count($userSearchPassword) == 0){
            return response([
                "auth error" => "неверное имя пользрователя или пароль"
            ], 401);
        }

        $randomStr = "Bearer " . Str::random(60) . $userSearchName[0]['id'];
        $searchUserResetToken = User::find($userSearchName[0]['id']);
        $searchUserResetToken->update(
            ['remember_token' => "$randomStr"]
        );

        return response([
            'authorization' => true,
            'token' => "$randomStr"
        ], 201)
            ->header('authorization',"$randomStr")
            ->cookie('authorization',"$randomStr",60);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return response([
            'message' => "Вы успешно вышли из системы"
        ], 201)
            ->header('authorization',"")
            ->cookie('authorization', "", 60 * (-3600));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
