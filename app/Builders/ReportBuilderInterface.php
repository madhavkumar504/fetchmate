<?php

namespace App\Builders;

interface ReportBuilderInterface
{
    public function addBasicInfo($name, $email): self;
    public function addAddress($address): self;
    public function addPreferences($preferences): self;
    public function getProfile(): array;
}
