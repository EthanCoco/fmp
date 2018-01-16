<?php

namespace app\modules\formdsn\models;

use Yii;
use app\modules\formdsn\models\Merror;

/**
 * This is the model class for table "FMP_TABLE_FIELD".
 *
 * @property integer $FIELD_ID
 * @property string $FIELD_NAME
 * @property string $FIELD_DESC
 * @property integer $FIELD_TYPE
 * @property string $FIELD_CODE
 * @property string $FIELD_VERIFY
 * @property string $FIELD_BELONG_NODE
 * @property integer $FIELD_GLOBE_REQUIRE
 * @property integer $FLOW_ID
 * @property string $TABLE_NAME
 */
class FMPTABLEFIELD extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'FMP_TABLE_FIELD';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//          [['FIELD_NAME', 'FLOW_ID', 'TABLE_NAME'], 'required'],
//          [['FIELD_TYPE', 'FIELD_GLOBE_REQUIRE', 'FLOW_ID'], 'integer'],
//          [['FIELD_NAME', 'FIELD_CODE', 'TABLE_NAME'], 'string', 'max' => 64],
//          [['FIELD_DESC', 'FIELD_VERIFY', 'FIELD_BELONG_NODE'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'FIELD_ID' => 'Field  ID',
            'FIELD_NAME' => 'Field  Name',
            'FIELD_DESC' => 'Field  Desc',
            'FIELD_TYPE' => 'Field  Type',
            'FIELD_CODE' => 'Field  Code',
            'FIELD_VERIFY' => 'Field  Verify',
            'FIELD_BELONG_NODE' => 'Field  Belong  Node',
            'FIELD_GLOBE_REQUIRE' => 'Field  Globe  Require',
            'FLOW_ID' => 'Flow  ID',
            'TABLE_NAME' => 'Table  Name',
        ];
    }
	
	/*根据条件获取所有满足条件的数据->可指定查询字段*/
	public static function getListField($condition = [],$fields = '*'){
		return self::find()->select($fields)->where($condition)->asArray()->all();
	}
	
	/*获取添加字段时最大后缀及默认字段前缀*/
	public static function getEditFieldInfos($flowID,$tableName){
		$field_prefix = Yii::$app->controller->module->params['bus_table_field_prefix'];
		$max_field = self::find()->where(['FLOW_ID'=>$flowID,'TABLE_NAME'=>$tableName])->max('FIELD_NAME');
		
		
		//判断
		$field_num = empty($max_field) ? 1 : (intval(str_replace($field_prefix, '', $max_field)) + 1);
		
		return ['fieldNum'=>$field_num,'fieldPrefix'=>$field_prefix];
	}
	
	public static function getField(){
		 return [
            'FIELD_NAME',
            'FIELD_DESC',
            'FIELD_TYPE',
            'FIELD_CODE',
            'FIELD_BELONG_NODE',
            'FIELD_GLOBE_REQUIRE',
            'FLOW_ID',
            'TABLE_NAME'
        ];
	}

	/*保存数据信息过滤数据*/
	public static function fitterField($inserted,$updated,$deleted){
		$insert_data = [];
		$update_data = [];
		$deleted_data = [];
		if($inserted != ''){
			foreach($inserted as $obj){
				if(!isset($obj->FIELD_NAME) || $obj->FIELD_NAME == ''){
					continue;
				}
				if(!isset($obj->FIELD_DESC) || $obj->FIELD_DESC == ''){
					continue;
				}
				if(!isset($obj->FIELD_TYPE) || $obj->FIELD_TYPE == ''){
					continue;
				}elseif($obj->FIELD_TYPE == '4' && isset($obj->FIELD_CODE) && $obj->FIELD_CODE == ''){
					continue;
				}elseif(isset($obj->FIELD_CODE) && $obj->FIELD_CODE != '' && is_string($obj->FIELD_CODE)){
					
				}
				
				$insert_data[] = [
					'FIELD_NAME' => $obj->FIELD_NAME,
					'FIELD_DESC' => $obj->FIELD_DESC,
					'FIELD_TYPE' => explode('=', $obj->FIELD_TYPE)[0],
					'FIELD_CODE' => !empty(isset($obj->FIELD_CODE) ? $obj->FIELD_CODE : '') ? strtoupper($obj->FIELD_CODE) : '',
					'FIELD_BELONG_NODE' => isset($obj->FIELD_BELONG_NODE) ? $obj->FIELD_BELONG_NODE : '',
					'FIELD_GLOBE_REQUIRE' => !empty(isset($obj->FIELD_GLOBE_REQUIRE) ? $obj->FIELD_GLOBE_REQUIRE : '') ? (explode('=', $obj->FIELD_GLOBE_REQUIRE)[0]) : '',
				];
			}
		}
		
		if($updated != ''){
			foreach($updated as $obj){
				if(!isset($obj->FIELD_NAME) || $obj->FIELD_NAME == ''){
					continue;
				}
				if(!isset($obj->FIELD_DESC) || $obj->FIELD_DESC == ''){
					continue;
				}
				if(!isset($obj->FIELD_TYPE) || $obj->FIELD_TYPE == ''){
					continue;
				}elseif($obj->FIELD_TYPE == '4' && isset($obj->FIELD_CODE) && $obj->FIELD_CODE == ''){
					continue;
				}elseif(isset($obj->FIELD_CODE) && $obj->FIELD_CODE != '' && is_string($obj->FIELD_CODE)){
					
				}
				
				$update_data[] = [
					'FIELD_NAME' => $obj->FIELD_NAME,
					'FIELD_DESC' => $obj->FIELD_DESC,
					'FIELD_TYPE' => explode('=', $obj->FIELD_TYPE)[0],
					'FIELD_CODE' => !empty(isset($obj->FIELD_CODE) ? $obj->FIELD_CODE : '') ? strtoupper($obj->FIELD_CODE) : '',
					'FIELD_BELONG_NODE' => isset($obj->FIELD_BELONG_NODE) ? $obj->FIELD_BELONG_NODE : '',
					'FIELD_GLOBE_REQUIRE' => !empty(isset($obj->FIELD_GLOBE_REQUIRE) ? $obj->FIELD_GLOBE_REQUIRE : '') ? (explode('=', $obj->FIELD_GLOBE_REQUIRE)[0]) : '',
				];
			}
		}
		
		if($deleted != ''){
			foreach($deleted as $obj){
				if(!isset($obj->FIELD_NAME) || $obj->FIELD_NAME == ''){
					continue;
				}
				$deleted_data[] = [
					'FIELD_NAME' => $obj->FIELD_NAME,
				];
			}
		}
		return ['inserted'=>$insert_data,'updated'=>$update_data,'deleted'=>$deleted_data];
	}
	
	/*校验表是否存在*/
	private static function checkTable($tableName){
		$db = Yii::$app->db;
		try{
		    $db->createCommand("desc `$tableName`")->execute();
		} catch (\Exception $e) {
			Merror::getInstance()->jsonReturn(['result'=>0,'msg'=>Yii::$app->controller->module->params['4009']]);
		}	
	}
	
	/*保存字段相关信息*/
	public static function saveField($tableName,$flowID,$infos = []){
		self::checkTable($tableName);
		$db = Yii::$app->db;
		$transaction = $db->beginTransaction();
		try{
			//批量添加
			$insert_data = [];
			//循环更新
			$update_data = [];
			//批量删除
			$delete_data = [];
			$_sql = "alter table `$tableName` ";
			if(isset($infos['inserted']) && !empty($infos['inserted'])){
				$inserted_infos = $infos['inserted'];
				$temp_data = [];
				foreach($inserted_infos as $field){
					$_sql .= "add column `" . $field['FIELD_NAME'] . "` varchar(100) default null comment '". $field['FIELD_DESC'] . "',";
					$temp_data = $field;
					$temp_data['TABLE_NAME'] = $tableName;
					$temp_data['FLOW_ID'] = $flowID;
					$insert_data[] = $temp_data;
				}
				
	            $insetrData = [];
				foreach($insert_data as $data){
					$insetrData[] = [
						$data['FIELD_NAME'],
			            $data['FIELD_DESC'],
			            $data['FIELD_TYPE'],
			            $data['FIELD_CODE'],
			            $data['FIELD_BELONG_NODE'],
			            $data['FIELD_GLOBE_REQUIRE'],
			            $data['FLOW_ID'],
			            $data['TABLE_NAME'],
					];
				}
				
			}
			
			if(isset($infos['updated']) && !empty($infos['updated'])){
				$updated_infos = $infos['updated'];
				$temp_data = [];
				foreach($updated_infos as $field){
					$_sql .= "modify column `" . $field['FIELD_NAME'] . "` varchar(100) default null comment '". $field['FIELD_DESC'] . "',";
					$temp_data = $field;
					$update_data[] = $temp_data;
				}
			}
			
			if(isset($infos['deleted']) && !empty($infos['deleted'])){
				$deleted_infos = $infos['deleted'];
				foreach($deleted_infos as $field){
					$_sql .= "drop " . $field['FIELD_NAME'] . ",";
					$delete_data[] = $field['FIELD_NAME'];
				}
			}
			
			//添加
			if(!empty($insetrData)){
				$db->createCommand()->batchInsert(self::tableName(),self::getField(), $insetrData)->execute();
			}
			
			//更新
			if(!empty($update_data)){
				foreach($update_data as $fd){
					$db->createCommand()->update(self::tableName(), $fd , ['TABLE_NAME'=>$tableName,'FLOW_ID'=>$flowID,'FIELD_NAME'=>$fd['FIELD_NAME']])->execute();
				}
			}
			
			//删除
			if(!empty($delete_data)){
				$db->createCommand()->delete(self::tableName(),['FIELD_NAME'=>$delete_data,'TABLE_NAME'=>$tableName,'FLOW_ID'=>$flowID])->execute();
			}
			
			//表操作
			$_sql = rtrim($_sql,',').";";
			$db->createCommand($_sql)->execute();
			
			
			$transaction->commit();
			
			Merror::getInstance()->jsonReturn(['result'=>1,'msg'=>Yii::$app->controller->module->params['4010']]);
		}catch(\Exception $e) {
			$transaction->rollBack();
			Merror::getInstance()->jsonReturn(['result'=>0,'msg'=>Yii::$app->controller->module->params['4011']]);
		}
	}

}
