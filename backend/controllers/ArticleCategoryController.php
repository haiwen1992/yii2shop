<?php
namespace backend\controllers;

use backend\models\ArticleCategory;
use yii\test\InitDbFixture;
use yii\web\Controller;
use yii\web\Request;

class ArticleCategoryController extends Controller{
     //列表查询
    public function actionIndex(){
        //调用模型处理数据
//      $article = ArticleCategory::find()->all();
        //小于零时时删除 所以status查出大于零的所有数据
        $article = ArticleCategory::find()->where(['>=','status','0'])->all();
        //调用视图分配数据
        return $this->render('index',['article'=>$article]);

    }
    //添加
    public function actionAdd(){
        $request = new Request();
        $model = new ArticleCategory();
        if($request->isPost){
            //加载表单数据
            $model->load($request->post());
            if($model->validate()){
                //保存
                $model->save();
                //设置提示信息
                \Yii::$app->session->setFlash('success', '添加成功');
                //跳转或主页面
                return $this->redirect(['article-category/index']);
            }else{
                //验证失败打印错误信息
                var_dump($model->getErrors());
            }
        }else{
            //显示表单
            return $this->render('add',['model'=>$model]);
        }
    }
    //修改
    public function actionEdit($id){
        $request = new Request();
        //根据id获取数据
        $model = ArticleCategory::findOne(['id'=>$id]);
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            if($model->validate()){
                //保存
                $model->save();
                //设置提示信息
                \Yii::$app->session->setFlash('success','修改成功');
                //跳转或主页面
                return $this->redirect(['article-category/index']);
            }else{
                //打印错误信息
                var_dump($model->getErrors());
            }

        }
        return $this->render('add',['model'=>$model]);
    }
//删除
public function actionDelete($id){
//    //根据id
//     $model = ArticleCategory::findOne(['id'=>$id]);
////     //get方式传过来
//////     $delete->load($request->get());
//    $model->status = -1;
//    $model->save();
    ArticleCategory::updateAll(['status'=>-1],['id'=>$id]);
}

}