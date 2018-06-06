<?php

namespace App\Http\Controllers;

use App\Loans;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class LoansController extends Controller
{
    /** @var array $validSortFields */
    private $validSortFields = [
        'id' => 1,
        'accepted_on' => 1,
        'approved_on' => 1
    ];

    /** @var array $validSortOrders */
    private $validSortOrders = [
        'asc' => 1,
        'desc' => 1
    ];

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function live(Request $request)
    {
        /* Validate request */
        $validator = Validator::make($request->all(), [
            'sort' => 'string',
            'sortOrder' => 'string'
        ]);
        if ($validator->fails()) {
            return response([
                'status' => '400 Bad Request',
                'errors' => $validator->errors()
            ], 400);
        }

        /* Extract params from request */
        $page = (array_key_exists('page', $_GET)) ? (int)$_GET['page'] : 1;
        $sortField = 'id';
        if (array_key_exists('sort', $_GET) && array_key_exists($_GET['sort'], $this->validSortFields)) {
            $sortField = $_GET['sort'];
        }
        $sortOrder = 'DESC';
        if (array_key_exists('sortOrder', $_GET) && array_key_exists($_GET['sortOrder'], $this->validSortOrders)) {
            $sortOrder = $_GET['sortOrder'];
        }

        $liveLoans = Loans::live($page, $sortField, $sortOrder);

        /* Ensure Live Loans were found */
        if (count($liveLoans) === 0) {
            return response([
                'status' => '404 Not Found',
            ], 404);
        }

        /* 200 Ok */
        return response([
            'status' => '200 Ok',
            'payload' => [
                $liveLoans
            ]
        ], 200);
    }
}
