
<?php
/**
 * Created by PhpStorm.
 * User: 18079
 * Date: 2017/12/31
 * Time: 12:51
 */

namespace frontend\controllers;


use backend\models\GoodsCategory;
use backend\models\GoodsGallery;
use frontend\models\Cart;
use frontend\models\Goods;
use frontend\models\GoodsIntro;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Cookie;

class GoodsController extends Controller
{
    public function actionIndex($id)
    {
        $goodscat = GoodsCategory::findOne(['id' => $id]);
        if ($goodscat->depth == 2) {
            $ids = [$id];
        } else {
            $goodsparent = $goodscat->children()->select('id')->andWhere(['depth' => 2])->asArray()->all();
            $ids = ArrayHelper::map($goodsparent, 'id', 'id');
        }
        $total = Goods::find()->where(['in', 'goods_category_id', $ids])->count();
        $pager = new Pagination([
            'totalCount' => $total,
            'defaultPageSize' => 10
        ]);
        $goods = Goods::find()->orderBy(['sort' => SORT_ASC])->limit($pager->limit)->offset($pager->offset)->where(['in', 'goods_category_id', $ids])->all();
        return $this->render('index', ['goods' => $goods, 'pager' => $pager]);
    }

    public function actionOne($id)
    {
        $goods = Goods::find()->where(['id' => $id])->one();
        $photos = GoodsIntro::find()->where(['goods_id' => $id])->one();
        $arr = GoodsGallery::find()->where(['goods_id' => $id])->all();
        foreach ($arr as $v) {
            $max = min([$v->id]);
        }
        $ma = GoodsGallery::find()->where(['id' => $max])->one();
        if ($goods->view_times == 0) {
            $i = 0;
        } else {
            $i = $goods->view_times;
        }
        ++$i;
        Goods::updateAll(['view_times' => $i], ['id' => $id]);//点击数
        return $this->render('one', ['photos' => $photos, 'arr' => $arr, 'goods' => $goods, 'ma' => $ma, 'i' => $i]);
    }


    //购买商品的处理
    public function actionBuy($goods_id, $amount)
    {
        if (\Yii::$app->user->isGuest) {
            $cookies = \Yii::$app->request->cookies;
            if ($cookies->has('cart')) {//在cookie中可以保存多个商品
                $cart = $cookies->getValue('cart');
                $arr = unserialize($cart);
            } else {
                $arr = [];
            }
            //判断cookie中是否有这个商品
            if (array_key_exists($goods_id, $arr)) {//如果存在就在cookie中的商品加上商品数量
                $arr[$goods_id] += $amount;
            } else {
                $arr[$goods_id] = $amount;
            }
            //用户没有登录,将数据保存在cookie中
            $cookies = \Yii::$app->response->cookies;//将商品写到cookie中
            $cookie = new Cookie();
            $cookie->name = 'cart';
            $cookie->value = serialize($arr);
            $cookies->add($cookie);
        } else {
            //用户已经登录了,将数据添加到数据库
            $goods = Cart::findOne(['goods_id' => $goods_id, 'member_id' => \Yii::$app->user->id]);
            if ($goods) {
                $num = $goods->amount + $amount;
                Cart::updateAll(['amount' => $num], ['goods_id' => $goods_id, 'member_id' => \Yii::$app->user->id]);
            } else {
                $cart = new Cart();
                $cart->goods_id = $goods_id;
                $cart->amount = $amount;
                $cart->member_id = \Yii::$app->user->id;
                $cart->save();
            }

        }
        return $this->redirect(['goods/cart']);
    }

    //购物车的处理
    public function actionCart()
    {

        //判断用户是不是登录
        if (\Yii::$app->user->isGuest) {
            $cookies = \Yii::$app->request->cookies;//读取cookie中的商品
            $cart = $cookies->getValue('cart');//得到保存在cookie中的商品
            if ($cookies->has('cart')) {//在cookie中可以保存多个商品
                $cart = $cookies->getValue('cart');
                $arr = unserialize($cart);//反序列化
                $cart = unserialize($cart);
            } else {
                $arr = [];
            }
            $ids = array_keys($arr);
        } else {
            //登录了就取用户的id
            $id = \Yii::$app->user->id;
            $is = Cart::find()->where(['member_id' => $id])->all();
            $cart = ArrayHelper::map($is, 'goods_id', 'amount');//商品数量
            $ids = ArrayHelper::map($is, 'goods_id', 'goods_id');//购物车表商品的id
        }
        $models = Goods::find()->where(['in', 'id', $ids])->all();
//        var_dump($arr);die;
        return $this->render('cart', ['models' => $models, 'cart' => $cart]);
    }

    //删除购物车的商品.
    public function actionOut($id)
    {
        if (\Yii::$app->user->isGuest) {//判断用户是否登录
            $cookies = \Yii::$app->request->cookies;
            $cart = $cookies->getValue("cart");
            $arr = unserialize($cart);
            unset($arr[$id]);//删除数组中的指定的元素并且不会改变数组
            $cookies = \Yii::$app->response->cookies;
            $cookie = new Cookie();
            $cookie->name = 'cart';
            $cookie->value = serialize($arr);
            $cookies->add($cookie);
        } else {
            Cart::deleteAll(['goods_id' => $id, 'member_id' => \Yii::$app->user->id]);
        }
    }

    //处理购物车中商品数量
    public function actionAlter($id, $amount)
    {
        //判断用户是否登录
        if (\Yii::$app->user->isGuest) {
            $cookies = \Yii::$app->request->cookies;
            $cart = $cookies->getValue('cart');
            $arr = unserialize($cart);
//            unset($arr[$id]);
            $arr[$id] = $amount;
            $cookies = \Yii::$app->response->cookies;
            $cookie = new Cookie();
            $cookie->name = 'cart';
            $cookie->value = serialize($arr);
            $cookies->add($cookie);
        } else {
            Cart::updateAll(['amount' => $amount], ['goods_id' => $id, 'member_id' => \Yii::$app->user->id]);
        }
    }

//    public function behaviors()
//    {
//        return [
//            'rbac' =>[
//                'class'=>RbacFilters::className(),
//                'except'=>['login','logout','uploader'],
//            ]
//        ];
//    }


//登录
    public function actionLogin()
    {
        $model = new Login();
        $request = new Request();
        if ($request->isPost) {
//           var_dump($request->post());die;
            $model->load($request->post(), '');
            if ($model->login()) {
                $id = \Yii::$app->user->id;
                Member::updateAll(['last_login_time' => time(), 'last_login_ip' => $_SERVER['REMOTE_ADDR']], ['id' => $id]);

//将cookie中的商品同步到数据库中
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
                $this->jump(4, '/site/index.html', '登录成功');
            } else {
                var_dump($model->getErrors());
                die;
            }
        }
        return $this->render('login');
    }

    }
