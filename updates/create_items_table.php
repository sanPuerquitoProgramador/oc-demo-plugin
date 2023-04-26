<?php namespace Polilla\Demo\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreateItemsTable Migration
 */
class CreateItemsTable extends Migration
{
    public function up()
    {
        Schema::create('polilla_demo_items', function (Blueprint $table) {
            $table->increments('id');
            $table->text('item');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('polilla_demo_items');
    }
}
