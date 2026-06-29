<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->foreignId('categoria_id')->after('stock')->constrained('categorias')->onDelete('cascade');
            $table->foreignId('proveedor_id')->nullable()->after('categoria_id')->constrained('proveedores')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropForeign(['categoria_id']);
            $table->dropForeign(['proveedor_id']);
            $table->dropColumn(['categoria_id', 'proveedor_id']);
        });
    }
};