<?php
namespace frontend\controllers;

use frontend\models\Address;
use yii\web\Controller;
use yii\web\RangeNotSatisfiableHttpException;
use yii\web\Request;

class AddressController extends Controller{
    //关闭验证
    public $enableCsrfValidation = false;

    public function actionIndex(){
        $model = Address::find()->all();
        return $this->render('index',['model'=>$model]);
    }
    public function actionAdd(){
       $request = new Request();
       $model = new Address();
       if($request->isPost){
           $model->load($request->post(),'');
           if($model->validate()){
               $model->save();
               \Yii::$app->session->setFlash('success'.'成功');
               return $this->redirect(['member/index']);
           }
           var_dump($model->getErrors());
       }
       return $this->render('add',['model'=>$model]);
    }
    //
    public function actionEdit($id){

        $request = new Request();
        $model = Address::findOne(['id'=>$id]);
//        var_dump($model);
//        die;
        if ($request->isPost){
            $model->load($request->post(),'false');

            if($model->validate()){
                $model->save('false');
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['address/index']);
            }
            var_dump($model->getErrors());
        }
        return $this->render('edit',['model'=>$model]);
    }
    public function actionDelete($id){
        $model = Address::deleteAll(['id'=>$id]);
    }
}