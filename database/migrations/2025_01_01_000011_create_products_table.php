<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('public_id', 20)->unique();      // p1, p2 ... (kept from original)
            $table->string('sku', 40)->unique();
            $table->string('name');
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('price');               // in pesos (integer as in original)
            $table->string('unit')->nullable();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->unsignedInteger('reviews')->default(0);
            $table->boolean('wholesale')->default(false);
            $table->boolean('featured')->default(false);
            $table->json('suited');                         // ['home','commercial','bulk']
            $table->json('tags');                            // ['Best Seller','Bulk Friendly']
            $table->text('desc');
            $table->json('specs');                          // [['Pack size','10 balls'], ...]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
