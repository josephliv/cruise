<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadMailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_mails', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email_from');
            $table->bigInteger('agent_id');
            $table->string('subject');
            $table->text('body');
            $table->string('attachment');
            $table->dateTime('received_date');
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
        Schema::dropIfExists('lead_mails');
    }
}
