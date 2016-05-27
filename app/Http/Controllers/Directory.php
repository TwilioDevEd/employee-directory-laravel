<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class Directory extends Controller
{
    public function search()
    {
        return view('directory.notFound');
    }
}
