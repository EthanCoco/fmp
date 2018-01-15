<?php

namespace app\modules\formdsn\controllers;

use yii\web\Controller;
use Yii;
use app\controllers\BaseController;

use app\models\User;
use app\models\Share;
use app\modules\formdsn\models\FMPFLOWDIR;
use app\modules\formdsn\models\FMPFLOW;
use app\modules\formdsn\models\FMPFLOWTABLE;
use app\modules\formdsn\models\FMPTABLEFIELD;


class DefaultController extends BaseController
{
	//设置布局模板
	public $layout = 'formdsn'; 
	
	/*首页渲染*/
    public function actionIndex(){
        return $this->render('index');
    }
	
	/*获取生成流程树*/
	public function actionFlowtree(){
		$jsonData = FMPFLOWDIR::getFlowTree();
		return $this->jsonReturn($jsonData);
	}
	
	/*获取流程对应的业务表树*/
	public function actionTabletree(){
		$request = Yii::$app->request;
		//流程ID
		$flowID = $request->get('flow_node_id','');
		//请求参数校验
		if(!$this->valNullParams($flowID)){
			return $this->jsonReturn(['result'=>0,'msg'=>Yii::$app->controller->module->params['4001']]);
		}
		
		$jsonData = FMPFLOWTABLE::getTableTree($flowID);
		
		return $this->jsonReturn(['result'=>1,'infos'=>$jsonData]);
	}
	
	/*添加业务表*/
	public function actionFlowtableadd(){
		$request = Yii::$app->request;
		//流程ID
		$flowID = $request->get('flow_node_id','');
		//表属性
		$table_type = intval($request->get('table_type'));
		//表名称
		$table_desc = $request->get('table_desc');
		
		$result = [];
		$infos = ['result'=>0];
		
		//请求参数校验
		if(!$this->valNullParams($flowID,$table_type,$table_desc)){
			return $this->jsonReturn(['result'=>0,'msg'=>Yii::$app->controller->module->params['4001']]);
		}
		
		//基础参数
		$paramInfos = ['FLOW_ID'=>$flowID,'FLOW_TABLE_TYPE'=>$table_type,'FLOW_TABLE_DESC'=>$table_desc];
		
		//查询是否存在主表
		$main_infos = FMPFLOWTABLE::findByAKey(['FLOW_ID'=>$flowID,'FLOW_TABLE_TYPE'=>1]);
		
		//主表存在
		if($table_type == 1 && !empty($main_infos)){
			$result = ['result'=>0,'msg'=>Yii::$app->controller->module->params['4002']];
		//添加主表
		}elseif($table_type == 1 && empty($main_infos)){
			$infos = $this->generate_table($paramInfos);
		//添加副表->未添加主表
		}elseif($table_type == 2 && empty($main_infos)){
			$result = ['result'=>0,'msg'=>Yii::$app->controller->module->params['4003']];
		//添加副表
		}elseif($table_type == 2 && !empty($main_infos)){
			$infos = $this->generate_table($paramInfos,$main_infos);
		//错误参数类型
		}else{
			$result = ['result'=>0,'msg'=>Yii::$app->controller->module->params['4000']];
		}
		
		//创建表后返回的结果判断
		if($infos['result']){
			$result = ['result'=>1,'msg'=>Yii::$app->controller->module->params['4005']];
		}
		
		return $this->jsonReturn($result);
	}
	
	/*创建表*/
	private function generate_table($paramInfos,$main_infos = []){
		$db = Yii::$app->db;
		//开启事物
		$transaction = $db->beginTransaction();
		try {
			//添加主表
		    if($paramInfos['FLOW_TABLE_TYPE'] == 1){
		    	//生成表名
				$table_name = 'BZ_ZGJOB_' . $paramInfos['FLOW_ID'];
				$paramInfos['FLOW_TABLE_NAME'] = $table_name;
				//创建sql
				$_sql = file_get_contents('../web/bussql/create_btable_m.sql');
				$_sql = str_replace('@@TABLE_NAME@@',$table_name,$_sql);
			//添加副表
			}elseif($paramInfos['FLOW_TABLE_TYPE'] == 2){
				//查找最大后缀
				$suffix_num = FMPFLOWTABLE::findSMaxVal(['FLOW_ID'=>$paramInfos['FLOW_ID'],'FLOW_TABLE_TYPE'=>$paramInfos['FLOW_TABLE_TYPE']]);
				//自增1形成新的后缀
				$suffix_num ++;
				//生成表名	
				$table_name = $main_infos['FLOW_TABLE_NAME'].'_SE_'.strval($suffix_num);
				$paramInfos['FLOW_TABLE_NAME'] = $table_name;
				$paramInfos['FLOW_TABLE_SE_SUFFIX'] = $suffix_num;
				//创建sql
				$_sql = file_get_contents('../web/bussql/create_btable_s.sql');
				$_sql = str_replace('@@TABLE_NAME@@',$table_name,$_sql);
			}
			//生成表
			$db->createCommand($_sql)->execute();
			//添加到表记录
			FMPFLOWTABLE::addSingle($paramInfos);
			//提交事物
			$transaction->commit();
			$result = ['result'=>1];
		} catch(\Exception $e) {
			print_r($e);
			//事物失败删除相关操作
			$db->createCommand("drop table $table_name")->execute();
			FMPFLOWTABLE::deleteByKey(['FLOW_TABLE_NAME'=>$table_name]);
			
		    $transaction->rollBack();
		    $result = ['result'=>0,'msg'=>Yii::$app->controller->module->params['4004']];
		}	
		
		return $result;
	}
	
	/*指向编辑修改表界面*/
	public function actionModtabledesc(){
		return $this->renderPartial('page/mod_table_desc');
	}
	
	/*修改保存动作*/
	public function actionModtabledescdo(){
		$request = Yii::$app->request;
		$table_desc = $request->get('table_desc','');
		$table_id = $request->get('table_id','');
		
		//请求参数校验
		if(!$this->valNullParams($table_id,$table_desc)){
			return $this->jsonReturn(['result'=>0,'msg'=>Yii::$app->controller->module->params['4001']]);
		}
		
		$flag = FMPFLOWTABLE::updateByAKey(['FLOW_TABLE_NAME'=>$table_id],['FLOW_TABLE_DESC'=>$table_desc]);
		
		if($flag !== false){
			if(!$flag){
				$result = ['result'=>0,'msg'=>'数据没有改动，不需要修改'];
			}else{
				$result = ['result'=>1,'msg'=>'修改成功'];
			}
		}else{
			$result = ['result'=>0,'msg'=>'修改失败'];
		}
		
		return $this->jsonReturn($result);
	}
	
	public function actionFieldtablelist (){
		$request = Yii::$app->request;
		$flow_table_id = $request->get('flow_table_id','');
		$flow_node_id = $request->get('flow_node_id','');
		$infos = FMPTABLEFIELD::find()->where(['FLOW_ID'=>$flow_node_id,'TABLE_NAME'=>$flow_table_id])->asArray()->all();
	
		return $this->jsonReturn($infos); 
	}
	
}
