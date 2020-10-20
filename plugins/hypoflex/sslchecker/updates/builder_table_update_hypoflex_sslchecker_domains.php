<?php namespace Hypoflex\Sslchecker\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHypoflexSslcheckerDomains extends Migration
{
    public function up()
    {
        Schema::table('hypoflex_sslchecker_domains', function($table)
        {
            $table->dateTime('last_check_date')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('hypoflex_sslchecker_domains', function($table)
        {
            $table->dropColumn('last_check_date');
        });
    }
}
