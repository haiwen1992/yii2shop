<?php
namespace  frontend\models;

use yii\db\ActiveRecord;

class Order extends  ActiveRecord{
    public static $deliveries = [
       1=>['顺丰快递',22,'速度快'],
       2=>['圆通快递',10,'速度一般,服务一般.价格一般'],
       3=>['韵达快递',10,'速度稍慢,服务一般,价格一般'],
    ];
    public static $pays = [
        1=>['货到付款','送货上门后再收款，支持现金、POS机刷卡、支票支付'],
        2=>['在线支付','即时到帐，支持银行借记卡信用卡微信支付和支付宝支付'],
        3=>['上门自提','支持刷卡,扫描支付'],
    ];
    public function rules()
    {
        return[
            [['member_id','name', 'province','city','area','address','tel','address_id'], 'required'],
           [['delivery_id','delivery_name','delivery_price','payment_id','payment_name','total','create_time'],'safe']
        ];
    }
}