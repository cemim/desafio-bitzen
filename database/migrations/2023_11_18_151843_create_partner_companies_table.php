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
        Schema::create('partner_companies', function (Blueprint $table) {
            $table->id();
            $table->string("razao_social");
            $table->string("nome_fantasia");
            $table->string("cnpj");
            $table->date("dt_fundacao");
            $table->string("email_responsavel");
            $table->string("nome_responsavel");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_companies');
    }
};
