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
            'FLOW_TABLE_DESC'	=> 'Flow Table Desc'
        ];
    }
}
