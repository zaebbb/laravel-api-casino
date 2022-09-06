<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DataCheck;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use function Ramsey\Uuid\v4;

class DataCheckController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Application|ResponseFactory|Response|int|object
     */
    public function index(Request $request)
    {
        $token = $request->header('authorization');
        $searchToken = User::where('remember_token', '=',"$token")->get();
        if(count($searchToken) == 0){
            return response([
                "body" => [
                    "Доступ запрещен вы не авторизованы"
                ]
            ],403)->setStatusCode(403,'Forbidden');
        }

        $allData = DataCheck::all();
        return response([
            "datas" => [
                $allData
            ]
        ], 201)->setStatusCode(201,'Complete data');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $token = $request->header('authorization');
        $searchUser = User::where('remember_token', '=', "$token")->get();
        if(count($searchUser) == 0){
            return response([
                "status" => false,
                "message" => "Forbidden"
            ])->setStatusCode(403, "Forbidden User");
        }
        $errors = [];
        $numberData = $request->number;
        $imageData = $request->hasFile('image');
        $arrData = $request->arr;
        if(getType($numberData) == "string"){
            $numberData = intval($numberData);
        }
        if(getType($arrData) == "array"){
            $arrData = "[" . implode(", ", $arrData) . "]";
        }
        if($imageData){
            if($request->file('image')->getSize() > 1024 * 1024 * 2){
                array_push($errors, array("image_size" => "Изображение не должно быть более 2 МБ"));
            }
            if(
                !strpos($request->file('image')->getClientOriginalName(), ".png") &&
                !strpos($request->file('image')->getClientOriginalName(), ".jpg")){
                array_push($errors, array("image_type" => "Прикрепленный файл должен являться изображением"));
            }
        } else{
            array_push($errors, array("image_fatal_err" => "При загрузке фотографии возникла непредвиденная ошибка"));
        }
        if(empty($numberData)){
            array_push($errors, array('number_required' => "Поле цифры обязательно е заполнению"));
        }
        if(mb_strlen($numberData) < 5){
            array_push($errors, array("number_min" => "Поле числа не должно быть менее 5 символов"));
        }
        if(mb_strlen($numberData) > 10){
            array_push($errors, array("number_min" => "Поле числа не должно быть больше 10 символов"));
        }
        if(empty($arrData)){
            array_push($errors, array("array_requred" => "Поле массив обязательно к заполнеиню"));
        }
        if(count($errors) !== 0){
            return response([
                "status" => false,
                "errors" => [
                    $errors
                ]
            ])->setStatusCode(401, "Fatal Error Create Data");
        } else {
            if($imageData){
                $destPath = public_path('imagesData/');
                $fileName = v4() . '.jpg';

                $request->file('image')->move($destPath, $fileName);
            }

            $newData = DataCheck::create([
                'number' => "$numberData",
                'image' => "$fileName",
                'arr' => "$arrData"
            ]);
            $searchData = DataCheck::where('image', '=', "$fileName")->get();

            return response([
                'status' => true,
                'data' => [
                    'id' => $searchData[0]['id'],
                    'number' => $searchData[0]['number'],
                    'image' => $searchData[0]['image'],
                    'arr' => $searchData[0]['arr']
                ]
            ],201)->setStatusCode(201, "Createful Success");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DataCheck  $dataCheck
     * @return Response
     */
    public function show(DataCheck $dataCheck)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DataCheck  $dataCheck
     * @return Response
     */
    public function update(Request $request, DataCheck $dataCheck)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DataCheck  $dataCheck
     * @return Response
     */
    public function destroy(DataCheck $dataCheck)
    {
        //
    }
}
