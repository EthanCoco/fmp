<?php

namespace app\modules\formdsn\models;

use Yii;

/**
 * This is the model class for table "FMP_ASSIST".
 *
 * @property integer $ASSIST_ID
 */
class FMPASSIST extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'FMP_ASSIST';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//          [['ASSIST_ID'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ASSIST_ID' => 'Assist  ID',
        ];
    }
}
