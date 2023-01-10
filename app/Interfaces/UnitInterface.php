<?php

namespace App\Interfaces;

interface UnitInterface
{
    public function getAll($businessId = null);
    
    public function getBaseOnly($businessId);
}