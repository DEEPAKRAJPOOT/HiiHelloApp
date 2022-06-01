<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();

            $table->string('custom_id')->nullable();
            $table->enum('period', ['daily', 'weekly', 'monthly', 'yearly'])->nullable();
            $table->string('interval')->nullable();
            $table->integer('amount')->nullable();
            $table->enum('is_popular', ['y', 'n'])->default('n')->nullable();
            $table->enum('is_active', ['y', 'n'])->default('y')->nullable();
            
            $table->softDeletes();
            
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
        Schema::dropIfExists('subscription_plans');
    }
}
