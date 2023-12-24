<?php

namespace App\Http\Controllers;

use App\Services\GetBalanceAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetBalanceController extends Controller
{
    public function __invoke(Request $request)
    {
        $userId = $request->get('user_id');

        $result = (new GetBalanceAction())($userId);

        return new JsonResponse($result);
    }
}
