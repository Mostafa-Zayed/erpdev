<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGarageColumnsToLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('business_locations', function (Blueprint $table) {
      
            
            $table->string('stamp')->nullable()->after('updated_at');
            $table->string('signature')->nullable()->after('stamp');
            $table->text('garage_invoice_footer')->nullable()->after('signature');
          
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
