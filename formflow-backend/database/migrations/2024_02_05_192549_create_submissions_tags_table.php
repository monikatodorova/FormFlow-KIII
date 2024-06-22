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
        Schema::create('submissions_tags', function (Blueprint $table) {
            $table->bigInteger('submission_id')->unsigned();
            $table->bigInteger('tag_id')->unsigned();

            $table->foreign('submission_id')->references('id')->on('submissions')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions_tags');
    }
};
