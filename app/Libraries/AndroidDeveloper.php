<?php

namespace App\Libraries;

use App\Interfaces\Employee;

class AndroidDeveloper implements Employee
{
    public function getSalary()
    {
        return "Android Developer salary is $6000";
    }
}
