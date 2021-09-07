<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormatVideoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('format_video', function (Blueprint $table) {
            $table->id();
            $table->foreignId('format_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('video_id')->constrained()->onDelete('cascade');
            $table->unique(['format_id', 'video_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('format_video');
    }
}
