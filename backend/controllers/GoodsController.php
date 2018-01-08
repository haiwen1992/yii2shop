<?php
namespace  backend\controllers;

use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsDayCount;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\Request;
use yii\helpers\Json;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
// 引入鉴权类
use Qiniu\Auth;//七牛云
// 引入上传类
use Qiniu\Storage\UploadManager;//七牛云

class GoodsController extends Controller{
    //关闭验证
    public $enableCsrfValidation = false;
    //列表页
    public function actionIndex(){
        //搜索
        $query = Goods::find();
        $name = \Yii::$app->request->get('name');
        $sn = \Yii::$app->request->get('sn');
        $min = \Yii::$app->request->get('min');
        $max = \Yii::$app->request->get('max');
        if (count($name) > 0) {
            $query->andWhere(['like', 'name', $name]);
        }
        if (count($sn) > 0) {
            $query->andWhere(['like', 'sn', $sn]);
        }
        if (count($min) > 0) {
            $query->andWhere(['like', 'shop_price', $min]);
        }
        if (count($max) > 0) {
            $query->andWhere(['like', 'market_price', $max]);
        }
        //分页 数据总记录数 每页显示的条数 当前第几页
        $total = $query->andWhere(['status'=>1])->count();
        $pager = new Pagination([
            'totalCount' => $total,
            'defaultPageSize' => 2
        ]);
        $good = $query->limit($pager->limit)->offset($pager->offset)->andwhere(['status' => 1])->all();
//        //每页显示的条数
//        $pageSize = 2;
//        $pager = new Pagination();
//        $pager->totalCount = $total;
//        $pager->defaultPageSize =2;
//        //查询加分页条件
//        $good = Goods::find()->limit($pager->limit)->offset($pager->offset)->all();
        return $this->render('index',['good'=>$good,'pager'=>$pager]);
    }
    //商品详情
    public function actionShow($id){
        //商品详情展示
        $model = GoodsIntro::find()->where(['goods_id'=>$id])->one();
        return $this->render('show',['model'=>$model]);
    }
    //处理图片上传
    public function actionUploader(){
        $img = UploadedFile::getInstanceByName('file');
        $fileName = '/upload/goods/'.uniqid().'.'.$img->extension;

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
                //上传成功http://p1eugxxb4.bkt.clouddn.com//upload1/5a3e857b35287.jpg
//                $url = "http://p1eugxxb4.bkt.clouddn.com/".$fileName;
                $url = "http://{$domain}/{$key}";
                return Json::encode(['url'=>$url]);
            }
        }
    }
    //添加
    public function actionAdd(){
        $request = new Request();
        $model = new Goods();
        //调用商品详情模型
        $mode = new GoodsIntro();
        $day = date('Y-m-d',time());
        if ($request->isPost) {
            //加载商品表单数据
            $model->load($request->post());
            //加载商品详情表单数据
            $mode->load($request->post());
//            var_dump($model);die;
            if ($model->validate()) {
                $goodsdyc = GoodsDayCount::find()->where(['day'=>date('Y-m-d',time())])->one();
                if($goodsdyc){
                    $goodsdyc->count += 1;
                    $goodsdyc->save();
                }else{
                    $goodsdyc = new GoodsDayCount();
                    $goodsdyc->day = $day;
                    $goodsdyc->count = 1;
                    $goodsdyc->save();
                }
                //
                $model->sn = date('Ymd').sprintf("%05d",$goodsdyc->count);
                $model->save();
                $model->create_time = time();
                // 保存
                $model->save(false);
                $mode->goods_id=$model->id;
                $mode->save(false);
                //设置提示信息
                \Yii::$app->session->setFlash('success', '添加成功');
                //跳转或主页面
                return $this->redirect(['goods/index']);
            } else {
                //验证失败打印错误信息
                var_dump($model->getErrors());
            }
        } else {
            //品牌分类
            $brang = Brand::find()->all();
            $a = arrayHelper::map($brang,'id','name');
            //商品分类
            $goodscategory = GoodsCategory::find()->all();
            $b = arrayHelper::map($goodscategory,'id','name');
            //显示表单
            return $this->render('add', ['model' => $model,'mode'=>$mode,'a'=>$a,'b'=>$b]);
        }
    }
    //修改
    public function actionEdit($id){
       $request = new Request();
       //根据id获取数据
        $model = Goods::findOne($id);
        $mode = GoodsIntro::findOne(['goods_id'=>$id]);
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            //加载商品详情表单数据
            $mode->load($request->post());
//            var_dump($request->post());
//            die;
            if($model->validate()){
              //保存
           $model->save();

           $mode->save();
           //设置提示信息
            \Yii::$app->session->setFlash('success','修改成功');
            //跳转主页
                return $this->redirect(['goods/index']);
            }else{
                //打印错误信息
                var_dump($model->getErrors());
            }
        }
        //品牌分类
        $brang = Brand::find()->all();
        $a = arrayHelper::map($brang,'id','name');
        //商品分类
        $goodscategory = GoodsCategory::find()->all();
        $b = arrayHelper::map($goodscategory,'id','name');
        return $this->render('add',['model'=>$model,'mode'=>$mode,'a'=>$a]);
    }
    //删除
    public function actionDelete($id){
        $model = Goods::deleteAll(['id'=>$id]);
        //跳转页面
        return $this->redirect(['goods/index']);

    }
    //查询相册
    public function actionShopp($id){
        $model = GoodsGallery::find()->where(['goods_id'=>$id])->all();
//        var_dump($model);die;
        return $this->render('tupian',['model'=>$model,'goods_id'=>$id]);
    }
   //添加相册
    public function actionPicture($id){
        $model = new GoodsGallery();
        $img = UploadedFile::getInstanceByName('file');
        $fileName = '/upload/goods1/'.uniqid().'.'.$img->extension;

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
                //上传成功http://p1eugxxb4.bkt.clouddn.com//upload1/5a3e857b35287.jpg
//                $url = "http://p1eugxxb4.bkt.clouddn.com/".$fileName;
                $url = "http://{$domain}/{$key}";
                $model->path = $url;
                $model->goods_id = $id;
                $model->save();
                return Json::encode(['url'=>$url]);
            }
        }
    }
//富文本编译器
    public function actions()
    {
        return [
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
                'config'=>[
                    'imageUrlPrefix' => "http://admin.yii2.com", /* 图片访问路径前缀 */
                    'imagePathFormat' => "/image/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以 自定义保存路径和文件名格式 */
                ]
            ]

        ];
    }
    //删除
    public function actionDel($id){
        $model = GoodsGallery::deleteAll(['id'=>$id]);
    }

}