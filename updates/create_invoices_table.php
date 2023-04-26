<?php namespace Polilla\Demo\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreateInvoicesTable Migration
 */
class CreateInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('polilla_demo_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->text('invoice');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('polilla_demo_invoices');
    }
}
