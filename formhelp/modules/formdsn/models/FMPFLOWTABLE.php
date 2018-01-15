<?php

namespace app\modules\formdsn\models;

use Yii;

/**
 * This is the model class for table "FMP_FLOW_TABLE".
 *
 * @property integer $ID
 * @property string $FLOW_TABLE_NAME
 * @property integer $FLOW_TABLE_TYPE
 * @property integer $FLOW_ID
 * @property string $FLOW_TABLE_DESC
 * @property string $FLOW_TABLE_SE_SUFFIX
 * 
 */
class FMPFLOWTABLE extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'FMP_FLOW_TABLE';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//          [['FLOW_TABLE_NAME', 'FLOW_ID'], 'required'],
//          [['FLOW_TABLE_ID', 'FLOW_TABLE_TYPE', 'FLOW_ID'], 'integer'],
//          [['FLOW_TABLE_NAME'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'FLOW_TABLE_NAME' => 'Flow  Table  Name',
            'FLOW_TABLE_TYPE' => 'Flow  Table  Type',
            'FLOW_ID' => 'Flow  ID',
            'FLOW_TABLE_DESC'	=> 'Flow Table Desc',
            'FLOW_TABLE_SE_SUFFIX' => 'Flow Table Se Suffix'
        ];
    }
	
	/*获取流程对应业务表树*/
	public static function getTableTree($flowID){
		$table_info = self::find()->where(['FLOW_ID'=>$flowID])->all();
		foreach($table_info as $info){
			$jsonData[] = [
						'id'=>$info['FLOW_TABLE_NAME'],
						'name'=>'[' . ($info['FLOW_TABLE_TYPE'] == 1 ? '主表' : '副表' ) . ']' . $info['FLOW_TABLE_DESC'],
						'pId'=>'-1',
						'isChild'=>1,
						'title'=>$info['FLOW_TABLE_DESC'],
						'type'=>$info['FLOW_TABLE_TYPE'],
						'isParent' => 'false'
					];
		}
        $jsonData[] = ['id' => '-1', 'name' => '业务表', 'pId' => '-1', 'isParent' => 'true', 'isChild'=>0];
		
		return $jsonData;
	}
	
	/*指定条件数组查询单条唯一记录*/
	public static function findByAKey($condition = []){
		return self::find()->where($condition)->one();
	}
	
	/*添加数据->单条数据*/
	public static function addSingle($data = []){
		$self = new self();
		foreach($data as $key => $value){
			$self->$key = $value;
		}
		$self->save();
	}
	
	/*通过条件删除数据*/
	public static function deleteByKey($condition){
		return self::deleteAll($condition);
	}
	
	/*根据条件查询最大的栏目值*/
	public static function findSMaxVal($condition){
		$max_value =  self::find()->where($condition)->max('FLOW_TABLE_SE_SUFFIX');
		return !empty($max_value) ? intval($max_value) : 0;
	}
	
	/*根据条件更新数据*/
	public static function updateByAKey($conditions,$data){
		return Yii::$app->db->createCommand()->update(self::tableName(), $data, $conditions)->execute();
	}
	
}
