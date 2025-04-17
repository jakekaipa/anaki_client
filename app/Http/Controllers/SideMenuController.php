<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SideMenuController extends Controller
{
    public function updateSession(Request $request)
    {
        $sideMenuState = $request->input('sideMenuState');
        Session::put('side-menu', $sideMenuState);

        return response()->json(['success' => true]);
    }
}
