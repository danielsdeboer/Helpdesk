<?php

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
            $table->unsignedInteger('user_id');
            $table->integer('content_id')->unsigned()->nullable();
            $table->string('content_type')->nullable();
            $table->string('status')->default('open');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create($tables['generic_contents'], function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('body');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create($tables['actions'], function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('subject_id')->unsigned()->nullable();
            $table->string('subject_type')->nullable();
            $table->integer('object_id')->unsigned()->nullable();
            $table->string('object_type')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create($tables['assignments'], function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ticket_id');
            $table->unsignedInteger('assigned_to');
            $table->unsignedInteger('created_by')->nullable();
            $table->boolean('is_visible')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create($tables['due_dates'], function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ticket_id');
            $table->date('due_on');
            $table->unsignedInteger('created_by')->nullable();
            $table->boolean('is_visible')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create($tables['internal_replies'], function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ticket_id');
            $table->text('body');
            $table->unsignedInteger('created_by');
            $table->boolean('is_visible')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create($tables['external_replies'], function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ticket_id');
            $table->text('body');
            $table->unsignedInteger('created_by');
            $table->boolean('is_visible')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create($tables['pools'], function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_lead');
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create($tables['pool_assignments'], function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ticket_id');
            $table->unsignedInteger('pool_id');
            $table->unsignedInteger('created_by')->nullable();
            $table->boolean('is_visible')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create($tables['closings'], function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ticket_id');
            $table->text('note')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->boolean('is_visible')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create($tables['openings'], function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ticket_id');
            $table->text('note')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->boolean('is_visible')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create($tables['notes'], function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ticket_id');
            $table->text('body');
            $table->unsignedInteger('created_by')->nullable();
            $table->boolean('is_visible')->default(0);
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
        $tables = config('helpdesk.tables');

        foreach ($tables as $table) {
            Schema::drop($table);
        }
    }
}