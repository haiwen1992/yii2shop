<?php
namespace backend\controllers;

use backend\models\Brand;
use yii\web\Controller;
use yii\web\Request;
use yii\web\UploadedFile;

class BrandController extends Controller
{
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
            //验证前处理图片
            $model->imgFile = UploadedFile::getInstance($model, 'imgFile');
            if ($model->validate()) {
                //处理图片
                $file = '/upload/' . uniqid() . '.' . $model->imgFile->getExtension();
                if ($model->imgFile->saveAs(\Yii::getAlias('@webroot') . $file)) {
                    //文件上传成功
                    $model->logo = $file;
                }
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
            //验证前处理图片
            $model->imgFile = UploadedFile::getInstance($model, 'imgFile');
            if ($model->validate()) {
                //处理图片
                $file = '/upload/' . uniqid() . '.' . $model->imgFile->extension;
                if ($model->imgFile->saveAs(\Yii::getAlias('@webroot') . $file)) {
                    //文件上传成功
                    $model->logo = $file;
                }
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
//        $model = Brand::deleteAll(['id'=>$id]);
//        $this->redirect(['brand/index']);
//        \Yii::$app->session->setFlash('success','删除成功');
        Brand::updateAll(['status'=>-1],['id'=>$id]);
    }
}