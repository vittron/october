<?php namespace Iocare\Letsencrypt\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateIocareLetsencryptLe extends Migration
{
    public function up()
    {
        Schema::create('iocare_letsencrypt_le', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('email', 255)->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('iocare_letsencrypt_le');
    }
}
