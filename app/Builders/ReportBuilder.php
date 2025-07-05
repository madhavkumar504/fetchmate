<?php

namespace App\Builders;

class ReportBuilder implements ReportBuilderInterface
{
    protected $profile;

    public function __construct()
    {
        $this->profile = [];
    }

    public function addBasicInfo($name, $email): self
    {
        $this->profile['data'] = [
            'name' => $name,
            'email' => $email,
        ];
        return $this;
    }

    public function addAddress($address): self
    {
        $this->profile['address'] = $address;
        return $this;
    }

    public function addPreferences($preferences): self
    {
        $this->profile['preferences'] = $preferences;
        return $this;
    }

    public function getProfile(): array
    {
        return $this->profile;
    }
}
