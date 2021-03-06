<?php

declare(strict_types=1);

namespace Tests\Commands\Upgrade\V6_2_0\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::create('tags', function (Blueprint $table) {
            $table->increments('id')->comment('标签主键');
            $table->string('name', 20)->default('')->comment('标签名');
            $table->string('slug')->default('')->comment('slug');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Schema::dropIfExists('tags');
    }
}
