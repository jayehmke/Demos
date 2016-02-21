<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransferOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_order', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('inventoryTransferID')->index();
			$table->integer('inventoryTransferNo');
			$table->integer('creatorID');
			$table->integer('warehouseFromID');
			$table->integer('warehouseToID');
			$table->integer('followupInventoryTransferID');
			$table->date('date');
			$table->string('notes');
			$table->boolean('confirmed');
			$table->dateTime('added');
			$table->dateTime('lastModified');
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
        Schema::drop('transfer_order');
    }
}
