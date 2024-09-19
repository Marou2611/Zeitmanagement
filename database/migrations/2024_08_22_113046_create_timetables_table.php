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
        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('teaching_load');        // Aktueller Lehrumfang in SWS
            $table->integer('max_hours_per_day');    // Maximale SWS-Zahl pro Tag
            $table->boolean('use_cn0003');           // Nutzung CN0003 für Lehrveranstaltung
            $table->boolean('use_group_rooms');      // Nutzung Gruppentischräume/Stuhlkreis für
            $table->text('comments')
                ->nullable();                        // Anmerkungen
            $table->json('monday_bachelor');         // Montag Zeiten Bachelor
            $table->json('tuesday_bachelor');        // Dienstag Zeiten Bachelor
            $table->json('wednesday_bachelor');      // Mittwoch Zeiten Bachelor
            $table->json('thursday_bachelor');       // Donnerstag Zeiten Bachelor
            $table->json('friday_bachelor');         // Freitag Zeiten Bachelor
            $table->json('monday_master');           // Montag Zeiten Master
            $table->json('tuesday_master');          // Dienstag Zeiten Master
            $table->json('wednesday_master');        // Mittwoch Zeiten Master
            $table->json('thursday_master');         // Donnerstag Zeiten Master
            $table->json('friday_master');           // Freitag Zeiten Master
            $table->foreignId('semester_id')->constrained();
            $table->foreignId('lecturer_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetables');
    }
};
