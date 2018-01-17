<?php

namespace app\modules\formdsn\models;

use Yii;
use app\modules\formdsn\models\FMPFLOW;
use app\modules\formdsn\models\FMPBUSNODETABLE;

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
	
	/*获取节点树*/
	public static function getFlowNodeTree($flowID){
		$flow_infos = FMPFLOW::findByID($flowID,['FLOW_NAME']);
		
		$node_datas = self::find()->where(['FLOW_ID'=>$flowID])->asArray()->orderby('NODE_ORDER')->all();
		
		$resultInfo = [];
		
		if(!empty($node_datas)){
			foreach($node_datas as $info){
				$resultInfo[] = [
					'id' => $info['NODE_ID'],
					'name' => $info['NODE_NAME'],
					'pId' => 0,
					'isChild' => 1,
					'isParent' => 'false'
				];
			}		
		}
		
        $resultInfo[] = ['id' => '0', 'name' => $flow_infos['FLOW_NAME'], 'pId' => '-1', 'isParent' => 'true', 'isChild'=>0];
		
		return $resultInfo;
	}
	
	/*获取节点树 带业务表子节点*/
	public static function getFlowNodeTreeTable($flowID){
		$flow_infos = FMPFLOW::findByID($flowID,['FLOW_NAME']);
		
		$node_datas = self::find()->where(['FLOW_ID'=>$flowID])->asArray()->orderby('NODE_ORDER')->all();
		
		$resultInfo = [];
		
		if(!empty($node_datas)){
			foreach($node_datas as $info){
				$resultInfo[] = [
					'id' => $info['NODE_ID'],
					'name' => $info['NODE_NAME'],
					'pId' => 0,
					'isChild' => 0,
					'isNode' => 1,
					'isRmenu' => 1,
					'isParent' => 'true'
				];
				
				$tempData = FMPBUSNODETABLE::findAllByCondition(['NODE_ID'=>$info['NODE_ID']]);
				if(!empty($tempData)){
					foreach($tempData as $temp){
						$resultInfo[] = [
							'id' => $temp['BUS_NAME'],
							'name' => $temp['BUS_DESC'],
							'pId' => $info['NODE_ID'],
							'isChild' => 1,
							'isNode' => 2,
							'isRmenu' => 2,
							'isParent' => 'false'
						];
					}
				}
				
				
			}		
		}
		
        $resultInfo[] = ['id' => '0', 'name' => $flow_infos['FLOW_NAME'], 'pId' => '-1', 'isParent' => 'true', 'isChild'=>0,'isNode'=>0,'isRmenu' => 0];
		
		return $resultInfo;
	}
}
