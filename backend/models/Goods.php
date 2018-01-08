<?php
namespace backend\models;

use yii\db\ActiveRecord;

class Goods extends ActiveRecord{
  //设置验证规则
    public function rules()
    {
        return[
            [['name','goods_category_id',
            'brand_id','market_price','shop_price',
             'stock','is_on_sale','status','sort',
           'view_times','logo'],'required'],//不能为空
        ];
    }
    //设置名称
    public function attributeLabels()
    {
        return [
            'name'=>'商品名称',
            'sn'=>'货号',
            'logo'=>'商品LOGO',
            'goods_category_id'=>'所属商品分类',
            'brand_id'=>'所属品牌',
            'market_price'=>'市场价格',
            'shop_price'=>'商品价格',
            'stock'=>'库存',
            'is_on_sale'=>'是否在售',
            'status'=>'状态',
            'sort'=>'排序',
            'create_time'=>'添加时间',
            'view_times'=>'浏览次数',
        ];
    }
}