<?php namespace Acte\Team\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class UpdateTableActeTeamTeam2 extends Migration
{
    public function up()
    {
        Schema::table('acte_team_team', function($table)
        {
            $table->string('name', 64)->nullable()->change();
            $table->string('position', 64)->nullable()->change();
            $table->string('description', 255)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('acte_team_team', function($table)
        {
            $table->string('name')->nullable()->change();
            $table->string('position')->nullable()->change();
            $table->string('description')->nullable()->change();
        });
    }
}
