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
        $query = Employee::where('full_name', 'LIKE', '%' . $name . '%');
        $count = $query->count();
        $twiml = new Services_Twilio_Twiml;
        if ($count === 1) {
            $employee = $query->first();
            $twiml->message(collect([$employee->full_name, $employee->phone_number,
                $employee->email])->implode("\n"));
            return $this->_response($twiml);
        } elseif ($count > 1) {
            $employees = $query->get();
            $_SESSION["employees"] = $employees->map(function($employee, $key){
                return $employee->email;
            });
            $employees_message = $employees->map(function($employee, $key) {
                $option = $key+1;
                return "$option for $employee->full_name";
            });
            $twiml->message(collect(['We found multiple people, reply with:',
                    $employees_message,'Or start over'])->flatten()->implode("\n"));
            return $this->_response($twiml);
        } else {
            $twiml->message('We did not find the employee you\'re looking for');
            return $this->_response($twiml);
        }
    }

    private function _response($twiml)
    {
        return response($twiml, 200)->header('Content-Type', 'application/xml');
    }
}
