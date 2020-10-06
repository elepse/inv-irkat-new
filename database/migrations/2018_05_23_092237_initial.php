<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Initial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_item');
            $table->JSON('comment');
            $table->dateTime('create_time');
            $table->string('creator', 255);
            $table->foreign('id_item')->references('id')->on('items');
        });

        Schema::create('owners', function (Blueprint $table) {
            $table->increments('owner_id');
            $table->string('last_name', 45);
            $table->string('first_name', 45);
            $table->string('father_name', 45);
        });

        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 500);
            $table->string('name_1c', 500);
            $table->string('inv_number', 20);
            $table->string('serial_number', 20);
            $table->decimal('price', 10, 2);
            $table->tinyInteger('count')->unsigned();
            $table->dateTime('create_time');
            $table->dateTime('edit_time');
            $table->string('last_owner')->unsigned();
            $table->string('last_location')->unsigned();
            $table->tinyInteger('status')->unsigned();
        });

        Schema::create('item_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('inventory_number', 20);
            $table->dateTime('create_time');
            $table->dateTime('edit_time');
        });

        Schema::create('item_links', function (Blueprint $table) {
            $table->integer('item_groups_id')->unsigned();
            $table->integer('items_id')->unsigned();
            $table->foreign('items_id')->references('id')->on('items');
            $table->foreign('item_groups_id')->references('id')->on('item_groups');
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('type')->null();
            $table->date('date');
            $table->integer('from_employee')->unsigned()->null();
            $table->integer('to_employee')->unsigned()->null();
            $table->dateTime('create_time');
            $table->dateTime('edit_time');
            $table->foreign('from_employee')->references('id')->on('owners');
            $table->foreign('to_employee')->references('id')->on('owners');
        });

        Schema::create('document_items', function (Blueprint $table) {
            $table->integer('document_id')->unsigned();
            $table->integer('items_id')->unsigned();
            $table->integer('item_group_id')->unsigned();
            $table->foreign('document_id')->references('id')->on('documents');
            $table->foreign('items_id')->references('id')->on('items');
            $table->foreign('item_group_id')->references('id')->on('item_groups');
        });

        Schema::create('locations', function (Blueprint $table) {
            $table->increments('loc_id');
            $table->string('loc_name', 45);
            $table->dateTime('create_time');
            $table->dateTime('edit_time');
        });

        Schema::create('location_history', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('type')->unsigned();
            $table->dateTime('create_time');
            $table->dateTime('edit_time');
        });

        Schema::create('location_links', function (Blueprint $table) {
            $table->integer('location_id')->unsigned();
            $table->integer('locations_history_id')->unsigned();
            $table->integer('items_id')->unsigned();
            $table->foreign('location_id')->references('id')->on('locations');
            $table->foreign('locations_history_id')->references('id')->on('location_history');
            $table->foreign('items_id')->references('id')->on('items');
        });

        Schema::create('document_files', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('document_id')->unsigned();
            $table->string('file', 255);
            $table->dateTime('create_time');
            $table->dateTime('edit_time');
            $table->foreign('document_id')->references('id')->on('documents');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::dropIfExists('documents');
        Schema::dropIfExists('document_files');
        Schema::dropIfExists('owners');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('location_history');
        Schema::dropIfExists('items_groups');
    }
}
