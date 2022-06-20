<?php

namespace App\Http\Controllers;

use App\Client;
use App\FbAccount;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function isToCollectMention(Request $request)
    {
        
        $account = FbAccount::find($request->account_id);

        $account->update(['mention' => $request->checked]);
        
    }



}