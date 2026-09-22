<?php

namespace shurjopayv2\ShurjopayLaravelPackage8\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShurjopayController extends Controller
{
    public function index($value='')
    {
        return view('shurjopay::shurjopay');
    }

    public function checkout($info){
        $flag=0;
        $info['prefix']= config('shurjopay.apiCredentials.prefix');
        $info['return_url']= config('shurjopay.apiCredentials.return_url');
        $info['cancel_url']= config('shurjopay.apiCredentials.cancel_url');
        if(!isset($info['prefix']))
        {
            $flag=1;
            echo 'Please provide Prefix';
        }
        if(!isset($info['amount']))
        {
            $flag=2;
            echo 'Please provide amount';

        }
        if(!isset($info['order_id']))
        {
            $flag=3;
            echo 'Please provide order id';

        }
        if(!isset($info['customer_name']))
        {
            $flag=4;
            echo 'Please provide customer name';

        }
        if(!isset($info['customer_phone']))
        {
            $flag=5;
            echo 'Please provide customer phone';

        }
        if(!isset($info['customer_address']))
        {
            $flag=6;
            echo 'Please provide customer address';

        }
        if($flag==0)
        {
            $response = $this->getUrl($info);

            $arr = json_decode($response);
            if(!empty($arr->checkout_url))
            {
                $url = ($arr->checkout_url);
                return redirect($url);
            }
            else{
                return $response;
            }

        }
    }
    private function getToken() {
        $userExists=false;
        if(!empty(config('shurjopay.apiCredentials.username')) && !empty(config('shurjopay.apiCredentials.password')))
        {
            $user= config('shurjopay.apiCredentials.username');
            $pass= config('shurjopay.apiCredentials.password');
            $userExists=true;
        }


        if($userExists)
        {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => config('shurjopay.apiCredentials.base_url').'/api/get_token',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{
                                            "username": "'.$user.'",
                                            "password": "'.$pass.'"
                                        }',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
        }
        else
        {
            $response="Please enter valid username and password";
        }



        return $response;
    }
    private function getUrl($info) {

        $response=$this->getToken();

        $arr=json_decode($response);

        if(!empty($arr->token))
        {
            $tok=($arr->token);
            $s_id=($arr->store_id);

            $info2=array(
                'token'=>$tok,
                'store_id'=>$s_id);
            $final_array=array_merge($info2, $info);
            $bodyJson=json_encode($final_array);
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => config('shurjopay.apiCredentials.base_url').'/api/secret-pay',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>$bodyJson,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                echo "cURL Error #:" . $err;
                exit();
            }else{
                return $response;
            }

        }
        else{
            return $response;
        }

    }
    public function verify($order_id) {
        $order_id = array(
            'order_id' => $order_id);
        $order_id=json_encode($order_id);
        $response=$this->getToken();
        $arr=json_decode($response);
        if(!empty($arr->token))
        {

            $tok=($arr->token);
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => config('shurjopay.apiCredentials.base_url').'/api/verification',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>$order_id
            ,
                CURLOPT_HTTPHEADER => array(
                    'Authorization:Bearer '.$tok,
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
        }


        return $response;



    }
}
