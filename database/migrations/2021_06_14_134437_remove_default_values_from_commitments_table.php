<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveDefaultValuesFromCommitmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('commitments', function (Blueprint $table) {
            $table->integer('motion_quantity_master')->default(null)->change();
            $table->integer('motion_quantity_iteration')->default(null)->change();
            $table->integer('motion_quantity_rich_content')->default(null)->change();

            $table->integer('motion_price_master')->default(null)->nullable()->change();
            $table->integer('motion_price_iteration')->default(null)->nullable()->change();
            $table->integer('motion_price_rich_content')->default(null)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('commitments', function (Blueprint $table) {
            $table->integer('motion_price_rich_content')->default(250)->change();
            $table->integer('motion_price_iteration')->default(500)->change();
            $table->integer('motion_price_master')->default(500)->change();

            $table->integer('motion_quantity_rich_content')->default(60)->change();
            $table->integer('motion_quantity_iteration')->default(60)->change();
            $table->integer('motion_quantity_master')->default(60)->change();
        });
    }
}
