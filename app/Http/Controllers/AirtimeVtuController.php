<?php

namespace App\Http\Controllers;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Vtpass;
use App\Payment;
use Illuminate\Support\Str;

class AirtimeVtuController extends Controller
{
  public function purchase(Request $request)
  {
    $request->validate([
      'serviceID'   => 'required|in:mtn,glo,etisalat,airtel', //integer e.g mtn,airtel
      'amount'      => 'required|required|regex:/^\d+(\.\d{1,2})?$/', // integer
      'phone'       => 'required', //integer
      'trxref'      => 'required',
    ]);

    $serviceID    = $request->serviceID;
    $amount       = $request->amount;
    $phone    = $request->phone;
    $request_id   = Str::random();
    $trxref       = $request->trxref;

    $payment        = Payment::whereReference($trxref)->first();

    $this->authorize('use', [Payment::class, $payment, $amount]);

    $res = Vtpass::purchase([
      'serviceID'   => $serviceID,
      'amount'      => $amount,
      'phone'       => $phone,
      'request_id'  => $request_id,
    ]);

    $user = $request->user('api');

    if ($res['code'] == '000') {
      Transaction::addNew($res, $payment->id, $user);

       return $res;
    } else {
      return $res;
    }
  }

  public function status(Request $request)
  {
    $request->validate([
      'request_id' => 'required',
    ]);

    $request_id = $request->request_id;

    return Vtpass::status([
      'request_id'      => $request_id,
    ]);
  }

  public function mtn_airtime_vtu_api(Request $request)
  {

    $username = "enluxtech@gmail.com"; //email address(sandbox@vtpass.com)
    $password = "Comkid@1"; //password (sandbox)
    $host = 'https://sandbox.vtpass.com/api/pay';
    $data = array(
      	'serviceID'=> $request->serviceID, //integer e.g mtn,airtel
      	'amount' => $request->amount, // integer
      	'phone' => $request->recepient, //integer
      	'request_id' => rand(100,9999) // unique for every transaction from your platform
    );


    $curl       = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => $host,
    	CURLOPT_RETURNTRANSFER => true,
    	CURLOPT_ENCODING => "",
    	CURLOPT_MAXREDIRS => 10,
    	CURLOPT_USERPWD => $username.":" .$password,
    	CURLOPT_TIMEOUT => 30,
    	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    	CURLOPT_CUSTOMREQUEST => "POST",
    	CURLOPT_POSTFIELDS => $data,
    ));
    $response = json_decode(curl_exec( $curl ),true);
  $err = curl_error($curl);
