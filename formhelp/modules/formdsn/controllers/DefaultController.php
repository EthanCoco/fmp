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
use app\modules\formdsn\models\FMPFLOWNODE;
use app\modules\formdsn\models\FMPBUSNODETABLE;

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
		$nodeData = FMPFLOWNODE::getNodeInfo($flowID);
		return $this->jsonReturn(['result'=>1,'infos'=>$jsonData,'nodeInfos'=>$nodeData]);
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
				$result = ['result'=>0,'msg'=>Yii::$app->controller->module->params['4006']];
			}else{
				$result = ['result'=>1,'msg'=>Yii::$app->controller->module->params['4007']];
			}
		}else{
			$result = ['result'=>0,'msg'=>Yii::$app->controller->module->params['4008']];
		}
		
		return $this->jsonReturn($result);
	}
	
	/*根据流程业务表字段列表*/
	public function actionFieldtablelist (){
		$request = Yii::$app->request;
		$tableName = $request->get('flow_table_id','');
		$flowID = $request->get('flow_node_id','');
		//请求参数校验
		if(!$this->valNullParams($tableName,$flowID)){
			return $this->jsonReturn(['result'=>0,'msg'=>Yii::$app->controller->module->params['4001']]);
		}
		
		//获取字段列表信息
		$infos = FMPTABLEFIELD::getListField(['FLOW_ID'=>$flowID,'TABLE_NAME'=>$tableName]);
		//获取列表添加当前字段最大后缀及字段前缀
		$field_infos = FMPTABLEFIELD::getEditFieldInfos($flowID,$tableName);
		
		return $this->jsonReturn(['result'=>1,'rows'=>$infos,'fieldInfos'=>$field_infos]); 
	}
	
	/*保存字段信息*/
	public function actionSavefields(){
		$request = Yii::$app->request;
		
		$tableName = $request->post('tableName','');
		$flowID = $request->post('flowID','');
		$infos = $request->post('infos',[]);
		
		$inserted = isset($infos['inserted']) ? (empty($infos['inserted']) ? '' : json_decode($infos['inserted'])) : '';
		$updated = isset($infos['updated']) ? (empty($infos['updated']) ? '' : json_decode($infos['updated'])) : '';
		$deleted = isset($infos['deleted']) ? (empty($infos['deleted']) ? '' : json_decode($infos['deleted'])) : '';
		
		if(!$this->valNullParams($tableName,$flowID)){
			return $this->jsonReturn(['result'=>0,'msg'=>Yii::$app->controller->module->params['4001']]);
		}
		if(empty($infos) || ($inserted == '' && $updated == '' && $deleted == '')){
			return $this->jsonReturn(['result'=>0,'msg'=>Yii::$app->controller->module->params['4006']]);
		}
		
		//$infos数据过滤
		$realData = FMPTABLEFIELD::fitterField($inserted,$updated,$deleted);
		//保存数据
		FMPTABLEFIELD::saveField($tableName,$flowID,$realData);
	}
	
	/*指向设置环节排序设置界面*/
	public function actionSetnodeorder(){
		return $this->renderPartial('page/set_node_order');
	}

	/*获取流程中的环节树*/
	public function actionFlownodetree(){
		$flowID = Yii::$app->request->get('flowID','');
		if(!$this->valNullParams($flowID)){
			return $this->jsonReturn(['result'=>0,'msg'=>Yii::$app->controller->module->params['4001']]);
		}
		
		$jsonData = FMPFLOWNODE::getFlowNodeTree($flowID);
		
		return $this->jsonReturn(['result'=>1,'infos'=>$jsonData]);
	}
	
	/*获取环节中需要排序的字段*/
	public function actionNodeorderlist(){
		$request = Yii::$app->request;
		$nodeID = $request->get('nodeID','');
		$flowID = $request->get('flowID','');
		
		$jsonData = FMPTABLEFIELD::getFieldNodeList($flowID,$nodeID);
		
		return $this->jsonReturn(['rows'=>$jsonData]);
	}
	
	/*保存字段排序信息*/
	public function actionSavenodeorder(){
		$request = Yii::$app->request;
		$data = $request->post('order_data','');
		$nodeID = $request->post('nodeID','');
		$flowID = $request->post('flowID','');
		$real_data = json_decode($data);
		if(!$this->valNullParams($flowID,$nodeID)){
			return $this->jsonReturn(['result'=>0,'msg'=>Yii::$app->controller->module->params['4001']]);
		}
		
		$flag = FMPTABLEFIELD::saveNodeOrder($flowID,$nodeID,$real_data);
		if($flag){
			$result = ['result'=>1,'msg'=>Yii::$app->controller->module->params['4010']];
		}else{
			$result = ['result'=>0,'msg'=>Yii::$app->controller->module->params['4011']];
		}
		
		return $this->jsonReturn($result);
	}

	/*指向业务表设置界面*/
	public function actionSetbustable(){
		return $this->renderPartial('page/set_bus_table');
	}
	
	/*获取流程中的环节树带业务表节点*/
	public function actionFlownodetreetable(){
		$flowID = Yii::$app->request->get('flowID','');
		if(!$this->valNullParams($flowID)){
			return $this->jsonReturn(['result'=>0,'msg'=>Yii::$app->controller->module->params['4001']]);
		}
		
		$jsonData = FMPFLOWNODE::getFlowNodeTreeTable($flowID);
		
		return $this->jsonReturn(['result'=>1,'infos'=>$jsonData]);
	}
	
	/*指向新建业务表界面*/
	public function actionOperatebustable(){
		$request = Yii::$app->request;
		$nodeID = $request->get('nodeID');
		$busName = $request->get('busName','');
		$bus_desc = '';
		if($busName != ''){
			$infos = FMPBUSNODETABLE::getByAKey(['NODE_ID'=>$nodeID,'BUS_NAME'=>$busName],['BUS_DESC']);
			$bus_desc = $infos['BUS_DESC'];
		}
		
		return $this->renderPartial('page/operate_bus_table',['nodeID'=>$nodeID,'busName'=>$busName,'busDesc'=>$bus_desc]);
	}
	
	/*保存环节对应的业务表单表*/
	public function actionOperatebustabledo(){
		$request = Yii::$app->request;
		$node_id = $request->get('node_id');
		$bus_name = $request->get('bus_name','');
		$bus_table_name = $request->get('bus_table_name');
		
		if(!$this->valNullParams($node_id,$bus_table_name)){
			return $this->jsonReturn(['result'=>0,'msg'=>Yii::$app->controller->module->params['4001']]);
		}
		
		if($bus_name == ''){
			$flag = FMPBUSNODETABLE::saveBusNodeTable($node_id,$bus_table_name);
		
			if($flag){
				$result = ['result'=>1,'msg'=>Yii::$app->controller->module->params['4010']];
			}else{
				$result = ['result'=>0,'msg'=>Yii::$app->controller->module->params['4011']];
			}
		}else{
			$flag = FMPBUSNODETABLE::modBusNodeTable(['NODE_ID'=>$node_id,'BUS_NAME'=>$bus_name],['BUS_DESC'=>$bus_table_name]);
		
			if($flag !== false){
				if($flag){
					$result = ['result'=>1,'msg'=>Yii::$app->controller->module->params['4007']];
				}else{
					$result = ['result'=>0,'msg'=>Yii::$app->controller->module->params['4006']];
				}
				
			}else{
				$result = ['result'=>0,'msg'=>Yii::$app->controller->module->params['4008']];
			}
		}
		
		return $this->jsonReturn($result);
		
	}
	
	/*删除业务表单对应的环节*/
	public function actionDelbustable(){
		$bus_id = Yii::$app->request->get('bus_id','');
	
		if(!$this->valNullParams($bus_id)){
			return $this->jsonReturn(['result'=>0,'msg'=>Yii::$app->controller->module->params['4001']]);
		}
		
		$flag = FMPBUSNODETABLE::delBusNodeTable(['BUS_NAME'=>$bus_id]);
			
		if($flag){
			$result = ['result'=>1,'msg'=>Yii::$app->controller->module->params['4012']];
		}else{
			$result = ['result'=>0,'msg'=>Yii::$app->controller->module->params['4013']];
		}
		
		return $this->jsonReturn($result);
	}
}
