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
        Schema::create('machine_events', function (Blueprint $table) {
            $table->id();
            $table->string('machine_id'); // ou foreignId se tiver tabela
            $table->string('state');
            $table->integer('total_production');
            $table->integer('good_count');
            $table->integer('reject_count');
            $table->decimal('temperature', 8, 2);
            $table->decimal('vibration', 8, 2);
            $table->decimal('availability', 5, 2);
            $table->decimal('performance', 5, 2);
            $table->decimal('quality', 5, 2);
            $table->decimal('oee', 5, 2);
            $table->timestamp('event_time');
            $table->integer('produced');
            $table->integer('rejected');
            $table->timestamps();
            $table->index(['machine_id', 'event_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_events');
    }
};
