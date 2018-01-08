<?php
namespace backend\models;


use yii\rbac\Permission;
use yii\base\Model;


class AuthItem extends Model {
    public $name;
    public $description;
    public $role;
    //设置添加场景
    const SCENARIO_ADD_PERMISSION  = 'add-permmission';//添加的权限场景
   //const SCENARIO_EDIT_PERMISSION = 'edit-permmission';
    //设置验证规则
    public function rules(){
        return[
          [['name','description'],'required'],//不为空
            ['role','safe'],
            ['name','weiyi','on'=>self::SCENARIO_ADD_PERMISSION],
            //['name','xiugai','on'=>self::SCENARIO_EDIT_PERMISSION]
        ];
    }
    public function weiyi(){
        $authManager = \Yii::$app->authManager;
        $model = $authManager->getPermission($this->name);
        if($model){
            $this->addError('name','权限已存在');
        }

    }


    //设置名称
    public function attributeLabels()
    {
        return [
            'name'=>'权限名称(路由)',
            'description'=>'描述',
            'quanxian'=>'权限',
        ];
    }
    public function save(){
          $authManager = \Yii::$app->authManager;
        //保存
        $authitem = new Permission();
        $authitem->name = $this->name;
        $authitem->description = $this->description;
        return $authManager->add($authitem);
    }
    public function da($id){
        $authManager = \Yii::$app->authManager;
        //保存
        $authitem = new Permission();
        $authitem->name = $this->name;
        $authitem->description = $this->description;
        if($id != $this->name){
            $p = $authManager->getPermission($this->name);
            if($p){
                $this->addError('name','权限已存在');
                return false;
            }
        }
        return $authManager->update($id,$authitem);
    }
}