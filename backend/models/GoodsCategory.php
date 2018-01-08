<?php
namespace backend\models;
use creocoder\nestedsets\NestedSetsBehavior;

use yii\db\ActiveRecord;
use yii\helpers\Json;
use yii\helpers\Url;

class GoodsCategory extends ActiveRecord{
    //设置验证规则
    public function rules(){
        return[
          [['name','parent_id','intro'],'required'],//不能为空
            ['parent_id','validatePid'],
        ];
    }
    //自定义验证规则
    public function validatePid(){
        $parent = GoodsCategory::findOne(['id'=>$this->parent_id]);
        if(!is_object($parent)){
            return false;
        }else{
            if($parent->isChildOf($this)){
                $this->addError('parent_id','不能修改到自己的子分类下');
            }
        }

    }
    public function attributeLabels(){
        return[
            'name'=>'名称',
            'parent_id'=>'所属分类',
            'intro'=>'简介',
        ];
    }
    public function behaviors() {
        return [
            'tree' => [
                'class' => NestedSetsBehavior::className(),
                 'treeAttribute' => 'tree',
                // 'leftAttribute' => 'lft',
                // 'rightAttribute' => 'rgt',
                // 'depthAttribute' => 'depth',
            ],
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find()
    {
        return new CategoryQuery(get_called_class());
    }
    //获取分类数据框 作为里面节点数据
    public static  function getNodes(){
   $nodes  =  self::find()->select(['id','parent_id','name'])->asArray()->all();
   array_unshift($nodes,['id'=>0,'parent_id'=>0,'name'=>'【添加分类】']);
   return Json::encode($nodes);
    }

    //前台展示页面
    public static  function getCategories(){
    $html = '';
   $categories1 = \backend\models\GoodsCategory::find()->where(['parent_id'=>0])->all();
                foreach ($categories1 as $k1=>$category1){
                 $html .= '<div class="cat '. ($k1?'':'iteml').'">';
                 $html .= '<h3><a href=""> '.$category1->name.'</a><b></b></h3>';
                 $html .=  '<div class="cat_detail">';
                 $categorys2= \backend\models\GoodsCategory::find()->where(['parent_id'=>$category1->id])->all();
                       foreach ($categorys2 as $k2=>$category2){
                 $html .= '<dl '.($k2?'':'class="dl_lst"').'>';
                 $html .=  '<dt><a href="'.Url::to(['list/list','id'=>$category2->id]).'">'.$category2->name.'</a></dt>';
                 $html .=  '<dd>';
                 $categorys3 =\backend\models\GoodsCategory::find()->where(['parent_id'=>$category2->id])->all();
                   foreach ($categorys3 as $category3){
                  $html .=  '<a href="'.Url::to(['list/list','id'=>$category3->id]).'">'.$category3->name.'</a>';
 }
                           $html .=  '</dd>';
                           $html .= '</dl>';
                            }
                    $html .= '</div>';
                    $html .= '</div>';
}
                   return $html;
    }
}
