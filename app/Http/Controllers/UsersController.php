<?php

namespace App\Http\Controllers;

use App\Users;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class UsersController extends Controller
{
    /**
     * @param $id
     * @return \Illuminate\Support\Collection
     */
    public function lender($id)
    {
        /* Validate request */
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|int'
        ]);

        if ($validator->fails()) {
            return response([
                'status' => '400 Bad Request',
                'errors' => $validator->errors()
            ], 400);
        }

        /* Retrieve Lender */
        $lender = Users::lender($id);

        /* Ensure Lender was found */
        if (count($lender) === 0) {
            return response([
                'status' => '404 Not Found',
            ], 404);
        }

        /* 200 Ok */
        return response([
            'status' => '200 Ok',
            'payload' => [
                $lender
            ]
        ], 200);
    }
}

