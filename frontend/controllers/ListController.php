<?php
namespace  frontend\controllers;

use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\Order;
use frontend\models\OrderGoods;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Cookie;

class ListController extends Controller
{
    //关闭验证
    public $enableCsrfValidation = false;
    //查询显示商品列表
    public function actionList($id)
    {
        $cate = GoodsCategory::findOne(['id'=>$id]);

        if($cate->depth==2){//三级分类
             //三级分类
            $ids = [$id];
        }else{
            //一级分类,二级分类
          $categories = $cate->children()->select('id')->andWhere(['depth'=>2])->asArray()->all();
            $ids = ArrayHelper::map($categories,'id','id');
        }
        $goods = Goods::find()->where(['in','goods_category_id',$ids])->all();
        return $this->render('list', ['goods'=>$goods]);
    }
    //商品相册详情
    public function actionShop($id){
     $content = GoodsIntro::find()->where(['goods_id'=>$id])->one();//内容
     $goods = Goods::findOne(['id'=>$id]);//商品
        //浏览次数
        Goods::updateAllCounters(['view_times'=>1],['id'=>$id]);
     $photh = GoodsGallery::find()->where(['goods_id'=>$id])->all();//相册
//     var_dump($photh);
//     die;
        return $this->render('goods',['content'=>$content,'goods'=>$goods,'photh'=>$photh]);
    }
    //添加购物车成功页面
    public function actionAddToCart($goods_id,$amount){
        //商品添加到购物车
        if(\Yii::$app->user->isGuest){
            //未登录保存到cookie
            //读取cookie中购物车信息
            $cookies = \Yii::$app->request->cookies;
            //判断cookie中有没有购物车信息
            if($cookies->has('cart')){
                $value = $cookies->getValue('cart');
                $cart = unserialize($value);
            }else{
                $cart = [];
            }
             //写cookie
            //判断购物车中是否存在该商品 存在 数量累加 不存在 直接赋值
            if(array_key_exists($goods_id, $cart)){
                $cart[$goods_id] += $amount;
            }else{
                $cart[$goods_id] = $amount;
            }
            $cookies = \Yii::$app->response->cookies;
            $cookie = new Cookie();
            $cookie->name = 'cart';
            $cookie->value = serialize($cart);
            $cookies->add($cookie);
        }else{
            //登录购物车数据保存到数据表
          $goods = Cart::findOne(['goods_id'=>$goods_id,'member_id'=>\Yii::$app->user->id]);
          if($goods){
              $num = $goods->amount + $amount;
              Cart::updateAll(['amount'=>$num],['goods_id'=>$goods_id,'member_id'=>\Yii::$app->user->id]);
          }else{
              $cart = new Cart();
              $cart->goods_id = $goods_id;
              $cart->amount = $amount;
              $cart->member_id = \Yii::$app->user->id;
              $cart->save();
          }
        }
        //跳转到购物车
        return $this->redirect(['list/cart']);
    }
    //购物车页面
    public function actionCart(){
        //判断用户是否登录,登录信息在数据表里面获取
        //未登录 信息从cookie里面获取
        if(\Yii::$app->user->isGuest){
            //读cookie
            $cookies = \Yii::$app->request->cookies;
            $value = $cookies->getValue('cart');
            $cart = unserialize($value);
            $ids = array_keys($cart);
        }else{
            //已登录
            //去取数据库里面的信息显示
            $id = \Yii::$app->user->id;
            $is = Cart::find()->where(['member_id'=>$id])->all();
            $cart = ArrayHelper::map($is,'goods_id','amount');
            $ids = ArrayHelper::map($is,'goods_id','goods_id');

        }
        $models = Goods::find()->where(['in','id',$ids])->all();
        return $this->renderPartial('cart',['models'=>$models,'cart'=>$cart]);
    }
    //删除购物车商品
    public function actionDelete($id){
         //判断是否用户登录 未登录删除cookie
        if(\Yii::$app->user->isGuest){
            $cookies = \Yii::$app->request->cookies;
            $cart = $cookies->getValue('cart');
            $a = unserialize($cart);
            unset($a[$id]);
            $cookies = \Yii::$app->response->cookies;
            $cookie = new Cookie();
            $cookie->name = 'cart';
            $cookie->value = serialize($a);
            $cookies->add($cookie);
        }
            Cart::deleteAll(['goods_id'=>$id,'member_id'=>\Yii::$app->user->id]);
        //登录删数据库
    }
    //修改数量
    public function actionChange(){
        //goods_id 数量amount
        $goods_id = \Yii::$app->request->post('goods_id');
        $amount = \Yii::$app->request->post('amount');
      if(\Yii::$app->user->isGuest){
         //未登录 修改cookie购物车数量
          $cookies = \Yii::$app->request->cookies;
          if($cookies->has('cart')){
              $value = $cookies->getValue('cart');
              $cart = unserialize($value);
          }else{
              $cart = [];
          }
            $cart[$goods_id]=$amount;
          $cookies = \Yii::$app->response->cookies;
          $cookie = new Cookie();
          $cookie->name = 'cart';
          $cookie->value = serialize($cart);
          $cookies->add($cookie);
      }else{
          Cart::updateAll(['amount' => $amount], ['goods_id' => $goods_id, 'member_id' => \Yii::$app->user->id]);
//          $goods_id = \Yii::$app->request->post('goods_id');
//          $amount = \Yii::$app->request->post('amount');
      }
    }
//    ////检查登录的情况
//        public function actionDaa(){
//        var_dump(\Yii::$app->user->identity);die;
//}
//订单
public function actionOrder(){
       //判断是否是登录用户 是保存数据到数据库未登录回登录页面
       if(\Yii::$app->user->isGuest){
             //未登录跳回登录页面
           return $this->redirect(['member/login']);
        }else{
           //登录保存数据到数据库
           $request = \Yii::$app->request;
           if($request->isPost){
               $order = new Order();
               $order->load($request->post(),'');
//               var_dump($request->post());
//               die;
               $address = Address::findOne(['id'=>$order->address_id]);
               $order->member_id = \Yii::$app->user->id;//用户的id
               $order->name = $address->name;//收货人姓名
               $order->province = $address->cmbprovince;//省
               $order->city = $address->cmbcity;//市
               $order->area	 = $address->cmbarea;//县
               $order->address = $address->detailed;//详细地址
               $order->tel = $address->phone;//电话
               //送货方式
               $order->delivery_name =  Order::$deliveries[$order->delivery_id[0]];
               $order->delivery_price = Order::$deliveries[$order->delivery_id[1]];
                //支付方法
               $order->payment_name = Order::$pays[$order->pay[0]];
               $order->total = 0;//订单金额
               $order->status = 1;//订单状态
               $order->create_time = time();//时间
                    //开启事务
               $transaction = \Yii::$app->db->beginTransaction();
               try{
                   if($order->validate()){
                       $order->save(false);
                   }
                   //订单商品信息
                   $carts = Cart::find()->where(['member_id'=>\Yii::$app->user->id])->all();//根据用户id查他对应的订单商品信息
                   foreach ($carts as $cart){
                       $goods = Goods::findOne(['id'=>$cart->goods_id]);
                       //判断库存
                       if($goods->stock >= $cart->amount){
                           //库存足够
                           $ordergoods = new OrderGoods();
                           $ordergoods->order_id = $order->id;
                           $ordergoods->goods_name = $goods->name;
                           $ordergoods->goods_logo = $goods->logo;
                           $ordergoods->price = $goods->shop_price;
                           $ordergoods->total = $ordergoods->price*$ordergoods->amount;
                           $ordergoods->save();
                           //扣减库存
                           $goods->stock -= $cart->amount;
                           $goods->save(false);
                           $order->total += $ordergoods->total;
                       }else{
                           //库存不够 抛出异常
                         throw new Exception('商品库存不够修改购物车');
                       }

                   }
                   //运费
                   $order->total += $order->delivery_price;
                   $order->save();
                   //清除购物车

                   //提交事务
                   $transaction->commit();
               }catch (Exception $e){
                  //回滚
                   $transaction->rollBack();
               };
               //跳转
               return $this->redirect(['success/sue']);
           }
           //$id = \Yii::$app->user->id;
           //$address = Address::findOne()->where(['member_id'=>$id])->all();
           $address = Address::find()->all();
           $id = \Yii::$app->user->id;
           $is = Cart::find()->where(['member_id'=>$id])->all();
           $cart = ArrayHelper::map($is,'goods_id','amount');
           $ids = ArrayHelper::map($is,'goods_id','goods_id');
           $models = Goods::find()->where(['in','id',$ids])->all();
           return $this->render('order',['address'=>$address,'models'=>$models,'cart'=>$cart]);
       }

}
//成功提交订单
public function actionSuccess(){
    return $this->render('sue');
}
//查看订单列表
public function actionOrderlist(){
//   $order = new Order();
   $order = Order::find()->where(['member_id'=>\Yii::$app->user->id])->all();
//    $ordergoods = OrderGoods::find()->where(['order_id'=>$order->id])->all();
//    foreach ($order as $o){
//        var_dump($o->id);
//        die;
//    };

    return $this->render('status',['order'=>$order]);
}
}















