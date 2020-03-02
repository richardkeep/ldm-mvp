<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDefendantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('defendants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->unsignedInteger('lawsuit_id');
            $table->unsignedInteger('submitter_id');
            $table->timestamps();

            $table->foreign('lawsuit_id')->references('id')->on('lawsuits')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('submitter_id')->references('id')->on('submitters')
                ->onDelete('cascade')->onUpdate('cascade');
        });

        DB::statement('ALTER TABLE defendants CHANGE id id int(6) zerofill NOT NULL AUTO_INCREMENT FIRST');
        DB::statement('ALTER TABLE defendants AUTO_INCREMENT = 800000');
        DB::statement('ALTER TABLE defendants CHANGE lawsuit_id lawsuit_id int(6) zerofill NOT NULL');
        DB::statement('ALTER TABLE defendants CHANGE submitter_id submitter_id int(6) zerofill NOT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('defendants');
    }
}
