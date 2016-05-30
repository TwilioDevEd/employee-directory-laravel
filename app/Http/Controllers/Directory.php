<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Employee;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Services_Twilio_Twiml;

class Directory extends Controller
{
    public function search(Request $request)
    {
        $name = $request->input('Body');
        $employees = Employee::where('full_name', $name);
        $count = $employees->count();
        if ($count === 1) {
            $employee = $employees->first();
            $message_response = new Services_Twilio_Twiml;
            $message_response->message(implode('\n', array($employee->full_name, $employee->phone_number, $employee->email)));
            return response($message_response, 200)->header('Content-Type', 'application/xml');
        } else {
            $message_response = new Services_Twilio_Twiml;
            $message_response->message('We did not find the employee you\'re looking for');
            return response($message_response, 200)->header('Content-Type', 'application/xml');
        }
    }

}
