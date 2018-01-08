<?php

namespace backend\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class User extends ActiveRecord implements IdentityInterface
{
//    public $role;
    public $password_h;
    public $password_ha;
    public $role;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {

        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {

        return [
            [['username', 'password_hash','email'], 'required'],
            [['password_h','password_ha','password_hash','role'],'safe'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
//            [['email'], 'unique'],
            array('email','email','message'=>'请输入正确的邮箱.'),//邮箱验证规则 百度
            [['password_reset_token'], 'unique'],
            ['password_ha', 'compare', 'compareAttribute' => 'password_h']//验证规则 验证新密码和确认新密码一致
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'auth_key' => 'Auth Key',
            'password_hash' => '密码',
            'password_reset_token' => 'Password Reset Token',
            'email' => '邮箱',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'last_login_time'=>'最后登录时间',
            'last_login_ip'=>'最后登录ip',
            'password_h'=>'新密码',
            'password_ha'=>'确认新密码',
            'role'=>'用户权限'
        ];
    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return self::findOne(['id'=>$id]);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
        return static::findOne(['access_token' => $token]);
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        // TODO: Implement getAuthKey() method.
        return $this->auth_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        // TODO: Implement validateAuthKey() method.
        return $this->getAuthKey() === $authKey;
    }
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->auth_key = \Yii::$app->security->generateRandomString();
            }
            return true;
        }
        return false;
    }
    //获取用户菜单
    public function getMenus(){
        $menuItems = [];
        $menus = Menu::find()->where(['superior_menu'=>0])->all();
        foreach ($menus as $menu){

            $children = Menu::find()->where(['superior_menu'=>$menu->id])->all();
            $items = [];
            foreach ($children as $child){

                //判断用户是否有该权限
                if(\Yii::$app->user->can($child->route))
                    $items[] = ['label'=>$child->name,'url'=>[$child->route]];

            }
            //没有子菜单的一级菜单不要显示
            if($items){
                $menusItems[] = ['label'=>$menu->name,'items'=>$items];

            }
            return $menuItems;
        }
//        $menusItems[] = ['label' => '首页', 'url' => ['/user/login']];
//     $menusItems[] = ['label' => '品牌管理', 'items' => [
//         ['label' => '品牌列表', 'url' => ['/brand/index']],
//         ['label' => '添加品牌', 'url' => ['/brand/add']],
//     ]];
//     $menusItems[] = ['label' => '文章管理', 'items' => [
//         ['label' => '文章列表', 'url' => ['/article/index']],
//         ['label' => '添加文章', 'url' => ['/article/add']],
//         ['label' => '文章分类列表', 'url' => ['/article-category/index']],
//         ['label' => '添加文章分类', 'url' => ['/article-category/add']],
//     ]];
//     $menusItems[] =   ['label' => '商品管理', 'items' => [
//         ['label' => '商品列表', 'url' => ['/goods/index']],
//         ['label' => '添加商品', 'url' => ['/goods/add']],
//         ['label' => '商品分类列表', 'url' => ['/goods-category/index']],
//         ['label' => '添加商品分类', 'url' => ['/goods-category/add']],
//     ]];
//     $menusItems[] = ['label' => '用户管理', 'items' => [
//         ['label' => '用户列表', 'url' => ['/user/index']],
//         ['label' => '添加用户', 'url' => ['/user/add']],
//     ]];
//     $menusItems[] = ['label' => 'RBAC', 'items' => [
//         ['label' => '权限列表', 'url' => ['/auth-item/index']],
//         ['label' => '添加权限', 'url' => ['/auth-item/addit']],
//         ['label' => '角色列表', 'url' => ['/auth-item/indexr']],
//         ['label' => '添加角色', 'url' => ['/auth-item/addr']],
//     ]];
//     $menusItems[] = ['label'=>'修改密码','url'=>['/user/edi']];


    }
}
