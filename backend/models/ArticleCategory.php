<?php
namespace backend\models;

use yii\db\ActiveRecord;

class ArticleCategory extends ActiveRecord{
//设置验证规则
    public function rules(){
        return[
            [['name','intro','sort','status'],'required'],//不能为空
        ];
    }
    //设置名称
    public function attributeLabels()
    {
        return [
            'name'=>'名称',
            'intro'=>'简介',
            'sort'=>'排序',
            'status'=>'状态'
        ];
    }
}