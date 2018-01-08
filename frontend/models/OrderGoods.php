<?php
namespace frontend\models;

use yii\db\ActiveRecord;

class OrderGoods extends ActiveRecord{
    public function rules()
    {
        return[
            [['goods_name', 'logo','price','amount','total'], 'required'],
        ];
    }
}