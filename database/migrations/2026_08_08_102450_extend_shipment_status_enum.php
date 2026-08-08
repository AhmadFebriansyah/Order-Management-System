<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class ExtendShipmentStatusEnum extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE shipments DROP CONSTRAINT IF EXISTS shipments_status_check");

        DB::statement("ALTER TABLE shipments ADD CONSTRAINT shipments_status_check CHECK (status IN (
            'PENDING', 'PACKING', 'IN_WAREHOUSE', 'ON_DELIVERY', 'DELIVERED'
        ))");
    }

    public function down()
    {
        DB::statement("ALTER TABLE shipments DROP CONSTRAINT IF EXISTS shipments_status_check");

        DB::statement("ALTER TABLE shipments ADD CONSTRAINT shipments_status_check CHECK (status IN (
            'PENDING', 'SHIPPED', 'DELIVERED'
        ))");
    }
}