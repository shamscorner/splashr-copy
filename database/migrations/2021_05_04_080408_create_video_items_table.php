<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_items', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignUuid('company_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('video_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('order_id')->constrained()->onDelete('cascade');
            $table->string('name')->nullable();
            $table->tinyInteger('type')->default(0); // 0 - master, 1 - iteration, 2 - rich content
            $table->tinyInteger('status')
                ->default(0); // 0 - none, 1 - in_progress, 2 - needs_review, 3 - approved, 4 - paid
            $table->dateTime('paid_on')->nullable();
            $table->json('url')->nullable();
            $table->string('reference_number')->nullable();
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
        Schema::dropIfExists('video_items');
    }
}
