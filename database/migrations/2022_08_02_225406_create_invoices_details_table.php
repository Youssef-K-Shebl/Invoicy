<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices_details', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50);
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('product', 50);
            $table->string('section', 50);
            $table->enum('value_status', ['غير مدفوعة', 'مدفوعة جزئيا', 'مدفوعه']);
            $table->text('note')->nullable();
            $table->string('user', 300);
            $table->date('payment_date')->nullable();
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
        Schema::dropIfExists('invoices_details');
    }
}
