<?php

namespace App\Http\Controllers;

use App\Services\AddMoneyAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddMoneyController extends Controller
{
    public function __invoke(Request $request)
    {
        $userId = $request->get('user_id');
        $amount = $request->get('amount');

        $result = (new AddMoneyAction())((object)[
            'user_id' => $userId,
            'amount' => $amount,
        ]);

        return new JsonResponse($result);
    }
}
