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
        Schema::create('monitors', function (Blueprint $table) {
            $table->id();
            $table->string('url')->unique();
            $table->integer('check_interval')->default(5);
            $table->integer('threshold')->default(3);
            $table->string('status')->default('pending')->index();
            $table->timestamp('last_checked_at')->nullable()->index();
            $table->float('uptime_percentage')->nullable();
            $table->timestamps();

            $table->index(['last_checked_at', 'check_interval']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitors');
    }
};
