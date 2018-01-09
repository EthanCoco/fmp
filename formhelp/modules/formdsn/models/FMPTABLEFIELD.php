<?php

namespace app\modules\formdsn\models;

use Yii;

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
}
