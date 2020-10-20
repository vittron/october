<?php namespace Iocare\Letsencrypt\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateIocareLetsencryptLe extends Migration
{
    public function up()
    {
        Schema::table('iocare_letsencrypt_le', function($table)
        {
            $table->string('domain', 255);
            $table->text('webroot');
            $table->text('certlocation');
            $table->increments('id')->unsigned(false)->change();
        });
    }
    
    public function down()
    {
        Schema::table('iocare_letsencrypt_le', function($table)
        {
            $table->dropColumn('domain');
            $table->dropColumn('webroot');
            $table->dropColumn('certlocation');
            $table->increments('id')->unsigned()->change();
        });
    }
}
