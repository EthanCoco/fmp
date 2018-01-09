<?php

namespace app\models;
use Yii;
use yii\base\Model;

class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
	public $name;
    private $_user = false;
	
	const SCENARIO_LOGIN = 'login';
	const SCENARIO_REGISTER = 'register';
	public function scenarios(){
		$scenarios = parent::scenarios();
	    $scenarios[self::SCENARIO_LOGIN] = ['name', 'password'];
	    $scenarios[self::SCENARIO_REGISTER] = ['name', 'password','realName','phone'];
	    return $scenarios;
	}
    
	public function rules(){
		return [
			['name', 'required','message'=>'账号不能为空', 'on' => [self::SCENARIO_LOGIN,self::SCENARIO_REGISTER]],
//			['name', 'validateIdCard', 'on' => [self::SCENARIO_REGISTER]],
			['realName', 'required','message'=>'姓名不能为空', 'on' => [self::SCENARIO_REGISTER]],
//			['phone', 'validatePhone', 'on' => [self::SCENARIO_REGISTER]],
			['password', 'required','message'=>'密码不能为空', 'on' => [self::SCENARIO_LOGIN,self::SCENARIO_REGISTER]],
			['password', 'validatePwd', 'on' => [self::SCENARIO_REGISTER]],
			['name', 'validateUniqueUserId', 'on' => [self::SCENARIO_REGISTER]],
		];
	}
	
	public function validateUniqueUserId($attribute, $params){
		if($this->name != ""){
			$num = self::find()->where(['name'=>$this->name])->count();
			if($num > 0){
				$this->addError($attribute, "账号已经被注册过了");
			}
		}
	}
	
	public function validatePhone($attribute, $params){
		if($this->phone == ""){
			$this->addError($attribute, "手机号码不能为空");
		}else{
			if (!is_numeric($this->phone)) {
		        $this->addError($attribute, "手机号码格式不正确");
		    }else if(!preg_match('/^0?1[0-9][0-9]\d{8}$/',$this->phone)){
		    	$this->addError($attribute, "手机号码格式不正确");
		    }
		}
	}
	
	public function validatePwd($attribute, $params){
		if($this->password == md5("")){
			$this->addError($attribute, "密码不能为空");
		}
	}
	
	public function validateIdCard($attribute, $params){
		if($this->name != ""){
			if($this->isCreditNo($this->name) == false){
				$this->addError($attribute, "身份证号码格式不正确");
			}
		}
	}
	
	public function isCreditNo($vStr){
	  	$vCity = [
		    '11','12','13','14','15','21','22',
		    '23','31','32','33','34','35','36',
		    '37','41','42','43','44','45','46',
		    '50','51','52','53','54','61','62',
		    '63','64','65','71','81','82','91'
	  	];
	  	if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $vStr)) return false;
	  	if (!in_array(substr($vStr, 0, 2), $vCity)) return false;
	  	$vStr = preg_replace('/[xX]$/i', 'a', $vStr);
	  	$vLength = strlen($vStr);
	  	if ($vLength == 18) {
	    	$vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-' . substr($vStr, 12, 2);
	  	} else {
	    	$vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2) . '-' . substr($vStr, 10, 2);
	  	}
	  	if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday) return false;
	  	if ($vLength == 18) {
	    	$vSum = 0;
		    for ($i = 17 ; $i >= 0 ; $i--) {
		      	$vSubStr = substr($vStr, 17 - $i, 1);
		      	$vSum += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr , 11));
		    }
	    	if($vSum % 11 != 1) return false;
	  	}
	  	return true;
	}
	
	public static function findSingleByWhere($where = []){
		$info = self::find()->where($where)->asArray()->one();
		if(count($info) > 1){
			
		}
		return $info;
	}
	
	public static function afterLoginDo(){
		date_default_timezone_set('PRC');
		$uid = Yii::$app->user->identity->uid;
		$name = Yii::$app->user->identity->name;
		$realName = Yii::$app->user->identity->realName;
		
		$session = Yii::$app->session;
		$session->set('uid',$uid);
		$session->set('realName',$realName);
		
		$user = self::findOne($uid);
		$user->userLoginCount = ($user->userLoginCount)+1;
		$user->userLastLoginTime = date("Y-m-d H:i:s");
		if($user->save()){
			return true;
		}
		return false;
	}
	
	
	public static function findByUsername($name){
    	$user = self::find()->where(['name' => $name])->one();
        if ($user){
            return new static($user);
        }
        return null;
    }
	
	public function login(){
        return Yii::$app->user->login($this->getUser());
    }
	
	public function getUser(){
        if ($this->_user === false) {
            $this->_user = self::findByUsername($this->name);
        }
        return $this->_user;
    }
	
	/**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->uid;
    }
    
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
    	$user = self::findById($id);
        if ($user) {
            return new static($user);
        }
        return null;
    }
	
	public static function findById($id) {
        $user = self::find()->where(array('uid' => $id))->asArray()->one();
        if ($user) {
            return new static($user);
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }
	
	/**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }
}
