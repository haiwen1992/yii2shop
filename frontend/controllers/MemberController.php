<?php
namespace frontend\controllers;


use Codeception\Module\Redis;
use frontend\models\Cart;
use frontend\models\Member;
use frontend\models\Login;
use frontend\models\SignatureHelper;
use yii\web\Controller;
use yii\web\Request;

class MemberController extends Controller{
    //关闭验证
    public $enableCsrfValidation = false;
    //用户表
    public function actionIdex(){
       $member = Member::find()->all();
       return $this->render('index', ['member' => $member]);
    }
    public function actionAdd(){
        $request = new Request();
      $model = new Member();
      if($request->isPost){
         $model->load($request->post(),'');

          //密码加密
          $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password_hash);
//          var_dump($model->password_hash);
//          die;
          $model->created_at = time();
          if($model->validate()){

           //保存
              $model->save('false');
              //发邮件
              \Yii::$app->mailer->compose()
                  //Yii::$app->mailer->compose()
                  ->setFrom('18180921302@163.com')
                  ->setTo($model->email)
                  ->setSubject('邮件主题:京西商城注册')
                  ->setHtmlBody('
         <span style="color: #1006F1">京西商城</span>开业!!!全国最低
         ')
                  ->send();
             //发邮件技术 上
//              //设置提示信息
//              \Yii::$app->session->setFlash('success','添加成功');
              //跳转到首页
             return $this->redirect(['site/index']);
          }
              //验证失败打印错误信息
              var_dump($model->getErrors());
      }
       //注册表单
        return $this->render('member',['model'=>$model]);
    }
    //验证用户名唯一
    public function actionValidateUser($username){
        $username = Member::findOne(['username'=>$username]);
        if($username){
            echo 'false';
        }else{
            echo 'true';
        }
    }
    //登录
    public function actionLogin(){
        //登录表单
        $model = new Login();
        $request = new Request();
//        $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post(),'');

//        var_dump($request->post());
//        die;
            if($model->login()){
                $id = \Yii::$app->user->id;
                $cookies = \Yii::$app->request->cookies;
                $carta = $cookies->getValue('cart');
                if ($carta) {
                    $cart = unserialize($carta);
                    foreach ($cart as $k => $v) {
                        $goods = Cart::findOne(['goods_id' => $k, 'member_id' => \Yii::$app->user->id]);
                        if ($goods) {
                            Cart::updateAll(['amount' => $goods->amount + $v], ['member_id' => \Yii::$app->user->id, 'goods_id' => $k]);
                        } else {
                            $model = new Cart();
                            $model->goods_id = $k;
                            $model->amount = $v;
                            $model->member_id = \Yii::$app->user->id;
                            $model->save();
                        }
                    }
                    $cookies = \Yii::$app->response->cookies;
                    $cookies->remove('cart');//删除cookie中的数据
                }
                //提示信息
                //\Yii::$app->session->setFlash('success','登录成功');
                //成功跳转页面
//               return $this->render('@web/index.html');
                return $this->redirect('/index.html');
            }
        }
        return $this->render('login',['model'=>$model]);
    }
    //注销
    public function actionLogout(){
        \Yii::$app->user->logout();
        return $this->redirect('index.html');
//        \Yii::$app->user->login();
//        echo '已退出';
    }
    //手机验证码
    public function actionSms($phone){
        //正则验证手机号格式
        $code = rand(1000,9999);
      $result = \Yii::$app->sms->send($phone,['code'=>$code]);
//     var_dump($result);
//     die;
       if($result->Code == 'OK'){
           //短信发送成功
           //将短信验证码保存到redis中
           $redis = new \Redis();
           $redis->connect('127.0.0.1');
           $redis->set('code_'.$phone,$code,5*60);
           return 'true';
       }else{
           //发送失败
           return '短信发送失败';
       }
      //ii::$app->sms->send();
//        $params = array ();
//
//        // *** 需用户填写部分 ***
//
//        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
//        $accessKeyId = "LTAIU5ZXCYaTXprZ";
//        $accessKeySecret = "t1vmIZr5ESWgDh5H98ny3zjfsPyqtx";
//
//        // fixme 必填: 短信接收号码
//        $params["PhoneNumbers"] = "18180921302";
//
//        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
//        $params["SignName"] = "严海文";
//
//        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
//        $params["TemplateCode"] = "SMS_120120269";
//
//        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
//        $params['TemplateParam'] = Array (
//            "code" => rand(1000,9999),
//           // "product" => "阿里通信"
//        );
//
//        // fixme 可选: 设置发送短信流水号
//       // $params['OutId'] = "12345";
//
//        // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
//        //$params['SmsUpExtendCode'] = "1234567";
//
//
//        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
//        if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
//            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
//        }
//
//        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
//
//             $helper = new SignatureHelper();
//        // 此处可能会抛出异常，注意catch
//        $content = $helper->request(
//            $accessKeyId,
//            $accessKeySecret,
//            "dysmsapi.aliyuncs.com",
//            array_merge($params, array(
//                "RegionId" => "cn-hangzhou",
//                "Action" => "SendSms",
//                "Version" => "2017-05-25",
//            ))
//        );
//          var_dump($content);
////        return $content;
    }
    //图形验证
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            //
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'minLength'=>3,
                'maxLength'=>3,
            ],
        ];
    }
//发邮件技术
public function actionEmail(){
      $requst =\Yii::$app->mailer->compose()
    //Yii::$app->mailer->compose()
        ->setFrom('18180921302@163.com')
         ->setTo('18180921302@163.com')
         ->setSubject('邮件主题:京西商城搞活动')
         ->setHtmlBody('
         <span style="color: #1006F1">邮件内容</span>大煞风景是单例饭卡手动阀世纪东方
         ')
         ->send();
        var_dump($requst);
}
 //redis练习
    public function actionRedis(){
      $redis = new \Redis();
      $redis->set('name','张三');
      $redis->expire('name',30);
      $redis->set('age',18);
      if($redis->ttl('name')){
         $redis->incr('age');
      }else{
          $redis->decr('age');
      }

    }
}
