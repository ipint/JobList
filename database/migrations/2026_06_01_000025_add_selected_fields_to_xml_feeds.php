<?php

use App\Models\XmlFeed;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('xml_feeds', function (Blueprint $table): void {
            $table->json('selected_fields')->nullable()->after('experience_levels');
        });

        DB::table('xml_feeds')
            ->update(['selected_fields' => json_encode(XmlFeed::defaultXmlFields())]);
    }

    public function down(): void
    {
        Schema::table('xml_feeds', function (Blueprint $table): void {
            $table->dropColumn('selected_fields');
        });
    }
};
