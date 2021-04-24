<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use zcrmsdk\crm\setup\restclient\ZCRMRestClient;
use zcrmsdk\crm\setup\org\ZCRMOrganization;
use zcrmsdk\oauth\ZohoOAuth;
use zcrmsdk\crm\crud\ZCRMModule;

class ZohooController extends Controller
{
    public function auth(Request $request)
    {
        $redirectTo = 'https://accounts.zoho.com/oauth/v2/auth' . '?' . http_build_query(
                [
                    'client_id' => env('ZOHO_CLIENT_ID'),
                    'redirect_uri' => env('ZOHO_REDIRECT_URI'),
                    'scope' => 'ZohoCRM.modules.ALL',
                    'token_persistence_path'=>env('token_persistence_path'),
                    'response_type' => 'code',
                ]);
        return redirect($redirectTo);
    }

    public function store(Request $request)
    {
        $input = $request->all();
       // dd($input);
        $client_id = env('ZOHO_CLIENT_ID');
        $client_secret = env('ZOHO_CLIENT_SECRET');
        $base_acc_url = 'https://accounts.zoho.com';


        // Get ZohoCRM Token
       // $tokenUrl = 'https://accounts.zoho.com.eu/oauth/v2/token?code=' . $input["code"] . '&client_id=' . $client_id . '&client_secret=' . $client_secret . '&redirect_uri=' .env('ZOHO_REDIRECT_URI'). '&grant_type=authorization_code';
        $token_url = $base_acc_url . '/oauth/v2/token?grant_type=authorization_code&client_id='. $client_id . '&client_secret='. $client_secret . '&redirect_uri='.env('ZOHO_REDIRECT_URI').'&code=' . $input["code"];
        $access_token_oblect = generate_access_token($token_url);
        //dd($access_token_oblect);
        $access_token = $access_token_oblect->access_token;

        //dd($access_token->access_token);
        //$test='1000.e9e34b66377d8e3e864266d4a255af1a.c4935852e476950ee3e6e91f11c1036d';

        $service_url = 'https://www.zohoapis.com/crm/v2/Deals';
        $data =  [
            'data' => [
                [
                    "Deal_Name"=> "DEALTEST",
                    "Stage"=> "Qualification",
                    "External_Id" =>"dealtest_external_id"
                ]
            ],
            "trigger"=> [
        "approval",
        "workflow",
        "blueprint"
    ]
    ];
        $header = array(
            'Authorization: Zoho-oauthtoken ' . $access_token,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $service_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);
        curl_close($ch);
        var_dump($result);

        $dealResponce_array = json_decode($result, true);
        $code = $dealResponce_array['data'][0]['code'];
        $info = $dealResponce_array['data'][0]['details'];
        //dd($code);
        if(isset($code) && ($code == "SUCCESS")){
            \Session::put('success','Deal created in ZohoCRM successfully.!');
            \Session::put('info', $info);
            \Session::put('token', $access_token);
            return redirect()->route('deal');
        }else {
            \Session::put('error','Deal not create, please try again.!!');
            return redirect()->route('deal');
        }
    }

    //also i can make form-based-adding. I didnt do it due to lack of time and there was nothing in message from Katerina.
}