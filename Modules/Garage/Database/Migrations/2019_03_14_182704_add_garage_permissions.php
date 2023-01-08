<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

class AddGaragePermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {     
        Permission::create(['name' => 'garage.view']);
        Permission::create(['name' => 'garage.create']);
        Permission::create(['name' => 'garage.update']);
        Permission::create(['name' => 'garage.delete']);
        Permission::create(['name' => 'garage_status.update']);
        Permission::create(['name' => 'garage_status.access']);
        Permission::create(['name' => 'garage.settings']);
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
