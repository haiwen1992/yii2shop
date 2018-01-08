<?php
namespace backend\models;

use yii\db\ActiveRecord;

class GoodsIntro extends ActiveRecord{
    //设置验证规则
    public function rules()
    {
        return[
            [['content'],'required'],//不能为空
        ];
    }
    //设置名称
    public function attributeLabels(){
        return[
            'content'=>'商品详情描述',
        ];
    }
}