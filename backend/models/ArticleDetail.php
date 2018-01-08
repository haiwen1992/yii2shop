<?php
namespace backend\models;

use yii\db\ActiveRecord;

class ArticleDetail extends  ActiveRecord{
    //设置规则
    public function rules(){
        return[
          [['content'],'required'],//不能为空
        ];
    }
    //设置名称
    public function attributeLabels(){
        return[
            'content'=>'详情',
        ];
    }
}