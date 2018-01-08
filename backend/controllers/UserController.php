<?php
namespace  backend\controllers;


use backend\filters\RbacFilter;
use backend\models\Login;
use backend\models\User;
use yii\web\Controller;
use yii\web\Request;

class UserController extends Controller{
    //列表页
    public function actionIndex(){
        $model = User::find()->all();
        return $this->render('index',['model'=>$model]);
    }
    //添加
    public function actionAdd(){
        $request = new Request();
        $model = new User();
        $authManager = \Yii::$app->authManager;
        if($request->isPost){
            $model->load($request->post());
            //密码加密
            $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password_hash);
            if($model->validate()){

                //保存
                $model->save();
                $id = $model->getId();
                if($model->role){
                    foreach ($model->role as $a){
                        $ps = $authManager->getRole($a);
                        $authManager->assign($ps,$id);
                    }
                }
                //设置提示信息
                \Yii::$app->session->setFlash('success','添加成功');
                //跳转页面
                return $this->redirect(['user/index']);
            }else{
                //打印错误信息
                var_dump($model->getErrors());
            }
        }else{
            $arr = $authManager->getRoles();
            $ar = [];
            foreach ($arr as $b){
                $ar[$b->name] =$b->description;
            }
            //添加表单
            return $this->render('add',['model'=>$model,'ar'=>$ar]);
        }
    }

    //修改
        public function actionEdit($id){
        $request = new Request();
            $model = new User();
            //根据id获取数据
            $model = User::findOne(['id'=>$id]);
            $authManager = \Yii::$app->authManager;
            $roles = $authManager->getRoles();
            $ar = [];
            foreach ($roles as $role){
                $ar[$role->name] = $role->description;
            }
            if($request->isPost){
                //加载表单数据
                $model->load($request->post());
                //密码加密
                $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password_hash);
                if($model->validate()){
                    //保存
                    $model->save();
                    $id = $model->getId();
                    if($model->role){
                        $authManager->revokeAll($id);
                        foreach ($model->role as $c){
                            $role = $authManager->getRole($c);
                            $authManager->assign($role,$id);
                        }
                    }
                    //设置提示信息
                    \Yii::$app->session->setFlash('succes','修改成功');
                    //跳转主页面
                    return $this->redirect(['user/index']);
                }else{
                    //错误信息
                    var_dump($model->getErrors());
                }
            }
            if($model->role){
                $roles = $authManager->getRolesByUser($id);
                foreach ($roles as $role){
                    $model->role[] = $role->name;
                }
            }
            return $this->render('add',['model'=>$model,'ar'=>$ar]);
        }
        //删除
        public function actionDelete($id)
        {
            $model = User::deleteAll(['id' => $id]);
            //跳转
          return $this->redirect(['user/index']);
        }
   //登录
    public function actionLogin(){
            //登录表单
    $model = new Login();
    $request = \Yii::$app->request;
    if($request->isPost){
        $model->load($request->post());
//        var_dump($request->post());
//        die;
         if($model->login()){
             //提示信息
             \Yii::$app->session->setFlash('success','登录成功');
             //成功跳转页面
            return $this->redirect(['user/index']);
         }
    }
    return $this->render('login',['model'=>$model]);
    }
    //注销
    public function actionLogout(){
        \Yii::$app->user->login();
        echo '已退出';
    }
//修改密码
public function actionEdi(){
    $request = new Request();
    $model = new User();
    //获取当前登录用户名
  $username = \Yii::$app->user->identity->username;

    //根据用户名名查询数据库里面对应的密数据
    //查询结果赋给一个变量
    $model = User::findOne(['username'=>$username]);
    //数据库查出的密码
     $password_hash  =  $model->password_hash;
       if($request->isPost){
           //加载表单数据
           $model->load($request->post());
           //验证数据库里面的密码和表单提交的密码
           if(\Yii::$app->security->validatePassword($model->password_hash,$password_hash)){
               //新密码加加密处理
               if($model->validate()){
                 $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password_h);
                 //保存新密码
                  $model->save();
                  \Yii::$app->session->setFlash('success','修改成功');
                  return $this->redirect(['user/index']);
               }else{
                   //打印错误信息
                   var_dump($model->getErrors());
                   die;
               }

               }else{
               $model->addError('password_hash','旧密码错误');
           }

           }
           //因为修改密码时旧密码要回显所以显示表单前把回显去掉
           $model->password_hash = '';
         return $this->render('password',['model'=>$model]);
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

