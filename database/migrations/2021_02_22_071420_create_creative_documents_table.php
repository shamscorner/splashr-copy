<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreativeDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('creative_documents', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type');
            $table->string('url');
            $table->foreignUuid('order_id')->constrained()->onDelete('cascade');
            $table->string('document_id'); // extract from url
            $table->boolean('is_changed')->default(false);
            // $table->integer('is_client_validated')->default(1); // 1 - default, 2 - disabled, 3 - validated
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
        Schema::dropIfExists('creative_documents');
    }
}
