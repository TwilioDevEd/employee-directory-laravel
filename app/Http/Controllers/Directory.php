<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Services_Twilio_Twiml;

class Directory extends Controller
{
    public function search()
    {
        $message_response = new Services_Twilio_Twiml;
        $message_response->message('We did not find the employee you\'re looking for');
        return response($message_response, 200)->header('Content-Type', 'application/xml');
    }

}
