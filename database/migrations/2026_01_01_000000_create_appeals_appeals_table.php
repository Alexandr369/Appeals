<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appeals_appeals', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('status')->default('pending');
            $table->string('reason_key');
            $table->text('explanation');

            // Snapshot of the sanction at the time the appeal was submitted.
            $table->string('sanction_type')->nullable();
            $table->string('sanction_reason')->nullable();
            $table->timestamp('sanction_date')->nullable();
            $table->string('sanction_duration')->nullable();
            $table->string('sanction_staff')->nullable();

            // Reserved for the Pro version (automatic unban routing).
            $table->string('sanction_source')->nullable();
            $table->string('sanction_reference')->nullable();

            $table->text('staff_response')->nullable();
            $table->unsignedInteger('handled_by')->nullable();
            $table->timestamp('handled_at')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('handled_by')->references('id')->on('users')->onDelete('set null');

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appeals_appeals');
    }
};
