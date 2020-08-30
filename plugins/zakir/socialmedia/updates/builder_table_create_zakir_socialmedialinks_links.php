<?php namespace Zakir\SocialMedia\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateZakirSocialmedialinksLinks extends Migration
{
    public function up()
    {
        Schema::create('zakir_socialmedialinks_links', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title', 200)->nullable();
            $table->string('icon', 200)->nullable();
            $table->string('target', 200)->nullable();
            $table->string('url', 200)->nullable();
            $table->string('rel', 200)->nullable();
            $table->string('background_color', 200)->nullable();
            $table->string('color', 200)->nullable();
            $table->integer('sort_order')->nullable()->default(0);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('zakir_socialmedialinks_links');
    }
}
