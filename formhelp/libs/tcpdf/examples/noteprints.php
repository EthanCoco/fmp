<?php
@ini_set('memory_limit', '1024M'); 
ini_set('max_execution_time', '0');
include "../../common/medoo.php";
require_once('tcpdf_include.php');
$database = new medoo();
$database1 = new medoo();
$curYear = date("Y");

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 001');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
$pdf->SetFont('stsongstdlight', '', 10, '', true);
// set default header data
$pdf->SetHeaderData("", '', '名册', "", array(255,255,255), array(255,255,255));
//$pdf->setFooterData(array(0,64,0), array(0,64,128));

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
//$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
//$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
//echo PDF_MARGIN_TOP;
// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
//$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

$codeArr = array();
$sql = "select codeID,codeName from xy_code";
$datas = $database->query($sql)->fetchAll();
foreach ($datas as $data)
{
	$codeArr[$data['codeID']] = $data['codeName'];
}

// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.

$noteid = isset($_GET["noteid"])?$_GET["noteid"]:"";
if(!empty($noteid)&&$noteid>0){
$sql = "select * from xy_note where noteID=".$noteid;
$datas = $database->query($sql)->fetchAll();
//print_r($datas);
foreach ($datas as $data){

$noteRealName = $data["noteRealName"];
$noteIdCard = $data["noteIdCard"];	
//echo $noteIdCard;
//if(strlen($noteIdCard)==18)
$birthday = substr($noteIdCard,6,4).".".substr($noteIdCard,10,2);
//echo $birthday;
$noteA = "";
if(!empty($data["noteA"]))
$noteA = $codeArr[$data['noteA']];//性别

$noteB = $data["noteB"];//身高
if(empty($noteB)) $noteB = 0;
$noteC = $data["noteC"];//体重
if(empty($noteC)) $noteC = 0;

$noteD = "";
if(!empty($data["noteD"]))
$noteD = $codeArr[$data['noteD']];//婚姻状况

$noteE = "";
if(!empty($data["noteE"]))
$noteE = $codeArr[$data['noteE']];//政治面貌

$noteF = "";
if(!empty($data["noteF"]))
$noteF = $codeArr[$data['noteF']];//民族

$noteBK = "";
if(!empty($data["noteBK"]))//健康状况
$noteBK = $codeArr[$data["noteBK"]];

$noteBC = "";
if(!empty($data["noteBC"]))
$noteBC = $codeArr[$data['noteBC']];//身份

$noteBB = "";
if(!empty($data["noteBB"]))
$noteBB = $codeArr[$data['noteBB']];//人员性质noteBJ

$noteBJ = "";
if(!empty($data["noteBJ"]))
$noteBJ = $codeArr[$data['noteBJ']];//户口性质
$noteO = $data['noteO'];//户口地址noteQ

$noteQ = "";
if(!empty($data["noteQ"]))
$noteQ = $codeArr[$data['noteQ']];//持有居住证情况
$noteR = $data['noteR'];// 居住证号
$noteS = $data['noteS'];//有效期

$noteM = "";
if(!empty($data["noteM"]))
$noteM = $codeArr[$data['noteM']];//户口所在地
//noteBL
$noteBL = "";
if(!empty($data["noteBL"]))
$noteBL = $codeArr[$data['noteBL']];//是否服兵役
$noteBM = $data['noteBM'];//noteO

$noteT = "";
if(!empty($data["noteT"]))
$noteT = $codeArr[$data['noteT']];//教师资格
$noteU = $data['noteU'];//

$noteZ = "";
if(!empty($data["noteZ"]))
$noteZ = $codeArr[$data['noteZ']];//教师资格证考试情况
$noteAA = "";
if(!empty($data["noteAA"]))
$noteAA = $codeArr[$data['noteAA']];//普通话

$noteJ = $data['noteJ'];//家庭常住地址
$noteL = $data['noteL'];//手机号码
$noteN = $data['noteN'];//固定号码

$noteP = $data['noteP'];//家庭常住地址
$noteBA = $data["noteBA"];
$noteBH = $data["noteBH"];
$noteAG = $data["noteAG"];//教育教学工作、实习经历
$noteAZ = $data["noteAZ"];//教育教学工作、实习经历
$noteAW = $data["noteAW"];

$EnglishInfo = "";
$noteAH = $data["noteAH"];
$noteAJ = $data["noteAJ"];
$noteAL = $data["noteAL"];
$noteAN = $data["noteAN"];

if($noteAN>0)$EnglishInfo = "TEM8";
else if($noteAL>0) $EnglishInfo = "TEM4";
else if($noteAJ>0) $EnglishInfo = "CET6";
else if($noteAH>0) $EnglishInfo = "CET4";

$computerInfo = "";
$noteAS = $data["noteAS"];
$noteBF = $data["noteBF"];
$noteBG = $data["noteBG"];
if($noteBG>0)$computerInfo = "三级";
else if($noteBF>0) $computerInfo = "二级";
else if($noteAS>0) $computerInfo = "一级";


$sql = "select * from xy_teacher left join xy_code on xy_code.codeID=xy_teacher.teacherA where teacherNoteID=".$noteid." order by codeOrder";
$teacherInfo = $database1->query($sql)->fetchAll();

$teacherA = "";
$teacherB = "";
$teacherC = "";
$teacherE = "";
$teacherF = "";
$teacherG = "";
$teacherH = "";
$teacherI = "";
$teacherJ = "";

$teacherdetails = "";
$teacherNum = 1;
foreach($teacherInfo as $teacherInfos)
{
	$teacherNum++;
	$teacherA = $teacherInfos["codeName"];//学历
	
	if(!empty($teacherInfos["teacherB"]))//学位
	$teacherB = $codeArr[$teacherInfos["teacherB"]];
	
	$teacherC = $teacherInfos["teacherC"];//毕业院校
	//if(!empty($teacherInfos["teacherE"]))//所学专业
	$teacherE = $teacherInfos["teacherE"];
	
	if(!empty($teacherInfos["teacherF"]))//学制
	$teacherF = $codeArr[$teacherInfos["teacherF"]];
	
	if(!empty($teacherInfos["teacherG"]))//学习形式
	$teacherG = $codeArr[$teacherInfos["teacherG"]];
	
	$teacherH = $teacherInfos["teacherH"];
	if($teacherH=='0000-00-00') $teacherH = "";
	
	$teacherI = $teacherInfos["teacherI"];
	if($teacherI=='0000-00-00') $teacherI = "";
	
	
	$teacherdetails = $teacherdetails."<tr>";
	$teacherdetails = $teacherdetails."<td>".$teacherA."</td>";
	$teacherdetails = $teacherdetails."<td>".$teacherB."</td>";
	$teacherdetails = $teacherdetails."<td>".$teacherC."</td>";
	$teacherdetails = $teacherdetails."<td>".$teacherE."</td>";
	$teacherdetails = $teacherdetails."<td>".$teacherG."</td>";
	$teacherdetails = $teacherdetails."<td>".$teacherI."</td>";
	$teacherdetails = $teacherdetails."</tr>";
}
/*
<tr>
					<td width="5%" rowspan="$familyNum" style="line-height:16px;">$familySpan
					家庭<br/>主要<br/>成员</td>
					<td width="10%">称谓</td>
					<td width="10%">姓名</td>
					<td width="30%">工作单位</td>
					<td width="25%">职务</td>
					<td width="20%">电话</td>
				</tr>*/
$familydetails = "";
$familyNum = 1;
$sql = "select * from xy_family where familyNoteID=".$noteid;
$familyInfos = $database1->query($sql)->fetchAll();
$familySpan = "";
foreach($familyInfos as $familyInfo)
{
	$familyNum++;
	$familyA = "";
	if(!empty($familyInfo["familyA"]))//学位
	$familyA = $codeArr[$familyInfo["familyA"]];
	$familyB = $familyInfo["familyB"];
	$familyC = $familyInfo["familyC"];
	$familyD = $familyInfo["familyD"];
	$familyE = $familyInfo["familyE"];
	
	$familydetails = $familydetails."<tr>";
	$familydetails = $familydetails."<td>".$familyA."</td>";
	$familydetails = $familydetails."<td>".$familyB."</td>";
	$familydetails = $familydetails."<td>".$familyC."</td>";
	$familydetails = $familydetails."<td>".$familyD."</td>";
	$familydetails = $familydetails."<td>".$familyE."</td>";
	$familydetails = $familydetails."</tr>";
}

if($familyNum>2)
{
	$spantop = ($familyNum-2)*45;
	$familySpan = '<span style="display:block;line-height:'.$spantop.'px"></span>';
}
$pdf->AddPage();

$html = <<<EOD
<style type="text/css">
table.inner{width:100%; border-collapse:collapse;table-layout:fixed; text-align:center; font-size:10px; font-family:'宋体'}
table.inner td{vertical-align:middle;border:solid 2px #000;padding-top:3px;height:28px;line-height:30px }
</style>

<p style="text-align:center;font-size:14px; font-weight:bold;height:15px;font-family:'黑体'">$curYear 年青浦区教育系统非在编教师简历表</p>

<table width="100%">
	<tr>
		<td border="1.5">
			<table class="inner" width="100%">
				<tr>
					<td width="10%" >姓 名</td>
					<td width="15%">$noteRealName</td>
					<td width="5%">性 别</td>
					<td width="5%">$noteA</td>
					<td width="15%">出生年月</td>
					<td width="20%">$birthday</td>
					<td width="10%">民 族</td>
					<td width="20%">$noteF</td>
				</tr>
				
				<tr>
					<td>婚姻状况</td>
					<td>$noteD</td>
					<td colspan="2">政治面貌</td>
					<td colspan="2">$noteE</td>
					<td>健康状况</td>
					<td>$noteBK</td>
				</tr>
				
				<tr>
					<td>身 高</td>
					<td>$noteB cm</td>
					<td colspan="2">体重</td>
					<td colspan="2">$noteC Kg</td>
					<td>户口所在地</td>
					<td>$noteM</td>
				</tr>
				
				<tr>
					<td>是否服兵役</td>
					<td>$noteBL</td>
					<td colspan="2">兵种</td>
					<td>$noteBM</td>
					<td>人员性质</td>
					<td colspan="2">$noteBB</td>
				</tr>
			</table>
		</td>
	</tr>
	
	<tr>
		<td border="1.5">
			<table class="inner" width="100%">
				<tr>
					<td width="10%" >户口性质</td>
					<td width="15%">$noteBJ</td>
					<td width="10%" colspan="2">户口地址</td>
					<td width="65%" colspan="4">$noteO</td>
					
				</tr>
				
				<tr>
					<td width="10%">居住证情况</td>
					<td width="15%">$noteQ</td>
					<td width="10%" colspan="2">居住证号</td>
					<td width="35%" colspan="2">$noteR</td>
					<td width="10%">有效期</td>
					<td width="20%">$noteS</td>
				</tr>
				
				<tr>
					<td width="10%">常住地址</td>
					<td width="90%" colspan="7">$noteP</td>
				</tr>
				
				<tr>
					<td width="10%">手    机</td>
					<td width="15%">$noteL</td>
					<td width="10%" colspan="2">固定电话</td>
					<td width="35%" colspan="2">$noteN</td>
					<td width="10%">原工作单位</td>
					<td width="20%">$noteJ</td>
				</tr>
			</table>
		</td>
	</tr>
	
	<tr>
		<td border="1.5">
			<table class="inner" width="100%">
				<tr>
					<td width="10%" >教师资格证</td>
					<td width="60%" colsapn="5">$noteT</td>
					<td width="10%">任教学科</td>
					<td width="20%">$noteU</td>
					
				</tr>
				
				<tr>
					<td width="25%" colsapn="2">教师资格课程考试情况</td>
					<td width="45%" colsapn="4">$noteZ</td>
					<td width="10%">普通话等级</td>
					<td width="20%">$noteAA</td>
					
				</tr>
			</table>
		</td>
	</tr>
	
	<tr>
		<td border="1.5">
			<table class="inner" width="100%">
				<tr>
					<td width="15%">学历</td>
					<td width="20%">学位</td>
					<td width="20%">毕业院校</td>
					<td width="20%">所学专业</td>
					<td width="15%">学习形式</td>
					<td width="10%">毕业时间</td>
				</tr>
				$teacherdetails
			</table>
		</td>
	</tr>
	
        
	<tr>
		<td border="1.5">
			<table class="inner" width="100%">
				<tr>
					<td width="5%" style="line-height:16px;">
					<span style="display:block;line-height:30px"></span>
					教育<br/>教学<br/>工作<br/>经历
					<span style="display:block;line-height:10px"></span>
					</td>
					<td width="95%" align="left">&nbsp;$noteAG</td>
				</tr>
			</table>
		</td>
	</tr>
	
	
	<tr>
		<td border="1.5">
			<table class="inner" width="100%">
				<tr>
					<td width="5%" rowspan="$familyNum" style="line-height:16px;">$familySpan
					家庭<br/>主要<br/>成员</td>
					<td width="10%">称谓</td>
					<td width="10%">姓名</td>
					<td width="30%">工作单位</td>
					<td width="25%">职务</td>
					<td width="20%">电话</td>
				</tr>
				$familydetails
				
			</table>
		</td>
	</tr>
	
	<tr>
		<td border="1.5">
			<table class="inner" width="100%">
				<tr>
					<td width="15%">外语等级水平</td>
					<td width="30%">$EnglishInfo</td>
					<td width="20%">计算机等级水平</td>
					<td width="35%">$computerInfo</td>
				</tr>
				
				<tr>
					<td width="5%" style="line-height:16px;"><span style="display:block;line-height:60px"></span>所获<br/>荣誉<span style="display:block;line-height:40px"></span></td>
					<td width="95%" align="left">&nbsp;$noteAW</td>
				</tr>
				<tr>
					<td width="5%" style="line-height:16px;"><span style="display:block;line-height:60px"></span>补充<br/>说明<span style="display:block;line-height:40px"></span></td>
					<td width="95%" align="left">&nbsp;$noteAZ</td>
				</tr>
			</table>
		</td>
	</tr>
    </table>
	
	<p>本人承诺以上内容及所附其他材料的真实性，如有虚假，由此引发的一切后果由本人承担。</p>
	<p style="text-align:right;line-height:24px">报名者本人签名：____________________</p>
EOD;

//echo $html;
// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

}

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output("personlist.pdf", 'I');//D
}
//============================================================+
// END OF FILE
//============================================================+
