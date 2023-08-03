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
            $table->string('full_name')->nullable()->default('');
            $table->string('first_name')->nullable()->default('');
            $table->string('last_name')->nullable()->default('');
            $table->date('birth_date')->nullable()->default('1980-01-01');
            $table->string('email')->nullable()->default('');
            $table->string('company')->nullable()->default('');
            $table->text('diet_wishes')->nullable()->default('');
            $table->boolean('partner')->nullable()->default(0);
            $table->string('partner_name')->nullable()->default('');
            $table->integer('children_amount')->nullable()->default(0);
            $table->string('children_ages')->nullable()->default('');
            $table->integer('page_id')->nullable()->default(null);
            $table->string('page_slug_at_registration')->nullable()->default('');
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
