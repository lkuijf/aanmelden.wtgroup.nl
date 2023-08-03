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
            $table->string('full_name')->default('');
            $table->string('first_name')->default('');
            $table->string('last_name')->default('');
            $table->date('birth_date')->default('');
            $table->string('email')->default('');
            $table->string('company')->default('');
            $table->text('diet_wishes')->default('');
            $table->boolean('partner')->default(0);
            $table->string('partner_name')->default('');
            $table->integer('children_amount')->default(0);
            $table->string('children_ages')->default('');
            $table->integer('page_id')->nullable()->default(null);
            $table->string('page_slug_at_registration')->default('');
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
