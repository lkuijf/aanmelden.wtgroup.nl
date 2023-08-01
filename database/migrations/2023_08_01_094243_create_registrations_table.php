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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('first_name');
            $table->string('last_name');
            $table->date('birth_date');
            $table->string('email');
            $table->string('company');
            $table->text('diet_wishes');
            $table->boolean('partner');
            $table->string('partner_name');
            $table->integer('children_amount')->default(0);
            $table->string('children_ages');
            $table->integer('page_id')->nullable()->default(null);
            $table->string('page_slug_at_registration');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registrations');
    }
};
