<?php namespace Acte\Team\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class UpdateTableActeTeamTeam extends Migration
{
    public function up()
    {
        Schema::table('acte_team_team', function($table)
        {
            $table->integer('sort_order')->default(0)->change();
        });
    }

    public function down()
    {
        Schema::table('acte_team_team', function($table)
        {
          $table->integer('sort_order')->change();
        });
    }
}
