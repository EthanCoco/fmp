<?php
namespace app\controllers;
use yii\web\Controller;
use yii\helpers\Html;
use Yii;

class IndexController extends BaseController{
	/*首页*/
	public function actionIndex(){
		return $this->render('index');
	}

	/*上传图片*/
	public function actionUpload(){
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		date_default_timezone_set('PRC');
		$file = $_FILES['file'];
		var_dump($file);exit;
		$type = strtolower($_FILES['file']["type"]);
		
		$timeNow = date('Y-m-d H:i:s',time());
		
		$timeNowMonth = date('Ym',time());
		
		$tmpfile = time();
		$fileName = $tmpfile.'.'.explode("/", $type)[1];
		if(!in_array($type, ['image/jpg','image/gif','image/png','image/jpeg'])){
			return ['code'=>'1','msg'=>'图片格式不正确','data'=>['src'=>'']];
		}
		if($_FILES['file']['size'] > 2*1024*1024){
			return ['code'=>'1','msg'=>'图片大小不能大于2M','data'=>['src'=>'']];
		}
		$createDir = './uploadfile/image/'.$timeNowMonth;
		$this->mkdirs($createDir);
		move_uploaded_file($_FILES['file']['tmp_name'], $createDir."/".$fileName);
		$resultFile = $createDir."/".$fileName;
		return ['code'=>'0','msg'=>'图片大小不能大于2M','data'=>['src'=>$createDir."/".$fileName]];
	}
	
	function mkdirs($dir, $mode = 0777){
	    if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
	    if (!self::mkdirs(dirname($dir), $mode)) return FALSE;
	    return @mkdir($dir, $mode);
	} 
	
//	public function actionTest(){
		/*添加数据*/
//		$tableName = FlowJob::tableName(2);
//		$db = Yii::$app->db->createCommand();
//		$db	->	insert($tableName,[
//				 	'a1' =>'a1',
//				 	'a2' =>'a2',
//				 	'a3' =>'a3',
//				 	'a4' =>'a4',
//				 	'a5' =>'a5',
//			 	])
//		 	->execute();
		/*修改数据*/	
//		$db	->	update($tableName, [
//					'a1' => "12344343545"
//				], ['id'=>1])
//				->execute();
		
		/*删除数据*/
//		$db	->	delete($tableName,['id'=>1])->execute();

//		$query = new yii\db\Query();
//		$info = $query	->from($tableName)
//						->all();
//		var_dump($info);		
//	}
}
