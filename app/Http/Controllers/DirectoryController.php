<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Employee;
use App\Http\Requests;
use App\Http\Session;
use App\Http\Controllers\Controller;
use Twilio\Twiml;

class DirectoryController extends Controller
{
    public function search(Request $request)
    {
        $body = $request->input('Body');
        if ($this->_isChoiceAnswer($body, $request)) {
            return $this->_selectedEmployee($body, $request);
        }

        $query = Employee::where('full_name', 'LIKE', '%' . $body . '%');
        $count = $query->count();
        if ($count == 1) {
            return $this->_singleResult($query);
        } elseif ($count > 1) {
            return $this->_multipleResults($query, $request);
        } else {
            return $this->_notFound();
        }
    }

    private function _selectedEmployee($body, $request)
    {
        $email = $request->session()->get('employees')->get($body - 1);
        return $this->_singleResult(Employee::where('email', $email));
    }

    private function _isChoiceAnswer($body, $request)
    {
        return is_numeric($body)
            && in_array(
                intval($body),
                range(1, $request->session()->get("employees")->count())
            );
    }

    private function _singleResult($query)
    {
        $twiml = new Twiml;
        $employee = $query->first();
            $twiml->message(
                collect(
                    [$employee->full_name, $employee->phone_number,
                    $employee->email]
                )->implode("\n")
            );
            return $this->_xmlResponse($twiml);
    }

    private function _multipleResults($query, $request)
    {
        $twiml = new Twiml;
        $employees = $query->get();
        $request->session()->put(
            'employees', $employees->map(
                function ($employee, $key) {
                    return $employee->email;
                }
            )
        );
        $employees_message = $employees->map(
            function ($employee, $key) {
                $option = $key + 1;
                return "$option for $employee->full_name";
            }
        );
        $twiml->message(
            collect(
                ['We found multiple people, reply with:',
                $employees_message, 'Or start over']
            )->flatten()->implode("\n")
        );
        return $this->_xmlResponse($twiml);
    }

    private function _notFound()
    {
        $twiml = new Twiml;
        $twiml->message('We did not find the employee you\'re looking for');
        return $this->_xmlResponse($twiml);
    }

    private function _xmlResponse($twiml)
    {
        return response($twiml, 200)->header('Content-Type', 'application/xml');
    }
}
