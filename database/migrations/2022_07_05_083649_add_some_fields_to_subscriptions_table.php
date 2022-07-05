<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeFieldsToSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('email')->nullable()->after('custom_id');
            $table->string('payment_type')->nullable()->after('payment_date');
            $table->longText('receipt_data')->nullable()->after('payment_type');
            $table->string('original_transaction_id')->nullable()->after('receipt_data');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['email', 'payment_type', 'receipt_data', 'original_transaction_id']);
        });
    }
}
