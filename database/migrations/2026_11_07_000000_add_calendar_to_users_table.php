<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The user's preferred calendar system ("jalali" or "gregorian"); null follows the language.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('calendar', 16)->nullable()->after('locale');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('calendar');
        });
    }
};
