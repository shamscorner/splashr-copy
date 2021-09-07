<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyCreditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_credit', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('credit_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('company_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('manager_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->nullable();
            $table->integer('quantity')->default(1);
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
        Schema::dropIfExists('company_credit');
    }
}
