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
        if ($count == 1) {
            return $this->singleResult($query);
        } elseif ($count > 1) {
            return $this->multipleResults($query);
        } else {
            return $this->notFound();
        }
    }

    private function singleResult($query)
    {
        $twiml = new Services_Twilio_Twiml;
        $employee = $query->first();
            $twiml->message(collect([$employee->full_name, $employee->phone_number,
                $employee->email])->implode("\n"));
        return $this->xmlResponse($twiml);
    }

    private function multipleResults($query)
    {
        $twiml = new Services_Twilio_Twiml;
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
        return $this->xmlResponse($twiml);
    }

    private function notFound()
    {
        $twiml = new Services_Twilio_Twiml;
        $twiml->message('We did not find the employee you\'re looking for');
        return $this->xmlResponse($twiml);
    }

    private function xmlResponse($twiml)
    {
        return response($twiml, 200)->header('Content-Type', 'application/xml');
    }
}
