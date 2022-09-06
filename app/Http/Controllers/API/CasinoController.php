<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Stavka;
use App\Models\User;
use Illuminate\Http\Request;

class CasinoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $allDataStavka = Stavka::all();
        if(count($allDataStavka) == 0){
            return response([
                'message' => 'Ставки не обнаружены'
            ],404);
        }
        return response([
            'message' => 'Найдены следующие ставки',
            'stavki' => [
                $allDataStavka
            ]
        ],200)->header("checkText", "obrabotochka");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $authRequest = $request->header('authorization');
//        $userAuthBearer = User::where('remember_token', '=', "$authRequest")->get();
//        if(count($userAuthBearer) == 0){
//            return response([
//                "message" => "Доступ запрещен"
//            ], 403);
//        }
        $userStavka = intval(substr($authRequest, 67));
        $userSearch = User::find($userStavka);
        $errors = [];
        if($userSearch == null){
            $userSearch = $request->name;

            if(empty($userSearch)){
                array_push($errors, array('user_required' => 'Поле имя обязательно к заполнению для гостя страницы'));
            }
        } else {
            $userSearch = $userSearch['name'];
        }
        $price = $request->price;
        $company = $request->company;
        $settings = $request->settings;

         if(gettype($settings) == "array"){
            $settings = "[" . implode(',', $settings) . "]";
        }

        if(empty($price)){
            array_push($errors, array('price_required' => 'Поле цена обязательно к заполнению'));
        }
        if(empty($company)){
            array_push($errors, array('company_required' => 'Поле компания обязательно к заполнению'));
        }
        if(empty($settings)){
            array_push($errors, array('settings_required' => 'Поле настройки обязательно к заполнению'));
        }
        if(count($errors) !== 0){
            return response([
                'send' => false,
                'errors' => [
                    $errors
                ]
            ],401);
        }

        $stavkaCreate = Stavka::create([
            'user' => $userSearch,
            'price' => $price,
            'company' => $company,
            'settings' => $settings,
        ]);

        return response([
            'message_status_send' => true,
            'stavka' => [
                $stavkaCreate
            ]
        ],201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Stavka  $stavka
     * @return \Illuminate\Http\Response
     */
    public function show(Stavka $stavka, $id)
    {
        $stavkaSearch = Stavka::find($id);
        if($stavkaSearch == null){
            return response([
                "search" => false,
                'message' => [
                    "text" => "ставка не найдена"
                ]
            ],404);
        }

        return response([
            'status_stavka' => true,
            'text' => "Ставка найдена",
            'message' => [
                $stavkaSearch
            ]
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Stavka  $stavka
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Stavka $stavka, $id)
    {
        $nameBearer = $request->header('authorization');
        $findStavka = Stavka::find($id);
        if($findStavka == null){
            return response([
                "status" => false,
                'message' => "Ставка не найдена"
            ], 404);
        }
        $nameBearer = intval(substr($nameBearer, 67));
        $searchUser = User::where('id', '=', "$nameBearer")->get();
        if(count($searchUser) == 0){
            return response([
                'message' => 'Доступ запрещен. Вы не авторизованы'
            ],403);
        }
        if($searchUser[0]['name'] === $findStavka['user']){
            $errors = [];

            $price = $request->price;
            $company = $request->company;
            $settings = $request->settings;

            if(empty($price)){
                array_push($errors, array('price_required' => 'Поле цена обязательно к заполнению'));
            }
            if(empty($company)){
                array_push($errors, array('company_required' => 'Поле компания обязательно к заполнению'));
            }
            if(empty($settings)){
                array_push($errors, array('settings_required' => 'Поле настройки обязательно к заполнению'));
            }
            if(count($errors) !== 0){
                return response([
                    "status" => "valid error",
                    "message" => [
                        $errors
                    ]
                ],401);
            }

            $upStavka = $findStavka->update([
                'price' => "$price",
                'company' => "$company",
                'settings' => "$settings",
            ]);

            return response([
                "status0" => true,
                "stavka" => [
                    $findStavka
                ]
            ],201);

        } else {
            return response([
                "message" => "Вы пытаетесь изменить не свою ставку. Доступ запрещен"
            ],403);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Stavka  $stavka
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stavka $stavka, $id, Request $request)
    {
        $bearerUser = $request->header('authorization');
        $bearerUser = intval(substr($bearerUser, 67));
        $userBearerSearch = User::where('id','=',"$bearerUser")->get();
        if(count($userBearerSearch) == 0){
            return response([
                'status' => false,
                "message" => "Вы не авторизованы"
            ],403);
        }
        $postSearch = Stavka::find($id);
        if($postSearch == null){
            return response([
                "message" => "Ставка не найдена"
            ],404);
        }
        if($userBearerSearch[0]['name'] === $postSearch['user']){
            $postSearch->delete();

            return response([
                'status' => true,
                "message" => "Успешное удаление"
            ], 201);
        } else {
            return response([
                "message" => "Вы пытаетесь удалить чужую ставку"
            ],403);
        }
    }
}
