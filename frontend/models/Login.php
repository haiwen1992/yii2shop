<?php
namespace frontend\models;

use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use yii\base\Model;

class Login extends Model{
  public $username;
  public $password_hash;
  public $red;
  public function rules(){
      return[
        [['username','password_hash'],'required'],//不能为空
        //  [['red'],'']
      ];
  }
  //登录方法
    public function login(){
    $username = Member::findOne(['username'=>$this->username]);
//      var_export($username);
//      die;
      if ($username){
          if (\Yii::$app->security->validatePassword($this->password_hash,$username->password_hash)){
          //如果点击保存密码设置有效时间
              if($this->red){
                  //保存时间
                  \Yii::$app->user->login($username,7*24*3600);

              }else{
                  \Yii::$app->user->login($username);
              }
              //可以登录
              //将用户信息保存到session中
              \Yii::$app->user->login($username,$this->red);
              return true;
          }else{
              //打印错误信息
              $this->addError('password_hash','密码错误');
          }
      }else{
          //用户名不存在
          $this->addError('username','用户名不存在');
      }
      return false;
    }
}