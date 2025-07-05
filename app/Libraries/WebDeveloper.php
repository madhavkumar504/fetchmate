<?php

namespace App\Libraries;

use App\Interfaces\Employee;

class WebDeveloper implements Employee
{
    public function getSalary()
    {
        return "Web Developer salary is $5000";
    }
}
