<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurposeVideoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purpose_video', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purpose_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('video_id')->constrained()->onDelete('cascade');
            $table->unique(['purpose_id', 'video_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purpose_video');
    }
}
