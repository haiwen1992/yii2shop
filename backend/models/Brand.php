<?php

namespace backend\models;

use yii\db\ActiveRecord;

class Brand extends ActiveRecord{

   //设置验证规则
    public function rules(){
        return[
            [['name','intro','sort','status','logo'],'required'],//不能为空
//            //上传文件的验证规则
//            ['imgFile','file','extensions'=>['jpg','png','gif'],'maxSize'=>1024*1024,'skipOnEmpty'=>false],
        ];
    }
    //设置名称
    public function attributeLabels()
    {
        return [
            'name'=>'名称',
            'intro'=>'简介',
            'logo'=>'图片',
            'sort'=>'排序',
            'status'=>'状态'
        ];
    }
}