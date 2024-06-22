<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->integer('projects');
            $table->integer('submissions');
            $table->integer('archive');
            $table->boolean('thank_you_page')->default(false);
            $table->boolean('api')->default(false);
            $table->boolean('export')->default(false);
            $table->boolean('spam_filter')->default(false);
            $table->integer('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
