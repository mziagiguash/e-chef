<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('instructors', function (Blueprint $table) {
            $table->json('name')->nullable()->change();
            $table->json('designation')->nullable()->change();
            $table->json('bio')->nullable()->change();
            $table->json('title')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('instructors', function (Blueprint $table) {
            $table->json('name')->nullable(false)->change();
            $table->json('designation')->nullable(false)->change();
            $table->json('bio')->nullable(false)->change();
            $table->json('title')->nullable(false)->change();
        });
    }
};
