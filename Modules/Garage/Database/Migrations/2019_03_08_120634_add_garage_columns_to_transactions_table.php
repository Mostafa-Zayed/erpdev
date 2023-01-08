<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGarageColumnsToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
      
            
            $table->integer('garage_job_card_id')->nullable()->after('created_by');
            $table->double('garage_cash')->default(0)->nullable()->after('garage_job_card_id');
            $table->double('garage_total_cash')->default(0)->nullable()->after('garage_cash');
            $table->integer('garage_is_cash')->default(0)->nullable()->after('garage_total_cash');
            $table->integer('garage_is_insurance')->default(0)->nullable()->after('garage_is_cash');
          
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
