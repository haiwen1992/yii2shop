<?php
namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\AuthItem;
use backend\models\Authr;
use yii\rbac\Permission;
use yii\rbac\Role;
use yii\web\Controller;
use yii\web\Request;

class AuthItemController extends Controller{
    //列表
    public function actionIndex(){
        $authManager = \Yii::$app->authManager;
      $model = $authManager->getPermissions();
      return $this->render('index',['model'=>$model]);
    }
    //添加
   public function actionAddit(){
//      $authManager = \Yii::$app->authManager;
       $model = new AuthItem();
       $model->scenario = AuthItem::SCENARIO_ADD_PERMISSION;
       $request = \Yii::$app->request;
       if($request->isPost){
           $model->load($request->post());
//           var_dump($model->load($request->post()));
//           die;
           if($model->validate()){
               $model->save();
//               var_dump($authitem->description);
//              die;
//               $authManager->add($authitem);
//               $authManager->add($authitem);
               return $this->redirect(['auth-item/index']);
           }
       }
      return $this->render('addit',['model'=>$model]);
   }
   //删除
    public function actionDelete($name){
        $authManager = \Yii::$app->authManager;
//        $model = new AuthItem();
        $model = $authManager->getPermission($name);
        $authManager->remove($model);
        return $this->redirect(['index']);
//      $model = $authManager->remove($name);
//        $authitem = new Permission();
    }
    //修改
    public function actionEdit($name){
        $authManager = \Yii::$app->authManager;
        $authManager = $authManager->getPermission($name);
        $model = new AuthItem();
        //$model->scenario = AuthItem::SCENARIO_EDIT_PERMISSION;
        $model->name = $authManager->name;
        $model->description = $authManager->description;
         $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
               if($model->da($name)){
                   //修改成功
                   \Yii::$app->session->setFlash('success','修改成功');
                   return $this->redirect(['auth-item/index']);
               }
            }else{
                //d打印错误信息
            }
        }
            return $this->render('addit',['model'=>$model]);

    }
//    //测试插件
//    public function actionRbac(){
//        return $this->render('auth');
//    }
//角色添加
public function actionAddr(){
    $request = new Request();
    $authManager = \Yii::$app->authManager;
    $model = new Authr();
    $request = \Yii::$app->request;
    if($request->isPost){
        $model->load($request->post());
//        var_dump($request->post());
//        die;
        if($model->validate()){
            //创建一个角色
            $role = new Role();
            $role->name=$model->name;
            $role->description = $model->description;
//            $role->quanxian = $model->quanxian;
            //保存数据表
            $authManager->add($role);
            if($model->role){
                foreach ($model->role as $quanx){
                    $quan = $authManager->getPermission($quanx);
                    $authManager->addChild($role, $quan);
                }
            }
            \Yii::$app->session->setFlash('success', '添加成功');
            return $this->redirect(['auth-item/indexr']);
        }

    }
    $authManager = \Yii::$app->authManager;
    $roles = $authManager->getPermissions();
    $role = [];
    foreach ($roles as $a){
        $role[$a->name]=$a->description;
    }
//    var_dump($role);
//    die;
    return $this->render('addr',['model'=>$model,'role'=>$role]);

}
//列表
    public function actionIndexr(){
          $authManager = \Yii::$app->authManager;
//        $authManager = \Yii::$app->authManager;
        $model = $authManager->getRoles();
//        var_dump($model);
//        die;
        return $this->render('indexr',['model'=>$model]);
    }
    //修改
    public function actionEditr($name){
        $authManager = \Yii::$app->authManager;
        //获取要修改的角色
        $role = $authManager->getRole($name);
        //实例化表单模型
        $model = new Authr();
        $model->name = $role->name;
        $model->description = $role->description;
        $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
               //保存
                $role = new Role();
                $role->name = $model->name;
                $role->description = $model->description;
                $authManager->update($name,$role);
                //取出所有的角色关联
                $authManager->removeChildren($role);
                //从新角色关联权限
                foreach ($model->role as $Name){
                    $per = $authManager->getPermission($Name);
                    $authManager->addChild($role,$per);
                }
            }
            \Yii::$app->session->setFlash('success', '修改成功');
            return $this->redirect(['auth-item/indexr']);
        }
      //获取角色拥有权限
      $model->role= [];
      $quanxian = $authManager->getPermissionsByRole($name);
      foreach ($quanxian as $quanx){
          $model->role[] =  $quanx->name;
      }
//      var_dump($model);
//        die;
      //得到所有的权限
        $rol=[];
        $permissions = $authManager->getPermissions();
        foreach($permissions as $per){
            $rol[$per->name] = $per->description;
        }
//        var_dump($rol);
//        die;
        //显示表单
        return $this->render('addr',['model'=>$model,'role'=>$rol]);
    }
    //删除
    public function actionDeleter($name){
        $authManager = \Yii::$app->authManager;
//        $model = new AuthItem();
        $model = $authManager->getRole($name);
        $authManager->remove($model);
        return $this->redirect(['indexr']);
    }
    public function behaviors()
    {
        return[
            'rbac'=>[
                'class'=>RbacFilter::className(),
                'except'=>['login','logout','upload','captcha'],
            ],

        ];
    }

}