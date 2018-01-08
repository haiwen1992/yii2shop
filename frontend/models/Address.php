<?php
namespace frontend\models;

use yii\db\ActiveRecord;

class Address extends ActiveRecord{
    //设置验证规则
    public function rules()
    {
        return[
            [['name','cmbprovince','detailed','phone ','cmbarea'],'required'],
            [['default','cmbcity','id'],'self'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'name' => '名称',
            'cmbprovince' => '省',
            'cmbcity' => '市',
            'detailed'=>'县',
            'phone'=>'手机',
            'cmbarea'=>'详细地址',
        ];
    }
}