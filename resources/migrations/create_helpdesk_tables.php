<?php

namespace Aviator\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHelpdeskTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = config('helpdesk.tables');

        Schema::create($tables['tickets'], function (Blueprint $table) {
            $table->increments('id');
            $table->char('uuid', 32)->unique();
            $table->unsignedInteger('user_id')->nullable();
            $table->string('name');
            $table->integer('content_id')->unsigned()->nullable();
            $table->string('content_type')->nullable();
            $table->string('status')->default('open');
            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = config('helpdesk.tableNames');

        foreach ($tables as $table) {
            Schema::drop($table);
        }
    }
}