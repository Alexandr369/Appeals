<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appeals_status_history', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('appeal_id');
            $table->string('status');
            $table->unsignedInteger('changed_by')->nullable();
            $table->timestamps();

            $table->foreign('appeal_id')->references('id')->on('appeals_appeals')->onDelete('cascade');
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appeals_status_history');
    }
};
