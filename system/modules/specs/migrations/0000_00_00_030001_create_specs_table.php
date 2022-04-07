<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpecsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('specs', function (Blueprint $table) {
            // id
            $table->string('id', 32)->primary();

            // 标签
            $table->string('label', 32);

            // 描述
            $table->string('description', 255)->nullable();

            // 展示表格
            $table->boolean('table_status')->default(true);

            // 默认排序字段
            $table->string('default_sort_field')->nullable();

            // 默认排序方式
            $table->string('default_sort_mode')->nullable();

            // 表格的配置信息
            $table->string('table_config', 255)->nullable();

            // 展示列表
            $table->boolean('list_status')->default(true);

            // 列表的布局
            $table->string('list_item', 255)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('specs');
    }
}
