<?php

namespace App\Http\Controllers;

use App\Bids;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class BidsController extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function post(Request $request)
    {
        /* Validate request */
        $validator = Validator::make($request->all(), [
            'loan_id' => 'required|integer|min:1',
            'lender_id' => 'required|integer|min:1',
            'amount' => 'required|integer|min:50|max:5000'
        ]);
        if ($validator->fails()) {
            return response([
                'status' => '400 Bad Request',
                'errors' => $validator->errors()
            ], 400);
        }

        /* Create new bid */
        $modelResponse = Bids::post($request->all());

        /* Ensure everything went smoothly */
        if ((int)$modelResponse['status'] !== 201) {
            return response([
                'status' => $modelResponse['status'],
                'errors' => $modelResponse['message']
            ], $modelResponse['status']);
        }

        return $modelResponse;
    }
}

