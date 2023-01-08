<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeGarageStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('garage_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('view_name')->nullable();
            $table->string('color')->nullable();
            $table->string('type')->nullable();
            $table->integer('sort_order')->nullable();
            $table->integer('business_id')->nullable();
                $table->boolean('is_completed_status')->default(0);
            $table->text('sms_template')->nullable();
              $table->text('email_subject')->nullable();
            $table->text('email_body')->nullable();
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
        Schema::dropIfExists('garage_statuses');
    }
}
