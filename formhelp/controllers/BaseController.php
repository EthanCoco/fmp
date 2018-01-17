<?php
namespace app\controllers;
use yii\web\Controller;
use Yii;

/*
 * 公共继承方法
 * 设置公用属性等方法
 */

class BaseController extends Controller{
	/*设置post请求-----解除post请求限制*/
	public $enableCsrfValidation = false;
	
	/*判断session是否过期*/
	public function init(){
		//设置时间参考地区
		date_default_timezone_set('PRC');
		if(Yii::$app->user->isGuest){
			$this->redirect(['/site/login']);
			Yii::$app->end();
		}
		$this->getView()->title = "FORM DESIGN"; 
	}
	
	/*统一返回方法*/
	public function jsonReturn($data){
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $data;
	}
	
	/*
	 * 校验参数是否为空
	 * null 和 '' 都默认为空值
	 * 0 和 false 不为空
	 */
	public function valNullParams(){
		$assert = 1;
		$args = func_get_args();
		if(!count($args))
			return false;
		foreach($args as $k=>$v){
			if(is_null($v) || $v == ''){
				$assert = 0;
				break;
			}
		}
		
		if(!$assert)
			return false;
		
		return true;
	}
}
