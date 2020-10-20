<?php namespace Acte\Team\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateActeTeamTeam extends Migration
{
    public function up()
    {
        Schema::create('acte_team_team', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('sort_order');
            $table->boolean('is_active')->nullable();
            $table->string('name')->nullable();
            $table->string('position')->nullable();
            $table->string('description')->nullable();
            $table->text('socials')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('acte_team_team');
    }
}
