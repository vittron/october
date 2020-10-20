<?php namespace Hypoflex\Sslchecker\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHypoflexSslcheckerDomains extends Migration
{
    public function up()
    {
        Schema::create('hypoflex_sslchecker_domains', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('domain', 255);
            $table->string('valid_from', 255)->nullable();
            $table->string('valid_to', 255)->nullable();
            $table->string('diff', 45)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('active');
            $table->string('ipaddress',255)->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('hypoflex_sslchecker_domains');
    }
}
