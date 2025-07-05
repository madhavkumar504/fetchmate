<?php

namespace App\Builders;

class ReportDirector
{
    protected $builder;

    public function __construct(ReportBuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    public function buildBasicProfile($name, $email, $address)
    {
        return $this->builder->addBasicInfo($name, $email)->addAddress($address)->getProfile();
    }
}
