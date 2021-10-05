<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\ClassString;

class indexController extends Controller
{
public function index(){
    return view('front.index');
}

public function detail(){

    return view('front.detail');
}

}
