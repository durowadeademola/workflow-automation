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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('widget_agent_name')->nullable()->after('webhook_url');
            $table->string('widget_primary_color')->nullable()->after('widget_agent_name');
            $table->string('widget_greeting')->nullable()->after('widget_primary_color');
            $table->string('widget_wa_number')->nullable()->after('widget_greeting');
            $table->text('widget_system_prompt')->nullable()->after('widget_wa_number');
            $table->string('widget_position')->nullable()->after('widget_system_prompt');
            $table->json('widget_quick_replies')->nullable()->after('widget_position');
            $table->unsignedInteger('widget_auto_open_delay')->nullable()->after('widget_quick_replies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'widget_agent_name',
                'widget_primary_color',
                'widget_greeting',
                'widget_wa_number',
                'widget_system_prompt',
                'widget_position',
                'widget_quick_replies',
                'widget_auto_open_delay',
            ]);
        });
    }
};
