<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamClustersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_clusters', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('team_id');
            $table->json('data')->nullable(false);
            $table->integer('zoom')->nullable(false);
            $table->date('created_at')->index()->nullable(false);

            $table->foreign('team_id')->references('id')->on('teams');

            $table->unique(['team_id', 'created_at', 'zoom']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('team_clusters');
    }
}
