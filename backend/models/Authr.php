<?php
namespace backend\models;

use yii\base\Model;

class Authr extends Model{
    public $name;
    public $description;
    public $role;
    //设置验证规则
    public function rules(){
        return[
          [['name','description'],'required'],
            ['role','safe'],
        ];
    }
    public function attributeLabels()
    {
        return[
            'name'=>'角色名称',
            'description'=>'描述',
            'quanxian'=>'权限',
        ];
    }

}