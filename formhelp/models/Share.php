<?php

namespace app\models;

use Yii;
use yii\base\Model;

class Share extends Model
{
	public static function comErrors($errors){
		$str = '';
		if(!empty($errors)){
			$str = '错误提示：<br/>';
			foreach($errors as $key=>$val){
				$str .= $val.'<br/>';
			}
		}
		return $str; 
	}
	
	public static function deleteHtml($str){
		$str = preg_replace( "@<script(.*?)</script>@is", "", $str ); 
		$str = preg_replace( "@<iframe(.*?)</iframe>@is", "", $str ); 
		$str = preg_replace( "@<style(.*?)</style>@is", "", $str ); 
		$str = preg_replace( "@<(.*?)>@is", "", $str ); 

		$str= htmlspecialchars_decode($str);
		return $str; 
	}	
	
	public static function codeValue($codeArr = [],$data = ''){
        $codeNameArr = [];//代码名称数组
        foreach($codeArr as $code){
            $val = $data[$code[0]];
			$val_arr = explode("_", $val);
			$val_len = count($val_arr);
			if($val_len == 1){
				$codeFind = Code::find()->where(['codeID'=>$val,'codeTypeID'=>$code[1],'codeStatus'=>1])->one();
				if(!empty($codeFind)){
	                $codeNameArr[$code[0]] = $codeFind->codeName;
	            }else{
	            	$codeNameArr[$code[0]] = '';
	            }
			}else{
				$val_name = '';
				for($i = 0; $i < $val_len; $i++ ){
					$codeFind = Code::find()->where(['codeID'=>$val_arr[$i],'codeTypeID'=>$code[1],'codeStatus'=>1])->one();
					if(!empty($codeFind)){
						if($i == ($val_len -1) ){
							$val_name .= $codeFind->codeName;
						}else{
							$val_name .= $codeFind->codeName.' ';
						}
		                
		            }
				}
				$codeNameArr[$code[0]] = $val_name;
			}
			
        }
        return $codeNameArr;
    }
	
	public static function mkdirs($dir, $mode = 0777){
	    if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
	    if (!self::mkdirs(dirname($dir), $mode)) return FALSE;
	    return @mkdir($dir, $mode);
	}
	
	public static function codeValue1111($codeArr = [],$data = ''){
        $codeNameArr = [];//代码名称数组
        foreach($codeArr as $code){
            $codeVal = $data[$code[0]];
            $codeFind = Code::find()->where(['codeID'=>$codeVal,'codeTypeID'=>$code[1],'codeStatus'=>1])->one();
            if(!empty($codeFind)){
                $codeNameArr[$code[0]] = $codeFind->codeName;
            }else{
                  $codeNameArr[$code[0]] = '';
            }
        }
        return $codeNameArr;
    }
	
