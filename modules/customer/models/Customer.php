<?php

namespace customer\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;
use cando\db\ActiveRecord;
use checkout\models\Cart;
use checkout\models\Quote;
use wishlist\models\Wishlist;

/**
 * customer 模型
 *
 * @author  zhangyang <zhangyangcado@qq.com>
 */
class Customer extends ActiveRecord implements IdentityInterface
{

    const STATUS_ENABLED  = 1;
    const STATUS_DISABLED = 0;

    const GENDER_MALE   = 0;
    const GENDER_FEMALE = 1;

    protected $_loginType;
    protected $_cart;
    protected $_wishlist;
    protected $_quote;
    


    /**
     * is_active 字段 hash 选项
     * 
     * @return array
     */
    public static function isActiveHashOptions()
    {
        return [
            static::STATUS_DISABLED => Yii::t('app', 'Disabled'),
            static::STATUS_ENABLED  => Yii::t('app', 'Enabled'),
        ];
    }




    /**
     * 性别 hash 选项
     * 
     * @return array
     */
    public static function genderHashOptions()
    {
        return [
            static::GENDER_MALE   => Yii::t('app', 'Male'),
            static::GENDER_FEMALE => Yii::t('app', 'Female'),
        ];
    }






    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%customer}}';
    }
    




    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'timestamp' => [
                'class' => TimestampBehavior::class,
            ],
        ]);
    }




    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nickname'], 'string', 'max' => 32],
            [['avatar', 'auth_key'], 'string', 'max' => 255],
            [['dob'], 'date'],
            [['qq'], 'string', 'max' => 16],
            [['default_address'], 'integer'],
            [['gender'], 'in', 'range' => array_keys(static::genderHashOptions())],
            [['is_active'], 'in', 'range' => array_keys(static::isActiveHashOptions())],
            [['is_active'], 'default', 'value' => static::STATUS_ENABLED],
            [['group_id'], 'integer'],
            [['group_id'], 'exist', 'targetClass' => CustomerGroup::class, 'targetAttribute' => 'id'],
            [['gender', 'qq', 'dob'], 'default', 'value' => null],
            [['default_address'], 'default', 'value' => null],
        ];   
    }




    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'              => Yii::t('app', 'Id'),
            'nickname'        => Yii::t('app', 'Nickname'),
            'phone'           => Yii::t('app', 'Mobile number'),
            'email'           => Yii::t('app', 'Email address'),
            'avatar'          => Yii::t('app', 'Avatar'),
            'created_at'      => Yii::t('app', 'Join time'),
            'updated_at'      => Yii::t('app', 'Updated at'),
            'auth_key'        => Yii::t('app', 'Auth key'),
            'dob'             => Yii::t('app', 'Date of birth'),
            'gender'          => Yii::t('app', 'Gender'),
            'qq'              => Yii::t('app', 'Qq'),
            'login_at'        => Yii::t('app', 'Login at'),
            'is_active'       => Yii::t('app', 'Is active'),
            'group_id'        => Yii::t('app', 'Customer group'),
            'default_address' => Yii::t('app', 'Default address'),
        ];
    }



    /**
     * 设置登录类型
     * 
     * @param CustomerAccount $type 登录类型
     */
    public function setLoginType( CustomerTypeInterface $type )
    {
        $this->_loginType = $type;
    }


    /**
     * 获取登录类型.
     * 
     * @return CustomerTypeInterface
     */
    public function getLoginType()
    {
        return $this->_loginType;
    }


    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
         return static::findOne([
             'id'        => $id,
             'is_active' => static::STATUS_ENABLED,
         ]);
    }



    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $helper = new AccessToken();
        $id = $helper->getIdByAccessToken($token);
        return $id ? static::findIdentity($id) : null;
    }




    /**
     * 生成/重新生成 access token
     * 
     * @return string
     */
    public function generateAccessToken()
    {
        $helper = new AccessToken();
        return $helper->generateAccessToken($this->id);
    }



    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if($insert) {
            $this->generateAuthKey();
            if(!$this->group_id) {
                 $this->resetDefaultGroup(false);   
            }
        }
        return parent::beforeSave($insert);
    }





    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }




    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
         return $this->auth_key === $authKey;
    }



    /**
     * 生成 auth_key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->helper->get('hash')->generateAuthKey(); 
    }



    /**
     * 重置默认组
     * 
     * @param  boolean $save 是否保存
     * @return booelan
     */
    public function resetDefaultGroup($save = true)
    {
        $default = Yii::$app->config->get('customer/group/default', CustomerGroup::DEFAULT_GROUP_ID);
        $this->group_id = $default;
        return $save ? $this->save() : true;
    }



    /**
     * 获取 phone 类型
     * 
     * @return yii\db\ActiveQuery
     */
    public function getTypePhone()
    {
        return $this->hasOne(types\Phone::class, ['customer_id' => 'id'])
            ->inverseOf('customer');
    }



    /**
     * 获取手机号。
     * 
     * @return string
     */
    public function getPhone()
    {
        if($this->typePhone) {
            return $this->typePhone->username;
        }
        return null;
    }


    /**
     * 获取 email 类型
     * 
     * @return yii\db\ActiveQuery
     */
    public function getTypeEmail()
    {
        return $this->hasOne(types\Email::class, ['customer_id' => 'id'])
            ->inverseOf('customer');
    }



    
    /**
     * 获取邮件地址
     * 
     * @return string
     */
    public function getEmail()
    {
        if($this->typeEmail) {
            return $this->typeEmail->username;
        }
        return null;
    }



    public function hasPasswordAccount()
    {
        return $this->typeEmail || $this->typePhone;
    }



    /**
     * 修改密码
     * 
     * @param  string $password 明文密码
     * @return boolean
     */
    public function changePassword($password)
    {
        $accounts = ['typePhone', 'typeEmail'];
        foreach($accounts as $relation) {
            $account = $this->$relation;
            if($account instanceof CustomerAccount) {
                $account->setPassword($password);
                $account->save();
            }
        }
        return true;
    }




    /**
     * 获取 cart 实例.
     * 
     * @return cart
     */
    public function getCart()
    {
        if(!$this->_cart) {
            $cart = Cart::findByCustomer($this);
            if(!$cart) {
                $cart = new Cart(['customer' => $this]);
                $cart->save();
            }
            $this->_cart = $cart;          
        }
        return $this->_cart;
    }



    /**
     * 获取 wishlist
     * 
     * @return wishlist
     */
    public function getWishlist()
    {
        if(!$this->_wishlist) {
            $wishlist = Wishlist::findByCustomer($this);
            if(!$wishlist) {
                $wishlist = new Wishlist(['customer' => $this]);
                $wishlist->save();
            }
            $this->_wishlist = $wishlist;          
        }
        return $this->_wishlist;
    }



    /**
     * 获取 customer quote
     * 
     * @return checkout\models\Quote
     */
    public function getQuote()
    {
        if(!$this->_quote) {
            $quote = Quote::findByCustomer($this);
            if(!$quote) {
                $quote = new Quote(['customer' => $this]);
                $quote->save();
            }
            $this->_quote = $quote;
        }
        return $this->_quote;
    }



    /**
     * 获取地址
     * 
     * @return yii\db\ActiveQuery[]
     */
    public function getAddresses()
    {
        return $this->hasMany(CustomerAddress::class, ['customer_id' => 'id'])
            ->inverseof('customer');
    }



    
    /**
     * 获取默认地址.
     * 
     * @return CustomerAddress
     */
    public function getDefaultAddress()
    {
        $addresses = $this->getAddresses();
        foreach($addresses as $address) {
            if($address->isDefault()) {
                return $address;
            }
        }
        return reset($addresses);
    }


}