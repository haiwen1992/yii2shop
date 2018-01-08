<?php
namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\ArticleCategory;
use backend\models\GoodsCategory;
use Symfony\Component\DomCrawler\Tests\Field\InputFormFieldTest;
use yii\web\Controller;
use yii\web\Request;

class GoodsCategoryController extends Controller
{
    //列表
    public function actionIndex()
    {
        //调用模型
        $model = GoodsCategory::find()->all();

        return $this->render('index', ['model' => $model]);
    }

    //添加
    public function actionAdd()
    {
        $request = new Request();
        $model = new GoodsCategory();
        if ($request->isPost) {
            //加载表单数据
            $model->load($request->post());
            if ($model->validate()) {
                if ($model->parent_id) {
                    //创建子节点 得到父节点id
                    $pid = GoodsCategory::findOne(['id' => $model->parent_id]);
                    //在父节点下创建
                    $model->appendTo($pid);
                } else {
                    //创建根节点
                    $model->makeRoot();
                }
//               //保存
//               $model->save();
                //设置提示信息
                \Yii::$app->session->setFlash('success', '添加成功');
                //跳转
                return $this->redirect(['goods-category/index']);
            } else {
                //验证失败打印错误信息
                var_dump($model->getErrors());
            }
        } else {
            //显示表单
            return $this->render('add', ['model' => $model]);
        }
    }
//    //测试插件
//    public  function actionCs(){
//        return $this->renderPartial('cs');
//    }
    //修改
    public function actionEdit($id)
    {
        //根据id获取数据
        $model = GoodsCategory::findOne(['id' => $id]);
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
                 if($model->parent_id){
              //创建子节点
                  $parent = GoodsCategory::findOne(['id'=>$model->parent_id]);
                  $model->appendTo($parent);
                 }else{
                     //创建根节点
                     if($model->getOldAttribute('parent_id')){
                         $model->makeRoot();
                     }else{
                         $model->save(false);
                     }
                 }
                 \Yii::$app->session->setFlash('success','修改成功');
                 return $this->redirect(['index']);
        }
        return $this->render('add',['model'=>$model]);
//            //加载数据
//            $model->load($request->post());
//            if ($model->validate()) {
//                if($model->parent_id){
//                    //创建子节点
//                    $parent = GoodsCategory::findOne(['id'=>$model->patent_id]);
//                    $model->appendTo($parent);
//                }else{
//                    //创建根节点
//                    if($model->getOldAttribute('parent_id')){
//                       $model->makeRoot();
//                    }
//                }
//                //保存
//                $model->save();
//                //设置提示信息
//                \Yii::$app->session->setFlash('success', '修改成功');
//                //跳转
//                return $this->redirect(['goods-category/index']);
//            } else {
//                //打印错误信息
//                var_dump($model->getErrors());
//            }
//
//        return $this->render('add', ['model' => $model]);
   }

    //删除
    public function actionDelete($id)
    {
        $model = GoodsCategory::deleteAll(['id' => $id]);
        //跳转
        return $this->redirect(['goods-category/index']);


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