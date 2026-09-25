<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('courierapis')) {
            Schema::table('courierapis', function (Blueprint $table) {
                if (!Schema::hasColumn('courierapis', 'client_context')) {
                    $table->string('client_context', 255)->nullable()->after('client_secret')->comment('Client-Context for Carrybee etc.');
                }
                if (!Schema::hasColumn('courierapis', 'webhook_secret')) {
                    $table->string('webhook_secret', 255)->nullable()->after('webhook_url')->comment('Webhook secret / token header');
                }
                if (!Schema::hasColumn('courierapis', 'default_store_id')) {
                    $table->string('default_store_id', 100)->nullable()->after('status')->comment('Default pickup store id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('courierapis')) {
            Schema::table('courierapis', function (Blueprint $table) {
                $cols = [];
                if (Schema::hasColumn('courierapis', 'client_context')) $cols[] = 'client_context';
                if (Schema::hasColumn('courierapis', 'webhook_secret')) $cols[] = 'webhook_secret';
                if (Schema::hasColumn('courierapis', 'default_store_id')) $cols[] = 'default_store_id';
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
};
