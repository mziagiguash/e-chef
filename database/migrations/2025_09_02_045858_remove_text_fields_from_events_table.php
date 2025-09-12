<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
            $table->text('description')->nullable()->change();
            $table->string('location')->nullable()->change();
            $table->string('topic')->nullable()->change();
            $table->text('goal')->nullable()->change();
            $table->string('hosted_by')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('title')->nullable(false)->change();
            $table->text('description')->nullable(false)->change();
            $table->string('location')->nullable(false)->change();
            $table->string('topic')->nullable(false)->change();
            $table->text('goal')->nullable(false)->change();
            $table->string('hosted_by')->nullable(false)->change();
        });
    }
};
