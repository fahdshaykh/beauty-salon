<?php



namespace App\Http\Controllers\Api;



use Illuminate\Http\Request;

use App\Http\Controllers\Controller as ApiController;



class BaseController extends ApiController

{

    /**

     * success response method.

     *

     * @return \Illuminate\Http\Response

     */

    public function sendResponse($result, $message)

    {

        

        $response = [

            'success' => true,

            'data'    => $result,

            'message' => $message,

        ];



        return response()->json($response,  200, ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
        JSON_UNESCAPED_UNICODE);

    }



    /**

     * return error response.

     *

     * @return \Illuminate\Http\Response

     */

    public function sendError($result, $message)

    {

        $response = [

            'success' => false,

            'data'    => $result,

            'message' => $message,

        ];

        return response()->json($response, 200, ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
        JSON_UNESCAPED_UNICODE);

        // if(!empty($errorMessages)){

        //     $response['data'] = $errorMessages;

        // }



        // if(empty($errorMessages)){

        //     $response['data'] = [];

        // }



        // return response()->json($response, $code);

    }

}

