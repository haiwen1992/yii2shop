<?php
namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\Menu;
use yii\web\Controller;
use yii\web\Request;

class MenuController extends Controller
{
    //列表
    public function actionIndex()
    {
        $menu = Menu::find()->all();
        return $this->render('index', ['menu' => $menu]);
    }

    //添加
    public function actionAdd()
    {
        $request = new Request();
        $model = new Menu();
        if ($request->isPost) {
            //加载表单数据
            $model->load($request->post());
            if ($model->validate()) {
                //保存
                $model->save();
                //设置提示信息
                \Yii::$app->session->setFlash('success', '添加成功');
                //跳转页面
                return $this->redirect(['menu/index']);
            }
        } else {
            $menus = Menu::find()->all();
            $ar = [];
            array_unshift($ar, '一级菜单');
            foreach ($menus as $menu) {
                $ar[$menu->id] = $menu->name;
            }
            $authManager = \Yii::$app->authManager;
            $pe = $authManager->getPermissions();
            $route = [];
            array_unshift($route, '选择路由');
            foreach ($pe as $p) {
                $route[$p->name] = $p->name;
            }

        }
        //显示添加的表单
        return $this->render('add', ['model' => $model, 'ar'=>$ar, 'route'=>$route]);

    }

    //修改
    public function actionEdit($id)
    {
        $request = new Request();
        //根据id获取一条数据
        $model = Menu::findOne(['id' => $id]);
        if ($request->isPost) {
            //加载表单数据
            $model->load($request->post());
            //验证
            if ($model->validate()) {
                //保存数据
                $model->save();
                //设置提示信息
                \Yii::$app->session->setFlash('success', '修改成功');
                //跳转回index
                return $this->redirect(['menu/index']);

            } else {
                //失败打印错误信息
                var_dump($model->getErrors());
            }
        } else {
            $menus = Menu::find()->all();
            $ar = [];
            array_unshift($ar, '一级菜单');
            foreach ($menus as $menu) {
                $ar[$menu->id] = $menu->name;
            }
            $authManager = \Yii::$app->authManager;
            $pe = $authManager->getPermissions();
            $route = [];
            array_unshift($route, '选择路由');
            foreach ($pe as $p) {
                $route[$p->name] = $p->name;
            }

            //显示修改表单
            return $this->render('add', ['model' => $model,'ar'=>$ar, 'route'=>$route]);
        }
    }

    //删除
    public function actionDelete($id)
    {
        //根据id删除一条数据
        $model = Menu::deleteAll(['id' => $id]);
        //跳转
        return $this->redirect(['menu/index']);

    }
    public function behaviors()
    {
        return[
            'rbac'=>[
//                'class'=>RbacFilter::className(),
                'class'=>RbacFilter::className(),
                'except'=>['login','logout','upload','captcha'],
            ],

        ];
    }
}