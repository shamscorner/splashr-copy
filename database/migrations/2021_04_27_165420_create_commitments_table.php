<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommitmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commitments', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignUuid('company_id')->constrained()->onDelete('cascade');
            $table->integer('quantity_master')->default(60);
            $table->integer('quantity_iteration')->default(60)->nullable();
            $table->integer('quantity_rich_content')->default(60)->nullable();
            $table->integer('price_master')->default(500);
            $table->integer('price_iteration')->default(500);
            $table->integer('price_rich_content')->default(250);
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
        Schema::dropIfExists('commitments');
    }
}
