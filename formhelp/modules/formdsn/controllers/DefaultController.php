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
use app\modules\formdsn\models\FMPASSIST;
use app\modules\formdsn\models\FMPTABLEFIELD;


class DefaultController extends BaseController
{
	//设置布局模板
	public $layout = 'formdsn'; 
    public function actionIndex(){
        return $this->render('index');
    }
	
	public function actionFlowtree(){
		$flowdir_info = FMPFLOWDIR::find()->asArray()->all();
		foreach($flowdir_info as $info){
			$flow_info = FMPFLOW::find()->where(['FLOW_DIRID'=>$info['FLOW_DIRID']])->asArray()->all();
			if(!empty($flow_info)){
				$resultInfo[] = [
					'id'=>'dir_'.$info['FLOW_DIRID'],
					'name'=>$info['FLOW_DIRNAME'],
					'pId'=>'-1',
					'isChild'=>0,
					'isParent' => 'true'
				];
				
				foreach($flow_info as $sub_info){
					$resultInfo[] = [
						'id'=>$sub_info['FLOW_ID'],
						'name'=>$sub_info['FLOW_NAME'],
						'pId'=>'dir_'.$sub_info['FLOW_DIRID'],
						'isChild'=>1,
						'isParent' => 'false'
					];
				}
				
			}
		}
		
        $resultInfo[] = ['id' => '-1', 'name' => '流程分类', 'pId' => '-1', 'isParent' => 'true', 'isChild'=>0];
		
		return $this->jsonReturn($resultInfo);
	}
	
	public function actionTabletree(){
		$request = Yii::$app->request;
		$flowID = $request->get('flow_node_id','');
		$table_info = FMPFLOWTABLE::find()->where(['FLOW_ID'=>$flowID])->all();
		foreach($table_info as $info){
			$jsonData[] = [
						'id'=>$info['FLOW_TABLE_NAME'],
						'name'=>$info['FLOW_TABLE_NAME'].'['.$info['FLOW_TABLE_DESC'].']',
						'pId'=>'-1',
						'isChild'=>1,
						'title'=>$info['FLOW_TABLE_DESC'],
						'type'=>$info['FLOW_TABLE_TYPE'],
						'isParent' => 'false'
					];
		}
        $jsonData[] = ['id' => '-1', 'name' => '业务表', 'pId' => '-1', 'isParent' => 'true', 'isChild'=>0];
		
		return $this->jsonReturn($jsonData);
	}
	
	public function actionFlowtableadd(){
		$request = Yii::$app->request;
		$flowID = $request->get('flow_node_id','');
		$table_type = intval($request->get('table_type'));
		$table_name_num = $request->get('table_name','');
		$table_desc = $request->get('table_desc');
		
		$return_msg = ['','主表','副表'];
		$infos = ['result'=>0];
		
		$is_exist_mian = FMPFLOWTABLE::find()->where(['FLOW_ID'=>$flowID,'FLOW_TABLE_TYPE'=>1])->count();
		
		if($table_type == 1 && $is_exist_mian == 1){
			$result = ['result'=>0,'msg'=>'主表已经存在了'];
		}elseif($table_type == 1 && $is_exist_mian == 0){
			$infos = $this->generate_table(1);
		}elseif($table_type == 2 && $is_exist_mian == 0){
			$result = ['result'=>0,'msg'=>'请先添加主表'];
		}elseif($table_type == 2 && $is_exist_mian == 1){
			$table_m_info = FMPFLOWTABLE::find()->select(['FLOW_TABLE_NAME','FLOW_ID'])->where(['FLOW_ID'=>$flowID,'FLOW_TABLE_TYPE'=>1])->one();
			$infos = $this->generate_table(2,$table_m_info,$table_name_num);
		}
		
		if($infos['result']){
			$table_name = $infos['table_name'];
			$FMPFLOWTABLE = new FMPFLOWTABLE();
			$FMPFLOWTABLE->FLOW_ID = $flowID;
			$FMPFLOWTABLE->FLOW_TABLE_NAME = $table_name;
			$FMPFLOWTABLE->FLOW_TABLE_TYPE = $table_type;
			$FMPFLOWTABLE->FLOW_TABLE_DESC = $table_desc;
			$FMPFLOWTABLE->save();
			
			$result = ['result'=>1,'msg'=>'创建'.$return_msg[$table_type].'成功'];
		}
		
		return $this->jsonReturn($result);
	}
	
	private function generate_table($type,$t_name_info = [],$t_name_num = ''){
		$db = Yii::$app->db;
		$transaction = $db->beginTransaction();
		
		try {
		    if($type == 1){
				$assist_info = FMPASSIST::find()->asArray()->one();
				$table_name = 'BZ_ZGJOB_'.strval($assist_info['ASSIST_ID']);
				$_sql = file_get_contents('../web/bussql/create_btable_m.sql');
				$_sql = str_replace('@@TABLE_NAME@@',$table_name,$_sql);
				$db->createCommand($_sql)->execute();
				$result = ['result'=>1,'table_name'=>$table_name];
			}else if($type == 2){
				$table_name = $t_name_info['FLOW_TABLE_NAME'].'_SE_'.strval($t_name_num);
				$is_exist_pos = FMPFLOWTABLE::find()->where(['FLOW_ID'=>$t_name_info['FLOW_ID'],'FLOW_TABLE_TYPE'=>2])->count();
				if($is_exist_pos){
					$this->jsonReturn(['result'=>0,'msg'=>'副表已经存在了']);
				}
				
				$_sql = file_get_contents('../web/bussql/create_btable_s.sql');
				$_sql = str_replace('@@TABLE_NAME@@',$table_name,$_sql);
				$db->createCommand($_sql)->execute();
			}
			
			$transaction->commit();
			if($type == 1){
				FMPASSIST::updateAllCounters(['ASSIST_ID' => 1]);
			}
			
			return ['result'=>1,'table_name'=>$table_name];
		} catch(\Exception $e) {
		    $transaction->rollBack();
		    $this->jsonReturn(['result'=>0,'msg'=>'创建表失败']);
		}	
	}
	
	public function actionModtabledesc(){
		$request = Yii::$app->request;
		$flow_table_id = $request->get('flow_table_id','');
		$data = FMPFLOWTABLE::find()->where(['FLOW_TABLE_NAME'=>$flow_table_id])->asArray()->one();
		return $this->renderPartial('page/mod_table_desc',['table_info'=>$data]);
	}
	
	public function actionModtabledescdo(){
		$request = Yii::$app->request;
		$table_desc = $request->get('table_desc','');
		$table_id = $request->get('table_id','');
		
		$FMPFLOWTABLE = FMPFLOWTABLE::findOne($table_id);
		$FMPFLOWTABLE->FLOW_TABLE_DESC = $table_desc;
		$flag = $FMPFLOWTABLE->save();
		
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
