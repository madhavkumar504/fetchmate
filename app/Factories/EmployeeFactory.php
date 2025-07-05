<?php

namespace App\Factories;

use App\Interfaces\Employee;
use App\Libraries\WebDeveloper;
use App\Libraries\AndroidDeveloper;
use App\Libraries\InvalidEmployee;


class EmployeeFactory
{
    public function make($type): Employee
    {
        return match ($type) {
            'web' => new WebDeveloper(),
            'android' => new AndroidDeveloper(),
            default => new InvalidEmployee("Invalid employee type: $type"),
        };
    }
}
