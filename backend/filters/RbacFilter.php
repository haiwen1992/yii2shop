<?php
namespace backend\filters;

use yii\base\ActionFilter;
use yii\web\HttpException;

class RbacFilter extends ActionFilter{

    public function beforeAction($action)
    {
       // return \Yii::$app->user->can($action->uniqueId);//判断当前用户是否拥有权限
//       $action->uniqueId;
//        return false;
        if(!\Yii::$app->user->can($action->uniqueId)){
            //用户没有登录 让用户登录
            if(\Yii::$app->user->isGuest){
                //跳转到登录页面
                return $action->controller->redirect(\Yii::$app->user->loginUrl)->send();
            }
          throw new HttpException(403,'你没有该功能操作权限请联系超级管理员');
        }
        return true;
    }
}