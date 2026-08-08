<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class ExtendOrderStatusEnum extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_status_check");

        DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_status_check CHECK (status IN (
            'CREATED', 'PENDING_PAYMENT', 'PAID',
            'PACKING', 'IN_WAREHOUSE', 'ON_DELIVERY', 'DELIVERED',
            'FAILED', 'CANCELLED'
        ))");
    }

    public function down()
    {
        DB::statement("ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_status_check");

        DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_status_check CHECK (status IN (
            'CREATED', 'PENDING_PAYMENT', 'PAID', 'PROCESSING', 'SHIPPED', 'COMPLETED',
            'FAILED', 'CANCELLED'
        ))");
    }
}