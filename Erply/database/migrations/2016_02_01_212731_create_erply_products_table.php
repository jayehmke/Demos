<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateErplyProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('erply_products', function (Blueprint $table) {
            //

            $table->increments('id');
            $table->integer('productID');
            $table->string('name');
            $table->string('code');
            $table->string('code2')->nullable();
            $table->string('manufacturerName')->nullable();
            $table->decimal('cost')->nullable();
            $table->string('groupName')->nullable();
            $table->string('categoryName')->nullable();
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
        Schema::drop('erply_products', function (Blueprint $table) {
            //
        });
    }
}
