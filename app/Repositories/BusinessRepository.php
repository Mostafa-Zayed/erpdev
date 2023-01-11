<?php

namespace App\Repositories;

use App\Interfaces\BusinessInterface;
use App\User;
use Carbon\Carbon;
use App\Http\Traits\Util;

class BusinessRepository implements BusinessInterface
{
    use Util;

    public function getAll($businessId)
    {
    }

    public function createNewBusiness(&$request)
    {
        $user = User::create_user($this->getOwnerDetails($request));

        $business = $this->getBusinessDetails($request);

        $businessLocation = $this->getBusinessLocationDetails($request);

        $business['owner_id'] = $user->id;

        if (!empty($business['start_date'])) {
            $business['start_date'] = Carbon::createFromFormat(config('business.default_date_format'), $business['start_date'])->toDateString();
        }

        //upload business logo
        $logo_name = Util::uploadFile($request, 'business_logo', 'business_logos', 'image');
        if (!empty($logo_name)) {
            $business['logo'] = $logo_name;
        }

        //default enabled modules
        $business_details['enabled_modules'] = $this->getDefaultEnabledModules();
    }

    public function getOwnerDetails(&$request)
    {
        $ownerData = $request->only(['surname', 'first_name', 'last_name', 'username', 'email', 'password', 'language']);

        $ownerData['language'] = empty($ownerData['language']) ? config('app.locale') : $ownerData['language'];

        return $ownerData;
    }

    public function getBusinessDetails(&$request)
    {
        $businessData = $request->only(['name', 'start_date', 'currency_id', 'time_zone']);
        $businessData['fy_start_month'] = 1;
    }

    public function getBusinessLocationDetails(&$request)
    {
        $businessLocationData = $request->only(['name', 'country', 'state', 'city', 'zip_code', 'landmark', 'website', 'mobile', 'alternate_number']);
    }

    private function getDefaultEnabledModules()
    {
        return config('business.enabledModules');
    }

    private function getRefNoPrefixes()
    {
        return config('business.ref_no_prefixes');
    }

    private static function getKeyBoardShortcuts()
    {
        return config('business.keyboardShortcuts');
    }
}
