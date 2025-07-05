<?php

namespace App\Libraries;

use App\Interfaces\Employee;

class InvalidEmployee implements Employee
{
    private $message;
    public function __construct($message)
    {
        $this->message = $message;
    }
    public function getSalary()
    {
        return response()->json(['error' => $this->message]);
    }
}
