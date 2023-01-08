<?php

use Illuminate\Database\Migrations\Migration;
use Modules\Garage\Entities\GarageStatus;

class AddGarageStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {     
        GarageStatus::create(['name' => 'Under_process','color' => '#ffe600','sort_order' => 1 , 'type' => 'cash','view_name' => 'Under process','is_completed_status' => 0 ]);
        GarageStatus::create(['name' => 'quote_submited','color' => '#2400ff','sort_order' => 2 , 'type' => 'insurance','view_name' => 'Need to submit','is_completed_status' => 0]);
        GarageStatus::create(['name' => 'LPO_pendig','color' => '#18d1c6','sort_order' => 3 , 'type' => 'insurance','view_name' => 'LPO pendig','is_completed_status' => 0]); 
        GarageStatus::create(['name' => 'Under_repair','color' => '#18d1c6','sort_order' => 5 , 'type' => 'repair','view_name' => 'Under repair','is_completed_status' => 0]);
        GarageStatus::create(['name' => 'LPO_issued','color' => '#18d1c6','sort_order' => 4 , 'type' => 'insurance','view_name' => 'LPO issued','is_completed_status' => 0]);

        GarageStatus::create(['name' => 'invoices','color' => '#18d1c6','sort_order' => 6 , 'type' => 'insurance','view_name' => 'invoices','is_completed_status' => 0]);
        GarageStatus::create(['name' => 'paid_invoice','color' => '#18d1c6','sort_order' => 7 , 'type' => 'insurance','view_name' => 'paid invoice','is_completed_status' => 0]);
        GarageStatus::create(['name' => 'Car_delivered','color' => '#11b52b','sort_order' => 8 , 'type' => 'both','view_name' => 'Car delivered','is_completed_status' => 1]);
        GarageStatus::create(['name' => 'ready_cars','color' => '#18d1c6','sort_order' => 9 , 'type' => 'cash','view_name' => 'ready cars','is_completed_status' => 0]);
        GarageStatus::create(['name' => 'pending','color' => '#ffe600','sort_order' => 10 , 'type' => 'repair','view_name' => 'pending','is_completed_status' => 0]);
        GarageStatus::create(['name' => 'repair_stopped','color' => 'red','sort_order' => 11 , 'type' => 'repair','view_name' => 'repair stopped','is_completed_status' => 0]);
        GarageStatus::create(['name' => 'repaired','color' => '#11b52b','sort_order' => 12 , 'type' => 'repair','view_name' => 'repaired','is_completed_status' => 0]);
     
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
