<?php

namespace App\Repositories;

use App\Interfaces\InvoiceSchemeInterface;
use App\InvoiceScheme;
use Illuminate\Support\Facades\DB;

class InvoiceSchemeRepository implements InvoiceSchemeInterface
{
    public function getAll(& $businessId)
    {
        return DB::table('invoice_schemes')->where('business_id',$businessId)->get();
    }
}