<?php
namespace backend\models;

use yii\base\Model;

class Login extends Model{
    public $username;
    public $password_hash;
    public $red;


    public function rules()
    {
        return[
            [['username','password_hash'],'required'],//不能为空
            [['red'],'safe'],
        ];
    }
    //登录方法
    public function login(){
        //验证账号密码
//       $model = new User();
        $user = User::findOne(['username'=>$this->username]);
        //(['username'=>$this->username]);
//        var_dump($this->username);die;
        if($user){
//                 var_dump($user->password_hash);die;
            //用户名存在验证密码
//            var_dump(\Yii::$app->security->validatePassword($this->password_hash,$user->password_hash));
//            die;
            if(\Yii::$app->security->validatePassword($this->password_hash,$user->password_hash)){
                //red点击记住密码
                if($this->red){
                    //设置有效时间
//                    var_dump($this->red);die;
                  $this->red = 7*24*3600;
                }
                //可以登录
                //将用户信息保存到session
                \Yii::$app->user->login($user,$this->red);
                return true;
            }else{
                //错误信息
                $this->addError('password_hash','密码不正确');
            }

        }else{
            //用户名不存在
            $this->addError('username','用户名不存在');
        }
        return false;
    }
}