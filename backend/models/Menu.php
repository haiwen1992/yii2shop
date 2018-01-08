<?php
namespace backend\models;

use yii\db\ActiveRecord;

class Menu extends ActiveRecord {
    //设置验证规则
    public function rules()
    {
        return[
          [['name','superior_menu','route','sort'],'required'],
        ];
    }
    public function attributeLabels()
    {
        return[
             'name'=>'名称',
            'superior_menu'=>'上级菜单',
            'route'=>'路由(地址)',
            'sort'=>'排序',
        ];
    }
}