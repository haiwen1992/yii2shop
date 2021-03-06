<?php

use yii\db\Migration;

/**
 * Handles the creation of table `article`.
 */
class m171221_053728_create_article_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('article', [
            'id' => $this->primaryKey()->comment('主键id'),
            'name'=>$this->string()->comment('名称'),
            'intro'=>$this->string()->comment('简介'),
            'article_category_id'=>$this->integer()->comment('文章分类id'),
            'sort'=>$this->integer()->comment('排序'),
            'status'=>$this->integer()->comment('状态'),
            'create_time'=>$this->integer()->comment('创建时间')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('article');
    }
}
