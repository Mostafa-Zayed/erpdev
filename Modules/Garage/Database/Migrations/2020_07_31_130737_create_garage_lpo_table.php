<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGarageLpoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('garage_lpo', function (Blueprint $table) {
            $table->increments('id');

               $table->string('lpo_no') ->nullable();
             
                    
            $table->decimal('amount', 22, 4)   ->nullable()->default(0); 
                $table->decimal('excess', 22, 4) ->nullable()->default(0);
                     $table->integer('tax_id')->nullable();
                     $table->integer('job_card_id')->nullable();
                     $table->integer('created_by')->nullable();
                  $table->string('claim_no') ->nullable();
                  $table->string('trn_no') ->nullable();
                  $table->datetime('lpo_date') ->nullable();
                
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('garage_lpo');
    }
}
