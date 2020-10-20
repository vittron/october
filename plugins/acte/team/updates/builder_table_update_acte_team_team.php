<?php namespace Acte\Team\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateActeTeamTeam extends Migration
{
    public function up()
    {
        Schema::table('acte_team_team', function($table)
        {
            $table->text('description')->nullable()->unsigned(false)->default(null)->change();
        });
    }
    
    public function down()
    {
        Schema::table('acte_team_team', function($table)
        {
            $table->string('description', 255)->nullable()->unsigned(false)->default(null)->change();
        });
    }
}