<?php
namespace  backend\models;

use yii\db\ActiveRecord;

class Article extends ActiveRecord{
    //设置验证规则
  public function rules(){
      return[
        [['name','intro','sort','status'],'required'],//不能为空
      ];
  }
  public function attributeLabels(){
      //设置名称
      return[
        'name'=>'名称',
        'intro'=>'简介',
        'article_category_id'=>'文章分类id',
        'sort'=>'排序',
        'status'=>'状态',
        'create_time'=>'创建时间',
      ];
  }

}