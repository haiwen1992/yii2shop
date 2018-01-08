<?php
namespace backend\models;

use yii\db\ActiveRecord;

class GoodsGallery extends ActiveRecord{
    //设置验证规则
    public function rules()
    {
        return[
            [['goods_id','path'],'required'],//不能为空
        ];
    }
}