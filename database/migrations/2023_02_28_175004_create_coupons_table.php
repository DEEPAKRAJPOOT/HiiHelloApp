<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->string('custom_id')->nullable();
            $table->string('coupon')->nullable();
            $table->string('value')->nullable();
            $table->string('description')->nullable();
            $table->enum('type', ['percentage', 'full'])->nullable()->comment("Discount or full redemption coupon");
            $table->enum('is_universal', ['n', 'y'])->default('n')->comment("If yes, Coupon valid for all plans");
            $table->enum('is_reusable', ['n', 'y'])->default('n');
            $table->enum('is_self_hosted', ['n', 'y'])->default('y')->comment("If yes, Exclusive to our platform");
            $table->enum('is_active', ['n', 'y'])->default('y');
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();

            $table->foreign('vendor_id')
                ->references('id')
                ->on('coupon_vendors')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('plan_id')
                ->references('id')
                ->on('subscription_plans')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupons');
    }
}
