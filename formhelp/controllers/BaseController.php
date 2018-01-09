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
	
}
