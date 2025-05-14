<?php

use App\BaseMigrations\CreateMoldsTableBase;

class CreateNodeTypesTable extends CreateMoldsTableBase
{
    /**
     * 模型名
     *
     * @var string
     */
    protected $model = \July\Node\NodeType::class;

    /**
     * 填充文件
     *
     * @var string|null
     */
    protected $seeder = \July\Node\Seeds\NodeTypeSeeder::class;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->string('id', 32)->primary();
            $table->string('label');
            $table->string('default_tpl')->nullable();
            $table->string('description')->nullable();

            // 是否预设 —— 预设字段不可删除，只能通过程序添加
            $table->boolean('is_reserved')->default(false);

            $table->string('langcode', 12);

            $table->timestamps();
        });

        $this->seed();
    }
}
