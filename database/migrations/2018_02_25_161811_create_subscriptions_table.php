<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->uuid('uuid')->nullable();
            $table->string('prefix',20)->nullable();
            $table->integer('number')->nullable();
            $table->string('type',20)->nullable();
            $table->integer('plan_id')->unsigned()->nullable();
            $table->integer('instance_id')->unsigned()->nullable();
            $table->integer('currency_id')->unsigned()->nullable();
            $table->string('frequency',20)->nullable();
            $table->date('validity')->nullable();
            $table->decimal('amount',25,5)->default(0);
            $table->string('source',20)->nullable();
            $table->boolean('status')->default(0);
            $table->string('phone',20)->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city',50)->nullable();
            $table->string('state',50)->nullable();
            $table->string('zipcode',10)->nullable();
            $table->string('country',20)->nullable();
            $table->string('token',50)->nullable();
            $table->text('gateway_response')->nullable();
            $table->string('gateway_token')->nullable();
            $table->string('gateway_status',20)->nullable();
            $table->ipAddress('ip')->nullable();
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
        Schema::dropIfExists('subscriptions');
    }
}
