<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('leads', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->text('title');
      $table->text('name')->nullable()->default(null);
      $table->text('second_name')->nullable()->default(null);
      $table->text('last_name')->nullable()->default(null);
      $table->date('birthdate')->nullable()->default(null);
      $table->text('phone');
      $table->text('email')->nullable()->default(null);
      $table->text('comment')->nullable()->default(null);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('leads');
  }
};
