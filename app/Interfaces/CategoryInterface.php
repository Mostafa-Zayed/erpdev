<?php

namespace App\Interfaces;

interface CategoryInterface
{
    public function getAll($businessId,$type,$parent);
    
    public function getAllParent($businessId,$type);
    
    public function getAllChilde($businessId,$type);
    
    public function getChild($categoryId,$businessId);
}