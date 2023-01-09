<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use App\Utils\DropDowns\UnitDropDown;

trait BusinessService
{
    public static function getBusinessId()
    {
        return request()->session()->get('user.business_id');
    }
    
    public static function businessCan($permissions)
    {
        foreach ($permissions as $permission) {
            if (!auth()->user()->can($permission)) {
                abort(403, 'Unauthorized action.');
            }
        }
        
        return true;
    }
    
    public static function defaultProfitPercent()
    {
        return request()->session()->get('business.default_profit_percent');
    }
    
    public static function getUser()
    {
        return request()->session()->get('user.id');
    }
    
    public function checkRequest(Request & $request,$key,$value = null ,$returnValue = null)
    {
        if ($request->filled($key)){
            
            return $request->$key;
            
        } elseif($request->filled($key) && ! is_null($value)) {
            
            return $request->$key == $value ? $request->$key : false;
            
        } elseif($request->filled($key) && ! is_null($value) && ! is_null($returnValue)) {
            
            return $returnValue;
        }
    }
    
    public function getSessionValue($key,$type = 'user')
    {
        return request()->session()->get($type . '.' . $key);
    }
    
    public static function getDrowpDown($model,$options = [])
    {
        switch($model){
            case 'unit':
                return (new UnitDropDown())->drawMenu();
            break;    
        }
    }

    public static function businessCanRegister()
    {
        return config('business.allowRegistration') ? true : false;
    }
}