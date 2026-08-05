<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Repair gateways.input_form rows that were stored double-encoded.
 *
 * GatewaySeeder passed json_encode($inputForm) into an Eloquent model whose
 * input_form is cast to 'object', so the value was encoded twice. Reading it
 * back gave a string rather than an object, and the manual-gateway edit screen
 * fataled with "foreach() argument must be of type array|object, string given".
 *
 * The seeder is fixed; this repairs installs that already ran it.
 */
class FixDoubleEncodedGatewayInputForm extends Migration {
    public function up() {
        if (!Schema::hasTable('gateways') || !Schema::hasColumn('gateways', 'input_form')) {
            return;
        }

        foreach (DB::table('gateways')->select('id', 'input_form')->get() as $row) {
            $raw = $row->input_form;

            if (!is_string($raw) || $raw === '') {
                continue;
            }

            $once = json_decode($raw, true);

            // Correctly stored rows decode straight to an array. A double-encoded
            // row decodes to a *string* that is itself valid JSON — re-store that
            // inner string so one decode yields the object the views expect.
            if (is_string($once) && json_decode($once, true) !== null) {
                DB::table('gateways')->where('id', $row->id)->update(['input_form' => $once]);
            }
        }
    }

    public function down() {
        // Nothing to undo: the previous state was corrupt data, not a schema change.
    }
}
