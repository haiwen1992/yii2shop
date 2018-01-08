<?php
namespace  backend\controllers;

use backend\filters\RbacFilter;
use backend\models\Article;
use backend\models\ArticleDetail;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Request;

class ArticleController extends  Controller{
    //列表页面
    public function actionIndex(){
        //调用模型处理数据
        //小于零时时删除 所以status查出大于零的所有数据
        $article = Article::find()->where(['>=','status','0'])->all();
        //调用视图分配数据
        return $this->render('index',['article'=>$article]);
    }
    //文章详情列表
    public function actionShow($id){
        //文章详情展示
        $model = ArticleDetail::find()->where(['article_id'=>$id])->one();
//        var_dump($model);die;
        return $this->render('show',['model'=>$model]);
    }
    //添加
    public function actionAdd(){
        $request = new Request();
        //调用文章管理模型
        $model = new Article();
        //调用文章详情模型
        $mode = new ArticleDetail();

        if($request->isPost){
            //加载表单数据
            $model->load($request->post());
            $mode->load($request->post());
            //创建时间默认当前时间
            $model['create_time'] = time();
            if($model->validate()){

                //保存
                $model->save(false);
                $mode->article_id=$model->id;
                $mode->save(false);
                //设置提示信息
                \Yii::$app->session->setFlash('success','添加成功');
                //跳转页面
                return $this->redirect(['article/index']);
            }else{
                //打印错误信息
                var_dump($model->getErrors());
            }
        }else{
            return $this->render('add',['model'=>$model,'mode'=>$mode]);
        }
    }
    //修改
    public function actionEdit($id){
        $request = new Request();
        //根据id获取数据
        $model = Article::findOne($id);
        $mode = ArticleDetail::findOne($id);
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            $mode->load($request->post());
            if($model->validate()){
                //保存
                $model->save(false);
                $mode->save(false);
                //设置提示信息
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['article/index']);
            }else{
                //打印错误信息
                var_dump($model->getErrors());
            }
        }
        //加在修改表单
        return $this->render('add',['model'=>$model,'mode'=>$mode]);
    }
    //删除
    public function actionDelete($id){
        //根据id
        $model = Article::findOne(['id'=>$id]);
        $model->status = -1;
        $model->save();
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