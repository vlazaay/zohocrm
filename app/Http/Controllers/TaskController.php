<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        //add related task
        $arr=\Session::get('info');
        $access_token = \Session::get('token');

        $service_url = 'https://www.zohoapis.com/crm/v2/Tasks';
        $data =  [
            'data' => [
                [
                    "Subject"=> "Test task for deal",
                    "Deals"=> [
                        "External_Id" => "dealtest_external_id"
                    ]
                ]
            ]
        ];
        $header = array(
            'Authorization: Zoho-oauthtoken ' . $access_token,
            'Content-Type: application/json',
            'X-EXTERNAL: Deals.External_Id'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $service_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);
        curl_close($ch);
        $taskResponce_array = json_decode($result, true);
        $code = $taskResponce_array['data'][0]['code'];

        if(isset($code) && ($code == "SUCCESS")){
            \Session::put('success','Related task and Deal created in ZohoCRM successfully.!');
            return view('index');
        }else {
            \Session::put('errorTask','Deal created but Related Task not, please try again.!!');
            return view('index');
        }

    }
}
