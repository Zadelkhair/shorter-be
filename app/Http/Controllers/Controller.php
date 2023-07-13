<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    // api response
    public function apiResponse($data, $error = null, $code = 200)
    {
        return response()->json([
            'data' => $data,
            'error' => $error
        ], $code);
    }

}
