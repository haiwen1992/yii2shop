<?php

use yii\db\Migration;

/**
 * Handles the creation of table `menu`.
 */
class m171229_021555_create_menu_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('menu', [
            'id' => $this->primaryKey(),
            'name'=>$this->string()->comment('名称'),
             'superior_menu'=>$this->string()->comment('上级菜单'),
            'route'=>$this->string()->comment('地址路由'),
            'sort'=>$this->integer()->comment('排序'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('menu');
    }
}
