<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
// database/migrations/xxxx_xx_xx_xxxxxx_update_classes_table_add_stream_id.php
public function up(): void {
    Schema::table('classes', function (Blueprint $table) {
        // Add foreign key
        $table->foreignId('stream_id')->nullable()->after('class_name')->constrained('streams')->nullOnDelete();
        
        // Drop the old string column
        $table->dropColumn('stream');
    });
}

public function down(): void {
    Schema::table('classes', function (Blueprint $table) {
        $table->string('stream', 20)->default('');
        $table->dropForeign(['stream_id']);
        $table->dropColumn('stream_id');
    });
}

};
