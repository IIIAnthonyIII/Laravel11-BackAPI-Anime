<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('type', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('status', ['A', 'I', 'E'])->default('A');
            $table->unsignedBigInteger('user_create')->nullable();
            $table->foreign('user_create')->references('id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('user_modifies')->nullable();
            $table->foreign('user_modifies')->references('id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('user_delete')->nullable();
            $table->foreign('user_delete')->references('id')->on('users')->onDelete('set null');
            $table->dateTime('date_delete')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('type');
    }
};
