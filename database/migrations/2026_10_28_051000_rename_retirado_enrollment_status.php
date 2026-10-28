<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('inscripciones')->where('estado', 'retirado')->update(['estado' => 'no_inscrito']);
    }

    public function down(): void
    {
        DB::table('inscripciones')->where('estado', 'no_inscrito')->update(['estado' => 'retirado']);
    }
};
