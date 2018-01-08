<?php

namespace backend\controllers;

use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsDayCount;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;
use yii\web\UploadedFile;
// 引入鉴权类
use Qiniu\Auth;
// 引入上传类
use Qiniu\Storage\UploadManager;

class GoodsController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionIndex()
    {
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
        $total = $query->andwhere(['status' => 1])->count();
        $pager = new Pagination([
            'totalCount' => $total,
            'defaultPageSize' => 4
        ]);
        $goods = $query->limit($pager->limit)->offset($pager->offset)->andwhere(['status' => 1])->all();
//        var_dump($goods);die;
        return $this->render('index', ['goods' => $goods, 'pager' => $pager]);
    }

    public function actionAdd()
    {
        $goodsday = GoodsDayCount::findOne(['day' => date('Ymd', time())]);
        $model = new Goods();
        $gooin = new GoodsIntro();
        $request = new Request();
        if ($request->isPost) {
            $model->load($request->post());
            $gooin->load($request->post());
            if ($model->validate()) {
                $model->create_time = strtotime($model->create_time);
                $model->save();
                $id = \Yii::$app->db->getLastInsertID();
                $gooin->goods_id = $id;
                $gooin->save();
                if ($goodsday) {
                    $goodsday->count += 1;
                } else {
                    $goodsday = new GoodsDayCount();
                    $goodsday->day = date('Ymd', time());
                    $goodsday->count = 1;
                }
                $goodsday->save();
                \Yii::$app->session->setFlash('success', '添加文章成功');
                return $this->redirect(['goods/index']);
            }
        } else {
            $brand = Brand::find()->all();
            $arr = ArrayHelper::map($brand, 'id', 'name');
            $model->is_on_sale = 1;
            $model->status = 1;
            $model->sn += 1;
            $model->sn = str_pad($model->sn, 5, 0, 0);
            if ($goodsday) {
                $model->sn = date('Ymd') . $model->sn + $goodsday->count;
            } else {
                //$model->sn=str_pad($model->sn,6,0,0);
                $model->sn = date('Ymd') . $model->sn;
            }

            return $this->render('add', ['model' => $model, 'arr' => $arr, 'gooin' => $gooin]);
        }
    }

    public function actionUpload()
    {
        $img = UploadedFile::getInstanceByName('file');
        $dirfile = 'uploads/' . \Yii::$app->controller->id . '/' . date('ymd') . '/';
        if (!is_dir($dirfile)) {
            mkdir($dirfile, 07770, true);
        }
        $file = '/' . $dirfile . uniqid() . '.' . $img->getExtension();
        if ($img->saveAs(\Yii::getAlias('@webroot') . $file)) {
            //============上传到七牛云上========

// 需要填写你的 Access Key 和 Secret Key
            $accessKey = "kBZLU93-piQoJo9bgv38ProxOPDm5RS99s9CsaCd";
            $secretKey = "B0sz2q1Wn-1RAfqb5UD1r0n-qx1aE_80SdCTCS5v";
            $bucket = "yii2shop";
            // 构建鉴权对象
            $auth = new Auth($accessKey, $secretKey);
            // 生成上传 Token
            $token = $auth->uploadToken($bucket);
            // 要上传文件的本地路径
            $filePath = \Yii::getAlias('@webroot') . $file;
            // 上传到七牛后保存的文件名
            $key = $file;
            // 初始化 UploadManager 对象并进行文件的上传。
            $uploadMgr = new UploadManager();
            // 调用 UploadManager 的 putFile 方法进行文件的上传。
            list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
            if ($err !== null) {
//                var_dump($err);
            } else {
                //http://p1aumk2lq.bkt.clouddn.com//uploads/goods/201712235a3d3adc92264.jpg
                $url = 'http://p1aumk2lq.bkt.clouddn.com/' . $file;
                return Json::encode(['url' => $url]);
            }
            //==================================
        }
    }

    public function actionEdit($id)
    {
        $model = Goods::find()->where(['id' => $id])->one();
        $gooin = GoodsIntro::find()->where(['goods_id' => $id])->one();
        $request = new Request();
        if ($request->isPost) {
            $model->load($request->post());
            $gooin->load($request->post());
//            var_dump($model);die;
            if ($model->validate()) {
                $model->create_time = strtotime($model->create_time);
                $model->save();
                $id = \Yii::$app->db->getLastInsertID();
                $gooin->goods_id = $id;
                $gooin->save();
                $goodsday = GoodsDayCount::find()->where(['day' => date('Ymd', time())])->one();
                $goo = new GoodsDayCount();
                if ($goodsday) {
                    GoodsDayCount::updateAll(['count' => $goodsday->count + 1], ['day' => $goodsday->day]);
                } else {
                    $goo->day = date('Ymd', time());
                    $goo->count = 1;
                    $goo->save();
                }
                \Yii::$app->session->setFlash('success', '修改文章成功');
                return $this->redirect(['goods/index']);
            }
        } else {
            $brand = Brand::find()->all();
            $arr = ArrayHelper::map($brand, 'id', 'name');
            $model->create_time = date('Ymd', $model->create_time);
            var_dump($model->create_time);
            die;
            return $this->render('add', ['model' => $model, 'arr' => $arr, 'gooin' => $gooin]);
        }
    }

    public function actionDel($id)
    {
        Goods::updateAll(['status' => 0], ['id' => $id]);
    }

    //回收站
    public function actionRecover()
    {
        $total = Goods::find()->where(['status' => 0])->count();
        $pager = new Pagination([
            'totalCount' => $total,
            'defaultPageSize' => 4
        ]);
        $goods = Goods::find()->where(['status' => 0])->limit($pager->limit)->offset($pager->offset)->all();
        return $this->render('recover', ['goods' => $goods, 'pager' => $pager]);
    }

    public function actionStrike($id)
    {
        Goods::deleteAll(['id' => $id]);
    }


    public function actionAlbum($id)
    {
        $albums = GoodsGallery::find()->where(['goods_id' => $id])->all();
        return $this->render('album', ['albums' => $albums, 'goods_id' => $id]);
    }

    public function actionDrawing($id)
    {
        $gsg = new GoodsGallery();
        $img = UploadedFile::getInstanceByName('file');
        $dirfile = 'uploads/' . \Yii::$app->controller->id . '/' . date('Ymd') . '/logo/';
        if (!is_dir($dirfile)) {
            mkdir($dirfile, 0777, true);
        }
        $file = '/' . $dirfile . uniqid() . '.' . $img->getExtension();
        if ($img->saveAs(\Yii::getAlias('@webroot') . $file)) {
            // 需要填写你的 Access Key 和 Secret Key
            $accessKey = "kBZLU93-piQoJo9bgv38ProxOPDm5RS99s9CsaCd";
            $secretKey = "B0sz2q1Wn-1RAfqb5UD1r0n-qx1aE_80SdCTCS5v";
            $bucket = "yii2shop";
            // 构建鉴权对象
            $auth = new Auth($accessKey, $secretKey);
            // 生成上传 Token
            $token = $auth->uploadToken($bucket);
            // 要上传文件的本地路径
            $filePath = \Yii::getAlias('@webroot') . $file;
            // 上传到七牛后保存的文件名
            $key = $file;
            // 初始化 UploadManager 对象并进行文件的上传。
            $uploadMgr = new UploadManager();
            // 调用 UploadManager 的 putFile 方法进行文件的上传。
            list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
            if ($err !== null) {
//                var_dump($err);
            } else {
                //http://p1aumk2lq.bkt.clouddn.com//uploads/goods/201712235a3d3adc92264.jpg
                $url = 'http://p1aumk2lq.bkt.clouddn.com/' . $file;
//                var_dump($id);die;
                $gsg->path = $url;
                $gsg->goods_id = $id;
                $gsg->save();
                return Json::encode(['url' => $url]);
            }
            //==================================
        }
    }
}



