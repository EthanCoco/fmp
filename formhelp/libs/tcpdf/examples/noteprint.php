<?php
@ini_set('memory_limit', '1024M'); 
ini_set('max_execution_time', '0');
include "../../common/medoo.php";
require_once('tcpdf_include.php');
$database = new medoo();
$database1 = new medoo();


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

$studyArr = array();
$sql = "select studyID,studyName from xy_study";
$datas = $database->query($sql)->fetchAll();
foreach ($datas as $data)
{
	$studyArr[$data['studyID']] = $data['studyName'];
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
$noteA = "";
if(!empty($data["noteA"]))
$noteA = $codeArr[$data['noteA']];//性别

$noteD = "";
if(!empty($data["noteD"]))
$noteD = $codeArr[$data['noteD']];//婚姻状况

$noteE = "";
if(!empty($data["noteE"]))
$noteE = $codeArr[$data['noteE']];//政治面貌

$noteF = "";
if(!empty($data["noteF"]))
$noteF = $codeArr[$data['noteF']];//民族

$noteBC = "";
if(!empty($data["noteBC"]))
$noteBC = $codeArr[$data['noteBC']];//身份

$noteM = "";
if(!empty($data["noteM"]))
$noteM = $codeArr[$data['noteM']];//户籍

$noteT = "";
if(!empty($data["noteT"]))
$noteT = $codeArr[$data['noteT']];//教师资格

$noteAA = "";
if(!empty($data["noteAA"]))
$noteAA = $codeArr[$data['noteAA']];//普通话

$noteL = $data['noteL'];//手机号码

$noteP = $data['noteP'];//家庭常住地址
$noteBA = $data["noteBA"];
$noteBH = $data["noteBH"];
$imgstrs = "";
if(!empty($noteBH))
$imgstrs = "<img src='".$noteBH."'/>";

$infos = "是否愿意区教育人才服务中心调剂：○&nbsp;&nbsp;愿意&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; ○&nbsp;&nbsp;不愿意";
//⊙&nbsp;&nbsp;愿意&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; ○&nbsp;&nbsp;不愿意
if($noteBA==111)
$infos = "是否愿意区教育人才服务中心调剂：●&nbsp;&nbsp;愿意&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; ○&nbsp;&nbsp;不愿意";
elseif($noteBA==112)
$infos = "是否愿意区教育人才服务中心调剂：○&nbsp;&nbsp;愿意&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; ●&nbsp;&nbsp;不愿意";

$noteN = $data['noteN'];//固定号码
$sql = "select * from xy_teacher left join xy_code on xy_code.codeID=xy_teacher.teacherA where teacherNoteID=".$noteid." order by codeOrder limit 1";
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

foreach($teacherInfo as $teacherInfos)
{
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
	
	if(!empty($teacherInfos["teacherJ"]))//学习形式
	$teacherJ = $codeArr[$teacherInfos["teacherJ"]];
	
	
}

$sql = "select * from xy_apply left join xy_company on xy_company.companyID=xy_apply.applyC where applyNoteID=".$noteid." order by applyId";
$applyInfos = $database1->query($sql)->fetchAll();
$i = 0;	
$str = "";				
foreach($applyInfos as $applyInfo)
{
	$i++;
	$applyA = "";
	if(!empty($applyInfo["applyA"]))//学段
	$applyA = $studyArr[$applyInfo["applyA"]];
	
	$applyB = "";
	if(!empty($applyInfo["applyB"]))//学习形式
	$applyB = $codeArr[$applyInfo["applyB"]];
	
	$str = $str."<tr>";
	$str = $str."<td width=\"10%\">".$i."</td>";
	$str = $str."<td width=\"20%\">".$applyA."</td>";
	$str = $str."<td width=\"20%\">".$applyB."</td>";
	$str = $str."<td width=\"50%\">".$applyInfo["companyName"]."</td>";
	$str = $str."</tr>";
}

$pdf->AddPage();

$html = <<<EOD
<style type="text/css">
table.inner{width:100%; border-collapse:collapse;table-layout:fixed; text-align:center; font-size:10px; font-family:'宋体'}
table.inner td{vertical-align:middle;border:solid 2px #000;padding-top:3px;height:24px;line-height:40px }
</style>

<p style="text-align:center;font-size:14px; font-weight:bold;height:15px;">2015年青浦区教育系统非在编教师报名信息表</p>

<table width="100%">
	<tr>
		<td border="1.5">
			<table class="inner" width="100%">
				<tr>
					<td width="5%" rowspan="5" style="line-height:16px;"><span style="display:block;line-height:190px"></span>基本<br/>情况</td>
					<td width="15%" >姓名</td>
					<td width="20%" colspan="2" >$noteRealName</td>
					<td width="15%">性别</td>
					<td width="20%">$noteA</td>
					<td rowspan="5" width="25%">$imgstrs</td>
				</tr>
				
				<tr>
					<td>身份证号码</td>
					<td colspan="2">$noteIdCard</td>
					<td>报名身份</td>
					<td>$noteBC</td>
				</tr>
				
				<tr>
					<td>政治面貌</td>
					<td colspan="2">$noteE</td>
					<td>婚姻状况</td>
					<td>$noteD</td>
				</tr>
				
				<tr>
					<td>户籍</td>
					<td colspan="2">$noteM</td>
					<td>民族</td>
					<td>$noteF</td>
				</tr>
				
				<tr>
					<td>教师资格</td>
					<td colspan="2">$noteT</td>
					<td>普通话等级</td>
					<td>$noteAA</td>
				</tr>
			</table>
		</td>
	</tr>
        
	<tr>
		<td border="1.5">
			<table class="inner" width="100%">
				<tr>
					<td width="5%" rowspan="2" style="line-height:16px;"><span style="display:block;line-height:66px"></span>联系<br/>方式</td>
					<td width="15%">手机</td>
					<td width="20%" colspan="2" >$noteL</td>
					<td width="15%">其他</td>
					<td colspan="2" width="45%">$noteN</td>
				</tr>
				
				<tr>
					<td>住址</td>
					<td colspan="6">$noteP</td>
				</tr>
			</table>
		</td>
	</tr>
	
	<tr>
		<td border="1.5">
			<table class="inner" width="100%">
				<tr>
					<td width="5%" rowspan="4" style="line-height:16px;"><span style="display:block;line-height:140px"></span>最高<br/>学历</td>
					<td width="15%">学历</td>
					<td width="40%" colspan="2" >$teacherA</td>
					<td width="15%">学位</td>
					<td colspan="2" width="25%">$teacherB</td>
				</tr>
				
				<tr>
					<td>毕业院校</td>
					<td colspan="6">$teacherC</td>
				</tr>
				<tr>
					<td>专业</td>
					<td>$teacherE</td>
					<td>学制</td>
					<td>$teacherF</td>
					<td>学习形式</td>
					<td>$teacherG</td>
				</tr>
				<tr>
					<td>是否师范</td>
					<td>$teacherJ</td>
					<td>入学时间</td>
					<td>$teacherH</td>
					<td>毕业时间</td>
					<td>$teacherI</td>
				</tr>
			</table>
		</td>
	</tr>
    </table>
	<p style="line-height:60px"></p>
	<p style="text-align:center;font-size:14px; font-weight:bold;height:15px;">报名意向</p>
	<table width="100%">
		<tr>
			<td border="1.5">
				<table class="inner" width="100%">
					<tr>
						<td width="10%">意向</td>
						<td width="20%" >学段</td>
						<td width="20%" >学科</td>
						<td width="50%">学校</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td border="1.5">
				<table class="inner" width="100%">
					$str
				</table>
			</td>
		</tr>


	</table>
	<p style="line-height:30px">$infos</p>
	<p style="text-align:right;line-height:30px">报名者本人签名：____________________</p>
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
