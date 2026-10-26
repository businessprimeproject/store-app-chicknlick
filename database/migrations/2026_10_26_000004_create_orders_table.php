<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->string('email')->index();
            $table->string('status')->default('Pending')->index();
            $table->text('delivery_address')->nullable();
            $table->text('special_instructions')->nullable();
            $table->timestamp('submitted_at')->nullable()->index();
            $table->decimal('total', 12, 2)->default(0);
            $table->json('items');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
