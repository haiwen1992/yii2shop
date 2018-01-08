<?php
namespace backend\models;

use yii\db\ActiveRecord;

class GoodsDayCount extends  ActiveRecord{
    public static function primaryKey()
    {
        return ['day'];
    }
}