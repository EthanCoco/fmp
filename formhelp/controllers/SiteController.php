<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\User;
use app\models\Share;

class SiteController extends BaseController
{
	//重写
	public function init(){
		date_default_timezone_set('PRC');
	}
	
	public function actions(){
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
	
	/*登录页面*/
    public function actionLogin(){
    	if(!Yii::$app->user->isGuest){
			$this->redirect(['/index/index']);
			Yii::$app->end();
		}
		$this->getView()->title = "用户登录"; 
		return $this->renderPartial('login',['secret'=>Yii::$app->params['login_secret']]);
	}
	
	/*登录*/
	public function actionLogindo(){
		$request = Yii::$app->request;
		if(!Yii::$app->user->isGuest){
			return $this->jsonReturn(['result'=>1]);
		}
		$model = new User();
		$model->setScenario(User::SCENARIO_LOGIN);
		if($model->load($request->post()) && $model->validate()){
		   	$name = $request->post()['User']['name'];
			$password = $request->post()['User']['password'];
		   	$info = User::findSingleByWhere(['name'=>$name,'password'=>$password]);
			if(empty($info)){
				$result = ['result'=>0,'msg'=>'账号或密码错误'];
			}elseif($model->login()){
				if(User::afterLoginDo()){
					$result = ['result'=>1];
				}else{
					$result = ['result'=>0,'msg'=>'服务器发生故障'];
				}
			}else{
				$result = ['result'=>0,'msg'=>'服务器发生故障'];
			}
		}else{
			$errors = $model->getFirstErrors();
			$result = ['result'=>0,'msg'=>Share::comErrors($errors)];
		}
		return $this->jsonReturn($result);
	}
	
	public function actionLogout(){
		Yii::$app->user->logout();
		$this->redirect(['/site/login']);
	}
}
