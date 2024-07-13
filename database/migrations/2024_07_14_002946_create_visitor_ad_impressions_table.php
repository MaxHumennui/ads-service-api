<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitorAdImpressionsTable extends Migration
{
    public function up()
    {
        Schema::create('visitor_ad_impressions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visitor_id')->constrained()->onDelete('cascade');
            $table->foreignId('ad_id')->constrained()->onDelete('cascade');
            $table->integer('impressions')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('visitor_ad_impressions');
    }
}
