<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instances', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('db_id')->unsigned()->nullable();
            $table->string('domain')->nullable();
            $table->string('email')->nullable();
            $table->string('first_name',50)->nullable();
            $table->string('last_name',50)->nullable();
            $table->string('status',20)->nullable();
            $table->string('activation_token',50)->nullable();
            $table->integer('plan_id')->unsigned()->nullable();
            $table->string('frequency',20)->nullable();
            $table->integer('currency_id')->unsigned()->nullable();
            $table->boolean('is_trial')->default(0);
            $table->date('date_of_expiry')->nullable();
            $table->integer('max_customer')->default(0);
            $table->integer('max_document')->default(0);
            $table->text('address_line_1')->nullable();
            $table->text('address_line_2')->nullable();
            $table->string('city',50)->nullable();
            $table->string('state',50)->nullable();
            $table->string('zipcode',20)->nullable();
            $table->string('country',10)->nullable();
            $table->string('phone',20)->nullable();
            $table->boolean('maintenance_mode')->default(0);
            $table->text('maintenance_mode_message')->nullable();
            $table->text('exclude_ip_from_maintenance')->nullable();
            $table->string('api_key')->nullable();
            $table->boolean('is_update_pending')->default(0);
            $table->string('timezone',50)->nullable();
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
        Schema::dropIfExists('instances');
    }
}
