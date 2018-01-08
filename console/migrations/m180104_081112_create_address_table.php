<?php

use yii\db\Migration;

/**
 * Handles the creation of table `address`.
 */
class m180104_081112_create_address_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('address', [
            'id' => $this->primaryKey(),
            'name'=>$this->string()->comment('姓名'),
            'cmbprovince'=>$this->string()->comment('省'),
            'cmbcity'=>$this->string()->comment('市'),
            'cmbarea'=>$this->string()->comment('县'),
            'detailed'=>$this->string()->comment('详细地址'),
            'phone'=>$this->string()->comment('手机号'),
            'default'=>$this->string()->comment('默认'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('address');
    }
}
