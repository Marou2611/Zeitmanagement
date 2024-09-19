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
        // Remove old, unused fields monday_bachelor ... friday master

        Schema::table('timetables', function (Blueprint $table) {
            $table->dropColumn('monday_bachelor');
            $table->dropColumn('tuesday_bachelor');
            $table->dropColumn('wednesday_bachelor');
            $table->dropColumn('thursday_bachelor');
            $table->dropColumn('friday_bachelor');
            $table->dropColumn('monday_master');
            $table->dropColumn('tuesday_master');
            $table->dropColumn('wednesday_master');
            $table->dropColumn('thursday_master');
            $table->dropColumn('friday_master');

            // Use array for master and bachelor instead
            $table->json('bachelor')->nullable();
            $table->json('master')->nullable();

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('timetables', function (Blueprint $table) {
            $table->string('monday_bachelor')->nullable();
            $table->string('tuesday_bachelor')->nullable();
            $table->string('wednesday_bachelor')->nullable();
            $table->string('thursday_bachelor')->nullable();
            $table->string('friday_bachelor')->nullable();
            $table->string('monday_master')->nullable();
            $table->string('tuesday_master')->nullable();
            $table->string('wednesday_master')->nullable();
            $table->string('thursday_master')->nullable();
            $table->string('friday_master')->nullable();

            $table->dropColumn('bachelor');
            $table->dropColumn('master');
        });
    }
};
