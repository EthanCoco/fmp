<?php

namespace app\modules\formdsn\models;

use Yii;

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
}
