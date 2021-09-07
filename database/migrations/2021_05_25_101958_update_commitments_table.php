<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCommitmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('commitments', function (Blueprint $table) {
            // quantity - motion
            $table->integer('quantity_master')->nullable()->change();
            $table->renameColumn('quantity_master', 'motion_quantity_master');
            $table->integer('quantity_iteration')->nullable()->change();
            $table->renameColumn('quantity_iteration', 'motion_quantity_iteration');
            $table->integer('quantity_rich_content')->nullable()->change();
            $table->renameColumn('quantity_rich_content', 'motion_quantity_rich_content');

            // price - motion
            $table->renameColumn('price_master', 'motion_price_master');
            $table->renameColumn('price_iteration', 'motion_price_iteration');
            $table->renameColumn('price_rich_content', 'motion_price_rich_content');

            // quantity - acting
            $table->integer('acting_quantity_master')->nullable();
            $table->integer('acting_quantity_iteration')->nullable();
            $table->integer('acting_quantity_rich_content')->nullable();

            // price - acting
            $table->integer('acting_price_master')->nullable();
            $table->integer('acting_price_iteration')->nullable();
            $table->integer('acting_price_rich_content')->nullable();

            // used quantity - motion (used quantity will be incremented upon invoicing video item)
            $table->integer('used_motion_quantity_master')->nullable();
            $table->integer('used_motion_quantity_iteration')->nullable();
            $table->integer('used_motion_quantity_rich_content')->nullable();

            // used quantity - acting (used quantity will be incremented upon invoicing video item)
            $table->integer('used_acting_quantity_master')->nullable();
            $table->integer('used_acting_quantity_iteration')->nullable();
            $table->integer('used_acting_quantity_rich_content')->nullable();
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
            $table->dropColumn('used_acting_quantity_rich_content');
            $table->dropColumn('used_acting_quantity_iteration');
            $table->dropColumn('used_acting_quantity_master');

            $table->dropColumn('used_motion_quantity_rich_content');
            $table->dropColumn('used_motion_quantity_iteration');
            $table->dropColumn('used_motion_quantity_master');

            $table->dropColumn('acting_price_rich_content');
            $table->dropColumn('acting_price_iteration');
            $table->dropColumn('acting_price_master');

            $table->dropColumn('acting_quantity_rich_content');
            $table->dropColumn('acting_quantity_iteration');
            $table->dropColumn('acting_quantity_master');

            $table->renameColumn('motion_price_rich_content', 'price_rich_content');
            $table->renameColumn('motion_price_iteration', 'price_iteration');
            $table->renameColumn('motion_price_master', 'price_master');

            $table->renameColumn('motion_quantity_rich_content', 'quantity_rich_content');
            $table->integer('motion_quantity_rich_content')->change();
            $table->renameColumn('motion_quantity_iteration', 'quantity_iteration');
            $table->integer('motion_quantity_rich_content')->change();
            $table->renameColumn('motion_quantity_master', 'quantity_master');
            $table->integer('motion_quantity_rich_content')->change();
        });
    }
}
