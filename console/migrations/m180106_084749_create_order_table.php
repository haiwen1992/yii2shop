<?php

use yii\db\Migration;

/**
 * Handles the creation of table `order`.
 */
class m180106_084749_create_order_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('order', [
            'id' => $this->primaryKey(),
            'member_id'=>$this->integer()->comment('用户id'),
            'name'=>$this->string()->comment('收货人'),
            'provine'=>$this->string()->comment('省'),
            'city'=>$this->string()->comment('市'),
            'area'=>$this->string()->comment('县'),
            'address'=>$this->string()->comment('详细地址'),
            'tel'=>$this->string()->comment('电话'),
            'delivery_id'=>$this->integer()->comment('配送方式id'),
            'delivery_name'=>$this->string()->comment('配送方式名称'),
            'delivery_price'=>$this->float()->comment('配送方式价格'),
            'payment_id'=>$this->integer()->comment('支付方式id'),
            'payment_name'=>$this->string()->comment('支付方式名称'),
            'total'=>$this->decimal()->comment('订单金额'),
            'status'=>$this->integer()->comment('订单状态（0已取消1待付款2待发货3待收货4完成）'),
            'trade_no'=>$this->string()->comment('第三方支付交易'),
            'create_time'=>$this->integer()->comment('创建时间'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('order');
    }
}