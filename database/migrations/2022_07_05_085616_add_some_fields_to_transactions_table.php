<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeFieldsToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('email')->nullable()->after('custom_id');
            $table->string('payment_type')->nullable()->after('subscription_id');
            $table->string('transaction_id')->nullable()->after('razorpay_signature');
            $table->string('original_transaction_id')->nullable()->after('transaction_id');
            $table->string('web_order_line_item_id')->nullable()->after('original_transaction_id');
            $table->dateTime('purchase_date')->nullable()->after('web_order_line_item_id');
            $table->dateTime('original_purchase_date')->nullable()->after('purchase_date');
            $table->dateTime('subscription_end_date')->nullable()->after('original_purchase_date');
            $table->longText('receipt_data')->nullable()->after('subscription_end_date');
            $table->string('in_app_ownership_type')->nullable()->after('receipt_data');
            $table->string('subscription_group_identifier')->nullable()->after('in_app_ownership_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['email', 'payment_type', 'transaction_id', 'original_transaction_id', 'web_order_line_item_id', 'purchase_date', 'original_purchase_date', 'subscription_end_date', 'receipt_data', 'in_app_ownership_type', 'subscription_group_identifier']);
        });
    }
}
