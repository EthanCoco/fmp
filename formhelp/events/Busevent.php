<?php
namespace app\events;

/*定义事件信息触发*/

class Busevent extends \yii\base\Component{
	const CREATE_BUS_TABLE = 'create_bus_table';
	public function create_bus_table(){
		$this->trigger(self::CREATE_BUS_TABLE);
	}
}
