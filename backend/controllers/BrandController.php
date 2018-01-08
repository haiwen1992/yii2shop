<?php
namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\Brand;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;
use yii\web\UploadedFile;
// 引入鉴权类
use Qiniu\Auth;//七牛云
// 引入上传类
use Qiniu\Storage\UploadManager;//七牛云

class BrandController extends Controller
{
    //关闭验证
    public $enableCsrfValidation = false;
    //列表页查询所有数据
    public function actionIndex()
    {

        //调用模型处理数据
//  $brand = Brand::find()->all();
        //小于零时时删除 所以status查出大于零的所有数据
        $brand = Brand::find()->where(['>=','status','0'])->all();
        //调用视图分配数据
        return $this->render('index', ['brand' => $brand]);
    }
//处理图片上传
public function actionUpload(){
        $img = UploadedFile::getInstanceByName('file');
        $fileName = '/upload/'.uniqid().'.'.$img->extension;

        if ($img->saveAs(\Yii::getAlias('@webroot').$fileName,0)){
            //====================将图片上传到七牛云
// 需要填写你的 Access Key 和 Secret Key
            $accessKey ="sm0gFP4wvQe9ilXX9QdKj0n4PysYd-E6NvowIglS";
            $secretKey = "sfpuVe4z--vms18JAOfpVGd9DNTrqV94AP-RcYCv";
            $bucket = "yii2";
            $domain = 'p1eugxxb4.bkt.clouddn.com';
// 构建鉴权对象
            $auth = new Auth($accessKey, $secretKey);
// 生成上传 Token
            $token = $auth->uploadToken($bucket);
// 要上传文件的本地路径
            //$fileName = '/upload/1.jpg';
            $filePath = \Yii::getAlias('@webroot').$fileName;
// 上传到七牛后保存的文件名
            $key = $fileName;
// 初始化 UploadManager 对象并进行文件的上传。
            $uploadMgr = new UploadManager();
// 调用 UploadManager 的 putFile 方法进行文件的上传。
            list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
           // echo "\n====> putFile result: \n";
            if ($err !== null) {
                //说明有错误
                //var_dump($err);
                //上传失败
                return Json::encode(['error'=>1]);
            } else {
                //没错 上传成功
                //var_dump($ret);
                //上传成功
                $url = "http://{$domain}/{$key}";
                return Json::encode(['url'=>$url]);
            }

            //===================================
            //上传成功
//            return Json::encode(['url'=>$fileName]);
        }else{
            //上传失败
            return Json::encode(['error'=>1]);
        }
}
//添加
    public function actionAdd()
    {
        $request = new Request();
        $model = new Brand();
        if ($request->isPost) {
            //加载表单数据
            $model->load($request->post());
//         var_dump($request->post());
//         die;

            if ($model->validate()) {
                // 保存
                $model->save();
                //设置提示信息
                \Yii::$app->session->setFlash('success', '添加成功');
                //跳转或主页面
                return $this->redirect(['brand/index']);
            } else {
                //验证失败打印错误信息
                var_dump($model->getErrors());
            }
        } else {
            //显示表单
            return $this->render('add', ['model' => $model]);
        }
    }

//修改
    public function actionEdit($id)
    {
        $request = new Request();
        //根据id获取数据
        $model = Brand::findOne(['id' => $id]);
        if ($request->isPost) {
            //加载数据
            $model->load($request->post());
            if ($model->validate()) {
                  //保存
                $model->save();
                //设置提示信息
                \Yii::$app->session->setFlash('success','修改成功');
                //跳转主页面
                return $this->redirect(['brand/index']);
            }else{
                //打印错误信息
                var_dump($model->getErrors());
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    //删除
    public function actionDelete($id){
//    var_dump($id);
//    die;
    //根据id
     $model = Brand::findOne(['id'=>$id]);
//     var_dump($model);
//     die;
//     //get方式传过来
////     $delete->load($request->get());
    $model->status = -1;
    $model->save();
//        Brand::updateAll(['status'=>-1],['id'=>$id]);
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