<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeletedContentTable extends Migration
{
    public function up (): void
    {
        $tables = config('helpdesk.tables');

        Schema::create($tables['deleted_contents'], function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
        });

        \Aviator\Helpdesk\Models\DeletedContent::updateOrCreate([
            'id' => 1,
        ], []);
    }

    public function down (): void
    {
        $tables = config('helpdesk.tables');

        Schema::dropIfExists($tables['deleted_contents']);
    }
}
