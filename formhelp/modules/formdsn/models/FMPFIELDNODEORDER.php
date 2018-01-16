<?php

namespace app\modules\formdsn\models;

use Yii;

/**
 * This is the model class for table "fmp_field_node_order".
 *
 * @property string $ORDER_ID
 * @property string $FLOW_ID
 * @property string $NODE_ID
 * @property string $ORDER_VAL
 */
class FMPFIELDNODEORDER extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fmp_field_node_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//          [['FLOW_ID', 'NODE_ID'], 'required'],
//          [['FLOW_ID', 'NODE_ID'], 'integer'],
//          [['ORDER_VAL'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ORDER_ID' => 'Order  ID',
            'FLOW_ID' => 'Flow  ID',
            'NODE_ID' => 'Node  ID',
            'ORDER_VAL' => 'Order  Val',
        ];
    }
	
	/*保存环节排序信息*/
	public static function saveNodeOrder($flowID,$nodeID,$real_data){
		self::deleteAll(['FLOW_ID'=>$flowID,'NODE_ID'=>$nodeID]);
		
		$orderVal = '';
		
		if(!empty($real_data)){
			foreach($real_data as $data){
				$orderVal .= $data->FIELD_ID . ',';
			}
			
			$orderVal = rtrim($orderVal,',');
		}
		
		$self = new self();
		$self->FLOW_ID = $flowID;
		$self->NODE_ID = $nodeID;
		$self->ORDER_VAL = $orderVal;
		return $self->save();
	}
}