dd($response);
  curl_close($curl);

  if ($err) {
    return response()->json([
   'code' => '404',
   'data'  => "cURL Error #:" . $err,
], 404);
  } else {
    $customer =  Transaction::create(array(
'response_description' =>$response['response_description'],
 'product_name' =>$response['content']['transactions']['product_name'],
 'transactionId' => $response['content']['transactions']['transactionId'],
 'requestId' =>$response['requestId'],
  'type' => $response['content']['transactions']['type'],
  'amout' =>$response['amount'],
  'quantity'  => $response['content']['transactions']['quantity'],
  'phone'  => $response['content']['transactions']['unique_element'],
  'transaction_date'   => $response['transaction_date']['date'],

     ));
    return $response;
  }


    }

    public function glo_airtime_vtu_api(Request $request)
    {
      $username = "enluxtech@gmail.com"; //email address(sandbox@vtpass.com)
      $password = "Comkid@1"; //password (sandbox)
      $host = 'https://sandbox.vtpass.com/api/pay';
      $data = array(
        	'serviceID'=> $request->serviceID, //integer e.g mtn,airtel
        	'amount' => $request->amount, // integer
        	'phone' => $request->recepient, //integer
        	'request_id' => rand(100,9999) // unique for every transaction from your platform
      );
      $curl       = curl_init();
      curl_setopt_array($curl, array(
      CURLOPT_URL => $host,
      	CURLOPT_RETURNTRANSFER => true,
      	CURLOPT_ENCODING => "",
      	CURLOPT_MAXREDIRS => 10,
      	CURLOPT_USERPWD => $username.":" .$password,
      	CURLOPT_TIMEOUT => 30,
      	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      	CURLOPT_CUSTOMREQUEST => "POST",
      	CURLOPT_POSTFIELDS => $data,
      ));
    $response = json_decode(curl_exec( $curl ),true);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return response()->json([
     'code' => '404',
     'data'  => "cURL Error #:" . $err,
  ], 404);
    } else {
      $customer =  Transaction::create(array(
  'response_description' =>$response['response_description'],
   'product_name' =>$response['content']['transactions']['product_name'],
   'transactionId' => $response['content']['transactions']['transactionId'],
   'requestId' =>$response['requestId'],
    'type' => $response['content']['transactions']['type'],
    'amout' =>$response['amount'],
    'quantity'  => $response['content']['transactions']['quantity'],
  'phone'  => $response['content']['transactions']['unique_element'],
    'transaction_date'   => $response['transaction_date']['date'],

       ));
      return $response;
    }


      }

      public function airtel_airtime_vtu_api(Request $request)
      {
        $username = "enluxtech@gmail.com"; //email address(sandbox@vtpass.com)
        $password = "Comkid@1"; //password (sandbox)
        $host = 'https://sandbox.vtpass.com/api/pay';
        $data = array(
            'serviceID'=> $request->serviceID, //integer e.g mtn,airtel
            'amount' => $request->amount, // integer
            'phone' => $request->recepient, //integer
            'request_id' => rand(100,9999) // unique for every transaction from your platform
        );
        $curl       = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $host,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_USERPWD => $username.":" .$password,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => $data,
        ));
      //  $response = curl_exec($curl);
        $response = json_decode(curl_exec( $curl ),true);
      $err = curl_error($curl);

      curl_close($curl);

      if ($err) {
        return response()->json([
       'code' => '404',
       'data'  => "cURL Error #:" . $err,
    ], 404);
      } else {
        $customer =  Transaction::create(array(
    'response_description' =>$response['response_description'],
     'product_name' =>$response['content']['transactions']['product_name'],
     'transactionId' => $response['content']['transactions']['transactionId'],
     'requestId' =>$response['requestId'],
      'type' => $response['content']['transactions']['type'],
      'amout' =>$response['amount'],
      'quantity'  => $response['content']['transactions']['quantity'],
      'phone'  => $response['content']['transactions']['unique_element'],
      'transaction_date'   => $response['transaction_date']['date'],

         ));
        return $response;
      }


        }

      public function etisalat_airtime_vtu_api(Request $request)
      {
        $username = "enluxtech@gmail.com"; //email address(sandbox@vtpass.com)
        $password = "Comkid@1"; //password (sandbox)
        $host = 'https://sandbox.vtpass.com/api/pay';
        $data = array(
            'serviceID'=> $request->serviceID, //integer e.g mtn,airtel
            'amount' => $request->amount, // integer
            'phone' => $request->recepient, //integer
            'request_id' => rand(100,9999) // unique for every transaction from your platform
        );
        $curl       = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $host,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_USERPWD => $username.":" .$password,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => $data,
        ));
      //  $response = curl_exec($curl);
        $response = json_decode(curl_exec( $curl ),true);
      $err = curl_error($curl);

      curl_close($curl);

      if ($err) {
        return response()->json([
       'code' => '404',
       'data'  => "cURL Error #:" . $err,
    ], 404);
      } else {
        $customer =  Transaction::create(array(
    'response_description' =>$response['response_description'],
     'product_name' =>$response['content']['transactions']['product_name'],
     'transactionId' => $response['content']['transactions']['transactionId'],
     'requestId' =>$response['requestId'],
      'type' => $response['content']['transactions']['type'],
      'amout' =>$response['amount'],
      'quantity'  => $response['content']['transactions']['quantity'],
      'phone'  => $response['content']['transactions']['unique_element'],
      'transaction_date'   => $response['transaction_date']['date'],

         ));
        return $response;
      }


        }

}
