<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('order_number')->unique();
            $table->string('idempotency_key')->unique();
            $table->unsignedInteger('user_id')->nullable();
            $table->enum('status', [
                'CREATED',
                'PENDING_PAYMENT',
                'PAID',
                'PROCESSING',
                'SHIPPED',
                'COMPLETED',
                'FAILED',
                'CANCELLED',
            ])->default('CREATED');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}