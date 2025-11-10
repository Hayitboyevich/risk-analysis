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
        Schema::create('illegal_objects', function (Blueprint $table) {
            $table->id();
            $table->string('lat');
            $table->string('long');
            $table->string('address', 400);
            $table->foreignId('region_id')->index()->nullable()->constrained();
            $table->foreignId('district_id')->index()->nullable()->constrained();
            $table->integer('score');
            $table->integer('status')->default(0);
            $table->bigInteger('created_by')->index()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('illegal_objects');
    }
};
