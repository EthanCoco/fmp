<?php

namespace app\modules\formdsn\controllers;

use yii\web\Controller;
use Yii;
use yii\helpers\Html;
use app\controllers\BaseController;

use app\models\User;
use app\models\Share;
use app\models\Code;
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
		
		$jsonData = FMPFLOWTABLE::getTableTree2($flowID);
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
	
	/*指向业务表设置界面2*/
	public function actionSetbustable2(){
		return $this->renderPartial('page/set_bus_table2');
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
	
	/*根据流程业务表字段列表----业务表设置*/
	public function actionFieldtablelistbus (){
		$request = Yii::$app->request;
		$tableName = $request->get('flow_table_id','');
		$flowID = $request->get('flow_node_id','');
		//请求参数校验
		if(!$this->valNullParams($tableName,$flowID)){
			return $this->jsonReturn(['result'=>0,'msg'=>Yii::$app->controller->module->params['4001']]);
		}
		
		//获取字段列表信息
		$infos = FMPTABLEFIELD::getListField(['FLOW_ID'=>$flowID,'TABLE_NAME'=>$tableName]);
		return $this->jsonReturn(['result'=>1,'infos'=>$infos]); 
	}
	
	/*创建目录*/
	private function mkdirs($dir, $mode = 0777){
	    if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
	    if (!self::mkdirs(dirname($dir), $mode)) return FALSE;
	    return @mkdir($dir, $mode);
	}
	
	/*上传文件*/
	public function actionUploadexcel(){
		$bus_id = Yii::$app->request->post('bus_id');
		$flow_id = Yii::$app->request->post('flow_id');
		
		$file = $_FILES['file'];
		$timeNow = date('Y-m-d H:i:s',time());
		
		$dir_flow = $flow_id;
		
		$fileName = $bus_id.'.xls';
		
		if($_FILES['file']['size'] > 2*1024*1024){
			return ['code'=>'1','msg'=>'文件大小不能大于2M','data'=>['src'=>'']];
		}
		
		$createDir = '../web/uploadfile/file2/textfile/'.$dir_flow;
		
		$this->mkdirs($createDir);
		
		move_uploaded_file($_FILES['file']['tmp_name'], $createDir."/".$fileName);
		
		$resultFile = $createDir."/".$fileName;
		
		$fileType = \PHPExcel_IOFactory::identify($resultFile);
		
		$objReader = \PHPExcel_IOFactory::createReader($fileType);
		$objPHPExcel = $objReader->load($resultFile);
		$savePath = $createDir.'/'.$dir_flow.'_'.$bus_id.'.html'; 
		$temp_path = $createDir.'/'.$bus_id.'.html'; 
		$objWriter = new \PHPExcel_Writer_HTML($objPHPExcel); 
		
		$objWriter->setSheetIndex(0); //可以将括号中的0换成需要操作的sheet索引
		$objWriter->save($savePath); //保存为html文件
		$objWriter->save($temp_path); //保存为编辑直观显示html文件
		$fileContent = file_get_contents($temp_path);
		$fileContent = str_replace("<html>", '', $fileContent);
		$fileContent = str_replace("</html>", '', $fileContent);
		$fileContent = str_replace("<head>", '', $fileContent);
		$fileContent = str_replace("</head>", '', $fileContent);
		$fileContent = str_replace("<body>", '', $fileContent);
		$fileContent = str_replace("</body>", '', $fileContent);
		
		$fileContent = preg_replace("/<meta.+>/Umi", "", $fileContent);
		$fileContent = preg_replace("/<!DOCTYPE.+>/Umi", "", $fileContent);
		
		file_put_contents($temp_path, $fileContent); 
		
		
		
		/*处理文件节点*/
		//获取字段列表信息
		$infos = FMPTABLEFIELD::getListField(['FLOW_ID'=>$flow_id]);
		
		$html = new \Simple_html_dom();
		$html->load_file($savePath);
		$table = $html->find("#sheet0",0);
		$table->setAttribute('name','containDiv');
		foreach($html->find('table tr td') as $e){
			if(is_numeric(trim($e->plaintext))){
				$flag_id = trim($e->plaintext);
				foreach($infos as $df){
					if($df['FIELD_ID'] == $flag_id){
						$e->setAttribute('name',$df['FIELD_NAME']);
						$e->setAttribute('title',$df['FIELD_DESC']);
						$e->setAttribute('id',$df['FIELD_NAME'].'_'.$df['FIELD_ID']);
						$e->innertext  = '';
						break;	
					}
				}
			}
			if(trim($e->plaintext) != ''){
				$content = trim($e->plaintext);
				$array_A = explode('=', $content);
				$len_A  = count($array_A);
				if($len_A > 1){
					if($array_A[2] == '0'){
						$e->innertext = $array_A[0]."<a href='javascript:;'  onclick='open_mul_record(this)' name='".$array_A[1]."' >[编辑]</a>";
					}else{
						$data = FMPTABLEFIELD::getByAKey(['FIELD_ID'=>$array_A[2]]);
						$e->setAttribute('originname',$data['FIELD_NAME']);
						$e->innertext = $array_A[0];
						$e->setAttribute('title',$array_A[0]);
						$e->setAttribute('origintable',$array_A[1]);
						$e->setAttribute('originid',$data['FIELD_NAME'].'_'.$data['FIELD_ID']);
					}
				}
			}
		}
		
		$doc = $html;
		file_put_contents($savePath, $doc);
		$html->clear(); 
		return $this->jsonReturn(['code'=>0,'msg'=>'','data'=>['src'=>'../../'.$temp_path]]);
	}
	
	/*保存表单提交的html*/
	public function actionSavehtml(){
		$bus_id = Yii::$app->request->post('busID');
		$flow_id = Yii::$app->request->post('flowID');
		$content = Yii::$app->request->post('content');
		$content = str_replace("<br/>", '', $content);
		$content = str_replace("<br>", '', $content);
		
		if(!$this->valNullParams($bus_id,$flow_id)){
			return $this->jsonReturn(['result'=>0,'msg'=>Yii::$app->controller->module->params['4001']]);
		}
		
		$createDir = '../web/uploadfile/file3/textfile/'.$flow_id;
		
		$this->mkdirs($createDir);
		
		$savePath = $createDir.'/'.$bus_id.'.html';
		
		$bus_html = fopen($savePath, 'w');
		fwrite($bus_html, "<div>");
		fwrite($bus_html, $content);
		fwrite($bus_html, "</div>");
		fclose($bus_html);
		
		
		
		/*处理文件节点*/
		//获取字段列表信息
		$infos = FMPTABLEFIELD::getListField(['FLOW_ID'=>$flow_id]);
		
		$html = new \Simple_html_dom();
		$html->load_file($savePath);
		foreach($html->find('table') as $table){
			foreach($table->find('tr td') as $e){
				if(is_numeric(trim($e->plaintext))){
					$flag_id = trim($e->plaintext);
					foreach($infos as $df){
						if($df['FIELD_ID'] == $flag_id){
							$e->setAttribute('name',$df['FIELD_NAME']);
							$e->setAttribute('title',$df['FIELD_DESC']);
							$e->setAttribute('id',$df['FIELD_NAME'].'_'.$df['FIELD_ID']);
							$e->innertext  = '';
							break;	
						}
					}
				}
//				if(trim($e->plaintext) != ''){
//					$content = trim($e->plaintext);
//					$array_A = explode('=', $content);
//					$len_A  = count($array_A);
//					if($len_A > 1){
//						if($array_A[2] == '0'){
//							$e->innertext = $array_A[0]."<a href='javascript:;'  onclick='open_mul_record(this)' name='".$array_A[1]."' >[编辑]</a>";
//						}else{
//							$data = FMPTABLEFIELD::getByAKey(['FIELD_ID'=>$array_A[2]]);
//							$e->setAttribute('originname',$data['FIELD_NAME']);
//							$e->innertext = $array_A[0];
//							$e->setAttribute('title',$array_A[0]);
//							$e->setAttribute('origintable',$array_A[1]);
//							$e->setAttribute('originid',$data['FIELD_NAME'].'_'.$data['FIELD_ID']);
//						}
//					}
//				}
			}
		}
		
		if(count($html->find("input[leipiplugins='listctrl']"))>0){
			
			foreach($html->find("input[leipiplugins='listctrl']") as $mul){
				
				$title = $mul->getAttribute('title');
				$fields = $mul->getAttribute('orgtitle');
				$fields = rtrim($fields,'`');
				$array_B = explode('`', $fields);
				
				$field_ids = $mul->getAttribute('orgcolvalue');
				$field_ids = rtrim($field_ids,'`');
				$array_A = explode('`', $field_ids);
				$len_A = count($array_A);
				$temp_html = "<table id='$title'><tr><td style='word-break: break-all; border-width: 1px; border-style: solid;' colspan='$len_A'>BBBB</td></tr>";
				for($i=0;$i<$len_A;$i++){
					$data = FMPTABLEFIELD::getByAKey(['FIELD_ID'=>$array_A[$i]]);
					$originid = $data['FIELD_NAME'].'_'.$data['FIELD_ID'];
					$temp_html .="<td style='word-break: break-all; border-width: 1px; border-style: solid;' originname='".$data['FIELD_NAME']."'  title='".$array_B[$i]."' origintable='".$title."' originid='".$originid."' >".$array_B[$i]."</td>";
				}
				$temp_html .= "</table>";
				$p = $mul->parent;
				$p->innertext = '';
				$p->innertext = $temp_html;
			}
		}
		
		$doc = $html;
		$savePath2 = $createDir.'/'.$flow_id.'_'.$bus_id.'.html';
		file_put_contents($savePath2, $doc);
		$html->clear();
		
		return $this->jsonReturn(['result'=>1,'msg'=>Yii::$app->controller->module->params['4010'],'filePath'=>'../../'.$savePath]);
	}
	
	/*指向预览页面2*/
	public function actionPrintview2(){
		$request = Yii::$app->request;
		$type = $request->get('type',0);
		$tableName = $request->get('busName','');
		$bus_table_name = $request->get('bus_table_name','');
		$flow_id = $request->get('flow_id','');
		if(!$this->valNullParams($tableName,$flow_id,$bus_table_name)){
			return $this->jsonReturn(['result'=>0,'msg'=>Yii::$app->controller->module->params['4001']]);
		}
		
		$filePath = '../web/uploadfile/file3/textfile/'.$flow_id.'/'.$flow_id.'_'.$tableName.'.html';
		if(!file_exists($filePath)){
			return $this->jsonReturn(['result'=>0,'msg'=>Yii::$app->controller->module->params['4001']]);
		}
		
		//获取字段列表信息
		$infos = FMPTABLEFIELD::getListField(['FLOW_ID'=>$flow_id,'TABLE_NAME'=>$bus_table_name]);
		
		return $this->renderPartial('page/print_view',['type'=>$type,'file'=>'../../'.$filePath,'infos'=>$infos]);
	}
	
	
	public function actionGetbushtml(){
		$request = Yii::$app->request;
		$bus_id = $request->get('bus_id','');
		$flow_id = $request->get('flow_id','');
		//请求参数校验
		if(!$this->valNullParams($bus_id,$flow_id)){
			return $this->jsonReturn(['result'=>0,'msg'=>Yii::$app->controller->module->params['4001']]);
		}
		
		$filePath = '../web/uploadfile/file3/textfile/'.$flow_id.'/'.$bus_id.'.html';
		$content = '';
		if(file_exists($filePath)){
			$content = file_get_contents($filePath);
		}
		
		return $this->jsonReturn(['result'=>1,'msg'=>'','content'=>$content]);
	}
	
	
	private function getExcelHtml($flow_id,$bus_id){
		$filePath = '../web/uploadfile/file2/textfile/'.$flow_id.'/'.$bus_id.'.html';
		$file = '';
		if(file_exists($filePath)){
			$file = '../../'.$filePath;
		}
		return $file;
	}
	
	/*获取excel生成的html*/
	public function actionGetexcelhtml(){
		$request = Yii::$app->request;
		$bus_id = $request->get('bus_id','');
		$flow_id = $request->get('flow_id','');
		//请求参数校验
		if(!$this->valNullParams($bus_id,$flow_id)){
			return $this->jsonReturn(['result'=>0,'msg'=>Yii::$app->controller->module->params['4001']]);
		}
		
		$file = $this->getExcelHtml($flow_id,$bus_id);
		
		return $this->jsonReturn(['result'=>1,'msg'=>'','file'=>$file]);
	}
	
	/*指向预览页面*/
	public function actionPrintview(){
		$request = Yii::$app->request;
		$type = $request->get('type',0);
		$tableName = $request->get('busName','');
		$bus_table_name = $request->get('bus_table_name','');
		$flow_id = $request->get('flow_id','');
		if(!$this->valNullParams($tableName,$flow_id,$bus_table_name)){
			return $this->jsonReturn(['result'=>0,'msg'=>Yii::$app->controller->module->params['4001']]);
		}
		
		$filePath = '../web/uploadfile/file2/textfile/'.$flow_id.'/'.$flow_id.'_'.$tableName.'.html';
		if(!file_exists($filePath)){
			return $this->jsonReturn(['result'=>0,'msg'=>Yii::$app->controller->module->params['4001']]);
		}
		
		$fileContent = file_get_contents($filePath);
		
		$fileContent = str_replace("<html>", '', $fileContent);
		$fileContent = str_replace("</html>", '', $fileContent);
		$fileContent = str_replace("<head>", '', $fileContent);
		$fileContent = str_replace("</head>", '', $fileContent);
		$fileContent = str_replace("<body>", '', $fileContent);
		$fileContent = str_replace("</body>", '', $fileContent);
		$fileContent = preg_replace("/<meta.+>/Umi", "", $fileContent);
		$fileContent = preg_replace("/<!DOCTYPE.+>/Umi", "", $fileContent);
		
		$temp_save_file = '../web/uploadfile/file2/textfile/'.$flow_id.'/temp_'.$flow_id.'_'.$tableName.'.html';
		
		file_put_contents($temp_save_file, $fileContent); 
		
		//获取字段列表信息
		$infos = FMPTABLEFIELD::getListField(['FLOW_ID'=>$flow_id,'TABLE_NAME'=>$bus_table_name]);
		
		return $this->renderPartial('page/print_view',['type'=>$type,'file'=>'../../'.$temp_save_file,'infos'=>$infos]);
	}
	
	
	/*控件页面*/
	public function actionControlpage(){
		$viewtype = Html::encode(Yii::$app->request->get('view_type'));
		$codeindex = Html::encode(Yii::$app->request->get('code_index'));
		$singleOrMore = Html::encode(Yii::$app->request->get('singleOrMore'));
		if($viewtype=='1'){
			return $this->renderPartial('codepage/codeList',['codeindex'=>$codeindex]);
		}else if($viewtype=='2'){
			if($singleOrMore=="1"){
				return $this->renderPartial('codepage/codeTree',['codeindex'=>$codeindex]);
			}else{
				return $this->renderPartial('codepage/codeTreeCheck',['codeindex'=>$codeindex]);
			}
		}
	}
	
	/*列表型控件*/
	public function actionList(){
		$codeindex = Html::encode(Yii::$app->request->get('codeindex'));
			
		$codelist = Code::find()->where(['codeTypeID'=>$codeindex,'codeStatus'=>1])->orderby('codeOrder asc')->asArray()->all();
		$listData = [];
		foreach($codelist as $code){
			$listData[] = ["id"=>$code['codeID'],"name"=>$code['codeName'],"pId"=>"-1","code"=>$code['codeID']];
		}
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $listData;
	}
	
	/*树型控件*/
	public function actionTree(){
		$codeindex = Html::encode(Yii::$app->request->get('codeindex'));
		
		$codetree = Code::find()->where(['codeTypeID'=>$codeindex,'codeStatus'=>1])->orderby('codeOrder asc')->asArray()->all();
		$treeData = [];
		foreach($codetree as $code){
			$treeData[] = ["id"=>$code['codeID'],"name"=>$code['codeName'],"pId"=>$code['codePid'],"code"=>$code['codeID'],"isleaf"=>$code['isLeaf']];
		}
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $treeData;
	}
	
	/*上传图片*/
	public function actionUploadimage(){
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		date_default_timezone_set('PRC');
		$file = $_FILES['file'];
		$type = strtolower($_FILES['file']["type"]);
		
		$timeNow = date('Y-m-d H:i:s',time());
		
		$tmpfile = time();
		$fileName = $tmpfile.'.'.explode("/", $type)[1];
		if(!in_array($type, ['image/jpg','image/gif','image/png','image/jpeg'])){
			return ['code'=>'1','msg'=>'图片格式不正确','data'=>['src'=>'']];
		}
		$createDir = '../web/uploadfile/image/';
		move_uploaded_file($_FILES['file']['tmp_name'], $createDir."/".$fileName);
		$resultFile = $createDir."/".$fileName;
		return ['code'=>'0','msg'=>'图片大小不能大于2M','data'=>['src'=>'../../'.$createDir."/".$fileName]];
	}
	
}
