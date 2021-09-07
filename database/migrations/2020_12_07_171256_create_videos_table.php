<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('client_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignUuid('campaign_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignUuid('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('service_offer')->nullable();
            $table->text('description')->nullable();
            $table->text('audience')->nullable();
            $table->string('key_message')->nullable();
            $table->string('cta')->nullable();
            $table->boolean('is_voice_over')->default(false);
            $table->boolean('is_soundtrack')->default(false);
            $table->text('other_requirements')->nullable();
            $table->string('thumbnail')->nullable();
            $table->integer('status')->default(1); // '1 - ordered, 2 - pending, 3 - proposal_received, 4 - video_received, 5 - storyboard_received, 6 - finished'
            $table->boolean('is_visited')->default(false);
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
        Schema::dropIfExists('videos');
    }
}
