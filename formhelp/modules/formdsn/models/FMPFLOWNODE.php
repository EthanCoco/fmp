<?php

namespace app\modules\formdsn\models;

use Yii;

/**
 * This is the model class for table "fmp_flow_node".
 *
 * @property string $NODE_ID
 * @property string $FLOW_ID
 * @property string $NODE_NAME
 * @property integer $NODE_ORDER
 */
class FMPFLOWNODE extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fmp_flow_node';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//          [['FLOW_ID', 'NODE_NAME'], 'required'],
//          [['FLOW_ID', 'NODE_ORDER'], 'integer'],
//          [['NODE_NAME'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'NODE_ID' => 'Node  ID',
            'FLOW_ID' => 'Flow  ID',
            'NODE_NAME' => 'Node  Name',
            'NODE_ORDER' => 'Node  Order',
        ];
    }
	
	/*获取流程节点信息*/
	public static function getNodeInfo($flowID){
		$datas = self::find()->where(['FLOW_ID'=>$flowID])->asArray()->all();
		$jsonData = [];
		if(!empty($datas)){
			foreach($datas as $data){
				$jsonData[] = [
					'id' => $data['NODE_ID'],
					'text' => $data['NODE_ID'] . '=' . $data['NODE_NAME']
				];
			}
		}
		
		return $jsonData;
	}
}
