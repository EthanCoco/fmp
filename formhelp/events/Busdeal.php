<?php
namespace app\events;
use app\models\Share;
use Yii;

/*事件执行*/
class Busdeal extends \yii\base\Component{
	public function create_bus_table_deal($event){
		Yii::$app->db->createCommand(Share::CreateBusTable($event->data))->execute();
	}
}
