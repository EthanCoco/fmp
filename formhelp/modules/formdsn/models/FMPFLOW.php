<?php

namespace app\modules\formdsn\models;

use Yii;

/**
 * This is the model class for table "FMP_FLOW".
 *
 * @property integer $FLOW_ID
 * @property integer $FLOW_DIRID
 * @property string $FLOW_NAME
 * @property string $FLOW_DESCRIPTION
 * @property integer $FLOW_STATUS
 */
class FMPFLOW extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'FMP_FLOW';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//          [['FLOW_DIRID'], 'required'],
//          [['FLOW_DIRID', 'FLOW_STATUS'], 'integer'],
//          [['FLOW_NAME'], 'string', 'max' => 64],
//          [['FLOW_DESCRIPTION'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'FLOW_ID' => 'Flow  ID',
            'FLOW_DIRID' => 'Flow  Dirid',
            'FLOW_NAME' => 'Flow  Name',
            'FLOW_DESCRIPTION' => 'Flow  Description',
            'FLOW_STATUS' => 'Flow  Status',
        ];
    }
	
	/*根据ID获取信息*/
	public static function findByID($flowID,$fields='*'){
		return self::find()->select($fields)->where(['FLOW_ID'=>$flowID])->one();
	}
}
