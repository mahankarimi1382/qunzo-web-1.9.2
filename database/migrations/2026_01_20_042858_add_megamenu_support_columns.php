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
        Schema::table('navigations', function (Blueprint $table) {
            $table->tinyInteger('megamenu_type')->default(1)->after('has_megamenu');
        });

        Schema::table('megamenu_items', function (Blueprint $table) {
            $table->renameColumn('featured_image', 'preview_image');
            $table->string('preview_title')->nullable()->after('url');
            $table->text('preview_description')->nullable()->after('preview_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('navigations', function (Blueprint $table) {
            $table->dropColumn('megamenu_type');
        });

        Schema::table('megamenu_items', function (Blueprint $table) {
            $table->renameColumn('preview_image', 'featured_image');
            $table->dropColumn(['preview_title', 'preview_description']);
        });
    }
};
