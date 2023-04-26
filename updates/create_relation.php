<?php namespace Polilla\Demo\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreateItemsTable Migration
 */
class CreateItemsInvoiceTable extends Migration
{
    public function up()
    {
        Schema::create('polilla_demo_invoice_item', function(Blueprint $table)
        {
            $table->integer('invoice_id')->unsigned();
            $table->integer('item_id')->unsigned();
            $table->primary(['invoice_id', 'item_id']);
            $table->integer('sort_order')->default(0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('polilla_demo_invoice_item');
    }
}