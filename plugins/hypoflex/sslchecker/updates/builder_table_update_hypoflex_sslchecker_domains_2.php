<?php namespace Hypoflex\Sslchecker\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHypoflexSslcheckerDomains2 extends Migration
{
    public function up()
    {
        Schema::table('hypoflex_sslchecker_domains', function($table)
        {
            $table->text('partial')->nullable();
            $table->text('ipaddress')->nullable()->unsigned(false)->default(null)->change();
        });
    }
    
    public function down()
    {
        Schema::table('hypoflex_sslchecker_domains', function($table)
        {
            $table->dropColumn('partial');
            $table->string('ipaddress', 255)->nullable()->unsigned(false)->default(null)->change();
        });
    }
}
