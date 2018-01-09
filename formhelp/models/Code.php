<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "code".
 *
 * @property integer $id
 * @property string $codeID
 * @property string $codeTypeID
 * @property string $codeName
 * @property integer $codeOrder
 * @property integer $codeStatus
 */
class Code extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'code';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['codeID'], 'required'],
            [['codeOrder', 'codeStatus','isLeaf'], 'integer'],
            [['codeID', 'codeTypeID', 'codeName','codePid'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codeID' => 'Code ID',
            'codeTypeID' => 'Code Type ID',
            'codeName' => 'Code Name',
            'codeOrder' => 'Code Order',
            'codeStatus' => 'Code Status',
            'codePid'	=>	'Code Pid',
            'isLeaf'	=>	'Is Leaf',
        ];
    }
	
	public static function getCodeSel($typeData = []){
    	$codeInfo = [];
    	foreach($typeData as $type){
    		if($type[1] == 1){
	    		$code = self::find()->where(['codeTypeID'=>$type[0]])->select(['codeID','codeName'])->asArray()->all();
    		}else{
	    		$code = self::find()->where(['AND',['codeTypeID'=>$type[0],'isLeaf'=>$type[1]],['not',['codePiD'=>-1]]])->select(['codeID','codeName'])->asArray()->all();
    		}
    		$codeInfo[$type[0]] = $code;
    	}
    	return $codeInfo;
    }
}
