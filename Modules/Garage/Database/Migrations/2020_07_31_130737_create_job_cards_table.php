<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('garage_job_cards', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')
                    ->references('id')->on('business')
                    ->onDelete('cascade');

            $table->integer('location_id') ->nullable()->unsigned(); 
                
             $table->integer('transaction_id')  ->nullable()->unsigned();
       

            $table->integer('contact_id')->nullable()->unsigned();
            $table->foreign('contact_id')
                    ->references('id')->on('contacts')
                    ->onDelete('cascade');

           
            $table->integer('status_id')->nullable();
            $table->enum('car_status', ['in', 'out'])->nullable();
            $table->enum('pay_types', ['cash', 'insurance','both'])->nullable();
            $table->enum('type', ['OD', 'TP','REC'])->nullable();
       
             $table->string('job_sheet_no') ->nullable();
             $table->string('serial_no') ->nullable();
             
            $table->integer('insurance_company_id')->nullable();
             $table->datetime('date_in')->nullable();
            $table->datetime('completed_on')->nullable();
            $table->datetime('estimation_date')->nullable();
         

             $table->string('car_marks') ->nullable();
             $table->string('police_report') ->nullable();
             $table->string('id_photo') ->nullable();
             $table->string('d_license') ->nullable();
             $table->string('v_license') ->nullable();
             $table->string('job_card_photo') ->nullable();
             
             
             $table->string('car_brand') ->nullable();
             $table->string('car_plate') ->nullable();
             $table->string('care_model') ->nullable();
             $table->string('excess') ->nullable();
             $table->string('repair_days') ->nullable();
             $table->string('estimation_pdf') ->nullable();
             $table->string('repair_status') ->nullable();
             $table->integer('amount') ->nullable()->default(0);
             
             
             
              $table->text('cash_desc')->nullable();
              $table->text('insurance_desc')->nullable();
              $table->text('parts')->nullable();
              $table->text('custom_field_1')->nullable();
              $table->text('custom_field_2')->nullable();
              $table->text('custom_field_3')->nullable();
              $table->text('custom_field_4')->nullable();
              $table->text('custom_field_5')->nullable();
              $table->text('parts_desc')->nullable();
              $table->text('notes')->nullable();

            $table->text('comment_by_ss')
                ->comment('comment made by technician')
                ->nullable();

            $table->decimal('cash_cost', 22, 4)
                ->nullable()->default(0);  
                
            $table->decimal('insurance_cost', 22, 4)
                ->nullable()->default(0);
           $table->decimal('deposit', 22, 4)
                ->nullable()->default(0);
             $table->integer('estimation')
                ->nullable()->default(0);

            $table->integer('created_by')->nullable()->unsigned();
            $table->foreign('created_by')
                ->references('id')->on('users');
                
 $table->integer('service_staff')->nullable()->unsigned();
            $table->foreign('service_staff')
                ->references('id')->on('users');

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
        Schema::dropIfExists('garage_job_cards');
    }
}