	/**
	 * 请求接口返回内容
	 * @param  string $params [请求的参数]
	 * @param  int $ipost [是否采用POST形式]
	 * @return  string
	 */
	public static function juhecurl($params=false,$ispost=0){
	    //聚合接口（详情请至聚合官网了解详情）	
	    $url = 'http://v.juhe.cn/sms/send';
		
	    $httpInfo = array();
	    $ch = curl_init();
	    curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
	    curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
	    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 30 );
	    curl_setopt( $ch, CURLOPT_TIMEOUT , 30);
	    curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
	    if( $ispost )
	    {
	        curl_setopt( $ch , CURLOPT_POST , true );
	        curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
	        curl_setopt( $ch , CURLOPT_URL , $url );
	    }
	    else
	    {
	        if($params){
	            curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
	        }else{
	            curl_setopt( $ch , CURLOPT_URL , $url);
	        }
	    }
	    $response = curl_exec( $ch );
	    if ($response === FALSE) {
	        return false;
	    }
	    $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
	    $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
	    curl_close( $ch );
	    return $response;
	}
	
	public static function object_to_array($obj){
	    $obj = (array)$obj;
	    foreach ($obj as $k => $v){
	        if (gettype($v) == 'resource'){
	            return;
	        }
	        if (gettype($v) == 'object' || gettype($v) == 'array'){
	            $obj[$k] = (array)self::object_to_array($v);
	        }
	    }
	    return $obj;
	}
	
	public static function ExcelStyleArrayInfoSet($type){
		$resultStyle = [];
		switch($type){
			case 1 : 
					//Excel表头样式
					$resultStyle = array(
								            'font' => array ('bold' => true),
								            'alignment' => array(
								            						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
								            						'vertical'=>\PHPExcel_Style_Alignment::VERTICAL_CENTER 
																),
								            'borders' =>array(
								            					'top' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
						            									'left' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
						            									'right' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
						            									'bottom' => array('style' => \PHPExcel_Style_Border::BORDER_THIN)
															)
										);
			break;
			case 2 :
					//Excel表体样式 文字靠左
					$resultStyle = array(
								            'font' => array ('bold' => false),
								            'alignment' => array(
								            						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
								            						'vertical'=>\PHPExcel_Style_Alignment::VERTICAL_CENTER 
																),
								            'borders' =>array(
								            					'top' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
								            					'left' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
								            					'right' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
								            					'bottom' => array('style' => \PHPExcel_Style_Border::BORDER_THIN)
															)
										); 	
			break;
			case 3 : 
					//Excel表体样式
					$resultStyle = array(
								            'borders' =>array(
								            					'top' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
						            									'left' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
						            									'right' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
						            									'bottom' => array('style' => \PHPExcel_Style_Border::BORDER_THIN)
															)
										);
			break;
			default:
			break;
		}
		return $resultStyle;
	}
	
	public static function getKeyInfo($key){
		$result = [];
		switch($key){
			case 'flow3' : 
				$result = [
					'tempExcel'	=>	'../web/mbfile/rczp_zgsc_flow3.xls',
					'keys' =>[
						[
							'key'=>[
									'perIndex','perName','perIDCard','perGender','perBirth','perJob','perPhone','perNation','perOrigin',
									'perPolitica','perWorkPlace','perMarried','perHeight','perWeight','perUniversity','perDegree','perMajor',
									'perEducation','perForeignLang','perComputerLevel','perEduPlace','perEmePhone','perEmail','perPostCode',
									'perAddr','perMark','perPub','perStatus','perReason','perCheckTime','perReResult1','perReGiveup1','perReTime1'
								],
							'num'=>3,
							'index'=>0,
							'sheetName'=>'人员基本信息',
						]
					],
				];
			break;
			case 'flow3_print' :
				$result = [
					'tempExcel'	=>	'../web/mbfile/rczp_zgsc_flow3_print.xls',
					'keys' => [
						'index'=>0,
						'sheetName'=>'人员基本信息',
						'zipName'=>'考生报名表',
					],
				];				
			break;	
			case 'flow4_step2' :
				 $result = [
					'tempExcel'	=>	'../web/mbfile/flow4_step2.xls',
					'keys' =>[
						[
							'key'=>[
									'id','exmName','exmAttr','exmCom','exmType','exmPost','exmPhone','exmCertNo','exmTime'
								],
							'num'=>2,
							'index'=>0,
							'sheetName'=>'考官信息',
						]
					],
				];
			break;
			case 'flow4_step3_mb' :
				 $result = [
					'tempExcel'	=>	'../web/mbfile/flow4_step3_downloadmb.xls',
					'keys' =>[
						[
							'key'=>[
									'id','gstGroup','gstStartEnd','exmType','exmName'
								],
							'num'=>3,
							'index'=>0,
							'sheetName'=>'考官签到表信息',
						]
					],
				];
			break;
			case 'flow4_step3_export' :
				 $result = [
					'tempExcel'	=>	'../web/mbfile/flow4_step3_export.xls',
					'keys' =>[
						[
							'key'=>[
									'id','gstStartEnd','gstGroup','gstItvPlace','exmNames'
								],
							'num'=>2,
							'index'=>0,
							'sheetName'=>'考官签到表信息',
						]
					],
				];
			break;
			case 'flow4_step4_mb' :
				 $result = [
					'tempExcel'	=>	'../web/mbfile/flow4_step4_downloadmb.xls',
					'keys' =>[
						[
							'key'=>[
									'id','perGroupSet','gstStartEnd','perName','perGender','perTicketNo','perPhone','perIDCard','perJob'
								],
							'num'=>3,
							'index'=>0,
							'sheetName'=>'考生签到表信息',
						]
					],
				];
			break;
			case 'flow4_step4_export' :
				 $result = [
					'tempExcel'	=>	'../web/mbfile/flow4_step4_export.xls',
					'keys' =>[
						[
							'key'=>[
									'id','perIndex','perName','perIDCard','perGender','perBirth','perJob','perPhone','perTicketNo'
									,'perGroupSet','gstStartEnd','gstItvPlace','perReResult1','perReGiveup1','perReTime1','perReResult2','perReGiveup2','perReTime2'
								],
							'num'=>3,
							'index'=>0,
							'sheetName'=>'考生分组信息',
						]
					],
				];
			break;
			case 'flow4_step5_mb' :
				 $result = [
					'tempExcel'	=>	'../web/mbfile/flow4_step5_downloadmb.xls',
					'keys' =>[
						[
							'key'=>[
									'id','perIndex', 'perName','perGender','perIDCard','perJob','perPhone','perTicketNo','perGroupSet','perViewScore','perPenScore'
								],
							'num'=>2,
							'index'=>0,
							'sheetName'=>'考生考试导入模板信息',
						]
					],
				];
			break;
			case 'flow4_step5_export' :
				 $result = [
					'tempExcel'	=>	'../web/mbfile/flow4_step5_export.xls',
					'keys' =>[
						[
							'key'=>[
									'perIndex', 'perName','perIDCard','perGender','perBirth','perJob','perPhone','perTicketNo','perGroupSet','perViewScore','perPenScore','perViewPenScore','perGradePub','perExamResult','perRead3','perReResult3','perReGiveup3','perReTime3'
								],
							'num'=>3,
							'index'=>0,
							'sheetName'=>'考试结果信息',
						]
					],
				];
			break;
			case 'flow5_step1_mb' :
				 $result = [
					'tempExcel'	=>	'../web/mbfile/flow5_step1_downloadmb.xls',
					'keys' =>[
						[
							'key'=>[
									'id','perName','perGender','perIDCard','perPhone','medPlace','medStartEnd'
								],
							'num'=>3,
							'index'=>0,
							'sheetName'=>'考生体检签到表信息',
						]
					],
				];
			break;
			case 'flow5_step1_export' :
				 $result = [
					'tempExcel'	=>	'../web/mbfile/flow5_step1_export.xls',
					'keys' =>[
						[
							'key'=>[
									'perIndex','perName','perIDCard','perGender','perBirth','perJob','perPhone','medPlace','medStartEnd',
									'perRead4','perReResult3','perReGiveup3','perReTime3','perReResult4','perReGiveup4','perReTime4'
								],
							'num'=>3,
							'index'=>0,
							'sheetName'=>'考生体检安排信息',
						]
					],
				];
			break;
			case 'flow5_step2_export' :
				 $result = [
					'tempExcel'	=>	'../web/mbfile/flow5_step2_export.xls',
					'keys' =>[
						[
							'key'=>[
									'perIndex', 'perName','perIDCard','perGender','perBirth','perJob','perPhone',
									'perMedCheck1','perMedCheck2','perRead5','perReResult5','perReGiveup5','perReTime5'
								],
							'num'=>3,
							'index'=>0,
							'sheetName'=>'考生体检信息',
						]
					],
				];
			break;
			case 'flow5_step2_mb' :
				 $result = [
					'tempExcel'	=>	'../web/mbfile/flow5_step2_downloadmb.xls',
					'keys' =>[
						[
							'key'=>[
									'id','perIndex', 'perName','perGender','perIDCard','perJob','perPhone','perMedCheck1','perMedCheck2'
								],
							'num'=>2,
							'index'=>0,
							'sheetName'=>'考生体检结果导入模板信息',
						]
					],
				];
			break;
			case 'flow6_export' :
				 $result = [
					'tempExcel'	=>	'../web/mbfile/flow6_export.xls',
					'keys' =>[
						[
							'key'=>[
									'perIndex', 'perName','perIDCard',
									'perGender','perBirth','perJob','perPhone',
									'perCarefulStatus','perCarefulReson','perPub6',
									'perRead6','perReResult5','perReGiveup5','perReTime5','perCarefulMark'
								],
							'num'=>3,
							'index'=>0,
							'sheetName'=>'考生政审信息',
						]
					],
				];
			break;
			case 'flow6_mb' :
				 $result = [
					'tempExcel'	=>	'../web/mbfile/flow6_downloadmb.xls',
					'keys' =>[
						[
							'key'=>[
									'id','perIndex', 'perName','perGender','perIDCard','perJob','perPhone'
								],
							'num'=>2,
							'index'=>0,
							'sheetName'=>'考生政审结果导入模板信息',
						]
					],
				];
			break;
			default:break;
		}

		return $result;
	}
	
	public static function  exportCommonExcel($data = []){
		@ini_set('memory_limit', '2048M');
		set_time_limit(0);
		error_reporting(E_ALL);
		date_default_timezone_set('PRC');
		$key = $data['key'];
		$fileInfo = $data['fileInfo'];
		$fileName = $fileInfo['fileName'].date('Y-m-d',time()).time();
		//exit(print($fileName) );
		$excelInfo = self::getKeyInfo($key);
		
		unset($data['key']);
		unset($data['fileInfo']);
		ksort($data);
		$objPHPExcel = \PHPExcel_IOFactory::createReader("Excel5")->load($excelInfo['tempExcel']);
		$index = 0;
		foreach($excelInfo['keys'] as $v){
			$objPHPExcel -> getSheet($v['index']) -> setTitle($v['sheetName']);
			$dataInfos = $data['sheet'.$index]['data'];
			$num = $v['num'];
			$keys = $v['key'];
			foreach($dataInfos as $info){
				$column = count($keys);
				$temp = 0;
				for($n = 0; $n < $column; $n++){
					if($temp == $column){
						break;
					}else{
						$pcoordinate = \PHPExcel_Cell::stringFromColumnIndex($n).''.$num;
						if($keys[$temp] == 'id'){
							$objPHPExcel->setActiveSheetIndex($v['index'])->setCellValue($pcoordinate,  ($num-1) );
						}else{
							$objPHPExcel->setActiveSheetIndex($v['index'])->setCellValue($pcoordinate, ' ' . $info[$keys[$temp]] . ' ');
						}
			            $temp++;
					}
				}
				$num++;
			}
			$index++;
		}

		ob_end_clean();
		$fileName = iconv("utf-8","gb2312",$fileName);
		header ( 'Content-Type: application/vnd.ms-excel' );
		header ( 'Content-Disposition: attachment;filename="'.$fileName.'.xls"'); 
		header ( 'Cache-Control: max-age=0' );
		$objWriter = \PHPExcel_IOFactory::createWriter ($objPHPExcel,'Excel5'); //在内存中准备一个excel2003文件
		$objWriter->save ( 'php://output' );
		exit;
	}
	
	public static function getCodeInfo($codeType = []){
		$len = count($codeType);
		if($len == 1){
			$cond = ['codeTypeID'=>$codeType[0]];
		}else{
			$cond = ['in', 'codeTypeID', $codeType];
		}
		
		$infos = Code::find()->select(['codeID','codeName'])
							 ->where(['codeStatus'=>1])
							 ->andWhere($cond)
							 ->orderby('codeTypeID asc,codeOrder asc')->asArray()->all();
		
		return $infos;
	}
	
	public static function MainTableName($rec){
        return 'flow_job_'.$rec;
    }
	
	public static function SetTableNames($rec){
		return ['flow_job_set_edu_'.$rec,'flow_job_set_fam_'.$rec,'flow_job_set_work_'.$rec];
	}
	
	public static function SetTableName($rec,$set){
        return 'flow_job_set_'.$set.'_'.$rec;
    }
	
	public static function CreateBusTable($busID){
		$sql = "
				CREATE TABLE `flow_job_".$busID."` (
					  `perID` int(11) NOT NULL AUTO_INCREMENT COMMENT '人员ID',
					  `perName` varchar(255) NOT NULL COMMENT '姓名',
					  `perIDCard` varchar(32) NOT NULL COMMENT '身份证号',
					  `perPhone` varchar(11) NOT NULL COMMENT '手机号码',
					  `perPhoto` varchar(255) NOT NULL COMMENT '照片',
					  `perGender` int(1) DEFAULT NULL COMMENT '性别',
					  `perNation` varchar(64) NOT NULL COMMENT '民族',
					  `perOrigin` varchar(128) NOT NULL COMMENT '籍贯',
					  `perPolitica` varchar(64) NOT NULL COMMENT '政治面貌',
					  `perWorkPlace` varchar(255) NOT NULL COMMENT '现工作单位',
					  `perMarried` varchar(64) NOT NULL COMMENT '婚姻状况',
					  `perBirth` varchar(128) NULL DEFAULT NULL COMMENT '出生年月',
					  `perHeight` int(11) DEFAULT NULL COMMENT '身高',
					  `perWeight` decimal(8,2) DEFAULT NULL COMMENT '体重',
					  `perUniversity` varchar(255) DEFAULT NULL COMMENT '毕业院校',
					  `perDegree` varchar(128) DEFAULT NULL COMMENT '学位',
					  `perMajor` varchar(128) DEFAULT NULL COMMENT '专业',
					  `perEducation` varchar(64) DEFAULT NULL COMMENT '学历',
					  `perForeignLang` varchar(64) DEFAULT NULL COMMENT '外语水平',
					  `perComputerLevel` varchar(64) DEFAULT NULL COMMENT '计算机水平',
					  `perEduPlace` varchar(128) DEFAULT NULL COMMENT '毕业生生源地',
					  `perEmePhone` varchar(11) DEFAULT NULL COMMENT '紧急人联系电话',
					  `perEmail` varchar(128) DEFAULT NULL COMMENT '电子邮箱',
					  `perPostCode` varchar(32) DEFAULT NULL COMMENT '邮政编码',
					  `perAddr` varchar(255) DEFAULT NULL COMMENT '联系地址',
					  `perMark` varchar(255) DEFAULT NULL COMMENT '备注信息',
					  `perIndex` varchar(255) NOT NULL COMMENT '报名序号',
					  `perJob` varchar(64) NOT NULL COMMENT '应聘岗位性质',
					  
					  `perBack` int(1) NOT NULL DEFAULT '0' COMMENT '报名主动撤回次数（最多三次，三次后不允许撤回）',
					  
					  `perTicketNo` varchar(128) NOT NULL COMMENT '准考证号',
					  `perGroupSet` varchar(20) NOT NULL COMMENT '所属组别',
					  `perStatus` int(1) NOT NULL DEFAULT '0' COMMENT '状态（0=待报，1=待审，2=审核通过，3=审核不通过）',
					  `perCheckTime` timestamp NULL DEFAULT NULL COMMENT '审核时间',
					  `perReason` varchar(255) DEFAULT NULL COMMENT '审核不通过原因',
					  
					  `perPub`  int(1) NOT NULL DEFAULT '0' COMMENT '资格审查公示结果（0=未公示，1=已公示）',
					  `perReResult1` varchar(64)  NOT NULL DEFAULT '03' COMMENT '资格审查反馈结果(是否参加考试)【01=确定参加，02=放弃参加，03=未反馈】',
					  `perReGiveup1` varchar(255) NOT NULL COMMENT '资格审查反馈原因(是否参加考试反馈原因)',
					  `perReTime1` timestamp NULL DEFAULT NULL COMMENT '资格审查反馈时间(是否参加考试反馈时间)',
					  
					  `perPub2`  int(1) NOT NULL DEFAULT '0' COMMENT '考生分组及通知(是否通知【公示】)（0=未通知，1=已通知）',
					  `perRead2` int(1) NOT NULL DEFAULT '1' COMMENT '考生分组及通知（通知阅读情况【1=未读，2=已读】）',
					  `perReResult2` varchar(64)  NOT NULL DEFAULT '03' COMMENT '考生分组及通知反馈结果【01=确定参加，02=放弃参加，03=未反馈】',
					  `perReGiveup2` varchar(255) NOT NULL COMMENT '考生分组及通知反馈原因',
					  `perReTime2` timestamp NULL DEFAULT NULL COMMENT '考生分组及通知反馈时间',
					  
					  `perViewScore` varchar(64) DEFAULT NULL COMMENT '面试成绩',
					  `perPenScore` varchar(64) DEFAULT NULL COMMENT '笔试成绩',
					  `perExamResult`  int(1) NOT NULL DEFAULT '0' COMMENT '考试结果【0=待处理，1=通过，2=不通过】',
					  
					  `perGradePub`  int(1) NOT NULL DEFAULT '0' COMMENT '考试结果录入及公示(成绩是否公布)（0=未公布，1=已公布）',
					  `perPub3`  int(1) NOT NULL DEFAULT '0' COMMENT '考试结果录入及公示(考试结果是否公示)（0=未公示，1=已公示）',
					  
					  `perRead3` int(1) NOT NULL DEFAULT '1' COMMENT '考试结果录入及公示（通知阅读情况【1=未读，2=已读】）',
					  `perReResult3` varchar(64)  NOT NULL DEFAULT '03' COMMENT '考试结果录入及公示(是否参加体检)【01=确定参加，02=放弃参加，03=未反馈】',
					  `perReGiveup3` varchar(255) NOT NULL COMMENT '考生分组及通知反馈原因(是否参加体检反馈原因)',
					  `perReTime3` timestamp NULL DEFAULT NULL COMMENT '考生分组及通知反馈时间(是否参加体检反馈时间)',
					  
					  `perPub4`  int(1) NOT NULL DEFAULT '0' COMMENT '体检安排公示（0=未公示，1=已公示）',
					  `perRead4` int(1) NOT NULL DEFAULT '1' COMMENT '体检安排（通知阅读情况【1=未读，2=已读】）',
					  `perReResult4` varchar(64)  NOT NULL DEFAULT '03' COMMENT '体检安排(是否参加体检)【01=确定参加，02=放弃参加，03=未反馈】',
					  `perReGiveup4` varchar(255) NOT NULL COMMENT '体检安排(是否参加体检反馈原因)',
					  `perReTime4` timestamp NULL DEFAULT NULL COMMENT '体检安排(是否参加体检反馈时间)',
					  
					  `perMedCheck1`  int(1) NOT NULL DEFAULT '0' COMMENT '体检结果（0=无数据，1=合格，2=不合格）',
					  `perMedCheck2`  int(1) NOT NULL DEFAULT '0' COMMENT '复查结果（0=无数据，1=合格，2=不合格）',
					  `perMedCheck3`  int(1)   DEFAULT NULL COMMENT '体检最终结果（1=通过，2=不通过）',
					  `perMedMark`  varchar(255) NOT NULL COMMENT '体检信息备注',
					  
					  `perPub5`  int(1) NOT NULL DEFAULT '0' COMMENT '体检结果录入公示（0=未公示，1=已公示）',
					  `perRead5` int(1) NOT NULL DEFAULT '1' COMMENT '体检结果录入（通知阅读情况【1=未读，2=已读】）',
					  `perReResult5` varchar(64)  NOT NULL DEFAULT '03' COMMENT '体检结果录入(是否参加政审)【01=确定参加，02=放弃参加，03=未反馈】',
					  `perReGiveup5` varchar(255) NOT NULL COMMENT '体检结果录入(是否参加政审反馈原因)',
					  `perReTime5` timestamp NULL DEFAULT NULL COMMENT '体检结果录入(是否参加政审反馈时间)',
					  
					  `perCarefulMark`  varchar(255) NOT NULL COMMENT '政审备注信息',
					  `perCarefulStatus`  int(1) NOT NULL DEFAULT '0' COMMENT '政审结果（0=待审，1=通过，2=不通过）',
					  `perCarefulReson` varchar(255) DEFAULT NULL COMMENT '政审不通过原因',
					  `perCarefulTime` timestamp NULL DEFAULT NULL COMMENT '政审审核时间',
					  `perPub6`  int(1) NOT NULL DEFAULT '0' COMMENT '政审是否公示（0=未公示，1=已公示）',
					  `perRead6` int(1) NOT NULL DEFAULT '1' COMMENT '政审通知阅读情况【1=未读，2=已读】',
					  
					  PRIMARY KEY (`perID`)
					) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
					
					CREATE TABLE `flow_job_set_edu_".$busID."` (
					  `eduID` int(11) NOT NULL AUTO_INCREMENT COMMENT '教育经历ID',
					  `perID` int(11) NOT NULL COMMENT '人员ID',
					  `eduStart` timestamp NULL DEFAULT NULL COMMENT '起始时间',
					  `eduEnd` timestamp NULL DEFAULT NULL COMMENT '终止时间',
					  `eduSchool` varchar(255) NOT NULL COMMENT '在何学校',
					  `eduMajor` varchar(128) DEFAULT NULL,
					  `eduPost` varchar(255) DEFAULT NULL COMMENT '任职职务',
					  `eduBurseHonorary` text COMMENT '奖学金及荣誉称号',
					  PRIMARY KEY (`eduID`)
					) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
					
					CREATE TABLE `flow_job_set_fam_".$busID."` (
					  `famID` int(11) NOT NULL AUTO_INCREMENT COMMENT '家庭成员ID',
					  `perID` int(11) NOT NULL COMMENT '人员ID',
					  `famRelation` varchar(64) NOT NULL COMMENT '成员关系',
					  `famName` varchar(64) NOT NULL COMMENT '成员姓名',
					  `famCom` varchar(64) DEFAULT NULL COMMENT '所在工作单位',
					  `famPost` varchar(64) DEFAULT NULL COMMENT '任职岗位',
					  PRIMARY KEY (`famID`)
					) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
					
					CREATE TABLE `flow_job_set_work_".$busID."` (
						  `wkID` int(11) NOT NULL AUTO_INCREMENT COMMENT '工作经历ID',
						  `perID` int(11) NOT NULL COMMENT '人员ID',
						  `wkStart` timestamp NULL DEFAULT NULL COMMENT '起始时间',
						  `wkEnd` timestamp NULL DEFAULT NULL COMMENT '截止时间',
						  `wkPost` varchar(255) NOT NULL COMMENT '所在岗位',
						  `wkCom` varchar(255) NOT NULL COMMENT '所在单位',
						  `wkInfo` text NOT NULL COMMENT '工作简述',
						  PRIMARY KEY (`wkID`)
						) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
				";
		return $sql;
	}
}