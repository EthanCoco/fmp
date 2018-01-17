<?php

namespace app\modules\formdsn\models;

use Yii;

/**
 * This is the model class for table "fmp_bus_node_table".
 *
 * @property string $BUS_ID
 * @property string $NODE_ID
 * @property string $BUS_NAME
 * @property string $BUS_DESC
 * @property integer $BUS_SUFFIX
 */
class FMPBUSNODETABLE extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fmp_bus_node_table';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//          [['NODE_ID', 'BUS_NAME'], 'required'],
//          [['NODE_ID', 'BUS_SUFFIX'], 'integer'],
//          [['BUS_NAME', 'BUS_DESC'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'BUS_ID' => 'Bus  ID',
            'NODE_ID' => 'Node  ID',
            'BUS_NAME' => 'Bus  Name',
            'BUS_DESC' => 'Bus  Desc',
            'BUS_SUFFIX' => 'Bus  Suffix',
        ];
    }
	
	/*根据条件查询最大的栏目值*/
	public static function findSMaxVal($condition){
		$max_value =  self::find()->where($condition)->max('BUS_SUFFIX');
		return !empty($max_value) ? intval($max_value) : 0;
	}
	
	/*根据条件查询数据*/
	public static function findAllByCondition($condition){
		return self::find()->where($condition)->orderby('BUS_SUFFIX')->all();
	}
	
	/*保存业务表对应的环节*/
	public static function saveBusNodeTable($nodeID,$busDesc){
		$suffix = self::findSMaxVal(['NODE_ID'=>$nodeID]);
		$suffix++;
		$bus_name = $nodeID . '_' . $suffix;
		$self = new self();
		$self->NODE_ID = $nodeID;
		$self->BUS_NAME = $bus_name;
		$self->BUS_SUFFIX = $suffix;
		$self->BUS_DESC = $busDesc;
		
		return $self->save();
	}
}
