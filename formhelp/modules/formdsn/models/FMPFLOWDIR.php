<?php

namespace app\modules\formdsn\models;

use Yii;

use app\modules\formdsn\models\FMPFLOW;
/**
 * This is the model class for table "FMP_FLOWDIR".
 *
 * @property integer $FLOW_DIRID
 * @property string $FLOW_DIRNAME
 * @property string $FLOW_DIRTIME
 */
class FMPFLOWDIR extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'FMP_FLOWDIR';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//          [['FLOW_DIRNAME'], 'required'],
//          [['FLOW_DIRTIME'], 'safe'],
//          [['FLOW_DIRNAME'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'FLOW_DIRID' => 'Flow  Dirid',
            'FLOW_DIRNAME' => 'Flow  Dirname',
            'FLOW_DIRTIME' => 'Flow  Dirtime',
        ];
    }
	
	
	/*生成流程树*/
	public static function getFlowTree(){
		$flowdir_info = self::find()->asArray()->all();
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
		
		return $resultInfo;
	}
	
	
}
