<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('borrower_id');
            $table->string('business');
            $table->string('description');
            $table->float('amount');
            $table->integer('term');
            $table->enum('grade', ['A','B','C']);
            $table->float('interest_rate');
            $table->boolean('is_approved');
            $table->dateTime('approved_on');
            $table->boolean('is_accepted');
            $table->datetime('accepted_on');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loans');
    }
}
