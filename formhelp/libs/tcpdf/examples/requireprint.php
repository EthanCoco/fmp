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

$studyArr = array();
$sql = "select studyID,studyName from xy_study";
$dataArrs = $database->query($sql)->fetchAll();
foreach ($dataArrs as $data)
	$studyArr[$data['studyID']] = $data['studyName'];
	
$companyArr = array();
$sql = "select companyID,companyName from xy_company";
$dataArrs = $database->query($sql)->fetchAll();
foreach ($dataArrs as $data)
	$companyArr[$data['companyID']] = $data['companyName'];	
// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.

$requiresID = isset($_GET["requiresID"])?$_GET["requiresID"]:"";
if(!empty($requiresID)&&$requiresID>0){
$sql = "select * from xy_requires where requiresID=".$requiresID;
$datas = $database->query($sql)->fetchAll();
foreach ($datas as $data){

$companyName = $companyArr[$data["requiresCompanyID"]];
$requiresYear = $data["requiresYear"];	
$requiresDescription = $data["requiresDescription"];
$requiresApplyNum = $data["requiresApplyNum"];
$requiresApplys1 = $data["requiresApplys1"];
$requiresApplys2 = $data["requiresApplys2"];
$requiresApplyUser = $data["requiresApplyUser"];
$requiresOptionNum = $data["requiresOptionNum"];
$requiresOptions1 = $data["requiresOptions1"];
$requiresOptions2 = $data["requiresOptions2"];
$requiresOptions3 = $data["requiresOptions3"];
$requiresApplyTime = $data["requiresApplyTime"];
$requiresOptionTime = $data["requiresOptionTime"];

if($requiresApplyTime=='0000-00-00') $requiresApplyTime = "";
if(!empty($requiresApplyTime)){
	$requiresApplyTimeArr = explode("-",$requiresApplyTime);
	$requiresApplyTime = $requiresApplyTimeArr[0]."年".$requiresApplyTimeArr[1]."月".$requiresApplyTimeArr[2]."日";
}
else
$requiresApplyTime = "&nbsp;年&nbsp;月&nbsp;日";

if($requiresOptionTime=='0000-00-00') $requiresOptionTime = "";
if(!empty($requiresOptionTime)){
$requiresOptionTimeArr = explode("-",$requiresOptionTime);
$requiresOptionTime = $requiresOptionTimeArr[0]."年".$requiresOptionTimeArr[1]."月".$requiresOptionTimeArr[2]."日";
}
else
$requiresOptionTime = "&nbsp;年&nbsp;月&nbsp;日";

$sql = "select * from xy_require where requiresID=".$requiresID."&&requireStatus=0";
$requireInfo = $database1->query($sql)->fetchAll();

$requireA = "";
$requireB = "";
$requireC = "";
$requireD = "";
$requireE = "";
$requireF = "";
$requireG = "";
$requireH = "";
$requireI = "";

$requiredetails = "";
$teacherNum = 0;
foreach($requireInfo as $teacherInfos)
{
	$requireA = "";
	$requireB = "";
	$requireC = "";
	$requireD = "";
	$requireE = "";
	$requireF = "";
	$requireG = "";
	$requireH = "";
	$requireI = "";

	$teacherNum++;
	if(!empty($teacherInfos["requireA"]))//学段
	$requireA = $codeArr[$teacherInfos["requireA"]];
	
	if(!empty($teacherInfos["requireB"]))//年级
	$requireB = $codeArr[$teacherInfos["requireB"]];
	
	if(!empty($teacherInfos["requireC"]))//岗位性质
	$requireC = $codeArr[$teacherInfos["requireC"]];
	
	if(!empty($requireC))
	{
		$requireCs = explode("--",$requireC);
		$requireC = $requireCs[1];
	}
	if(!empty($teacherInfos["requireD"]))//岗位性质
	$requireD = $codeArr[$teacherInfos["requireD"]];
	
	$requireE = $teacherInfos["requireE"];
	
	$requireF = $teacherInfos["requireF"];
	
	if(!empty($teacherInfos["requireG"]))//学段
	$requireG = $codeArr[$teacherInfos["requireG"]];
	
	if(!empty($teacherInfos["requireI"]))//学段
	$requireI = $codeArr[$teacherInfos["requireI"]];
	
	if(!empty($teacherInfos["requireH"]))//学段
	$requireH = $codeArr[$teacherInfos["requireH"]];
	
	
	$requiredetails = $requiredetails."<tr>";
	$requiredetails = $requiredetails."<td>".$teacherNum."</td>";
	$requiredetails = $requiredetails."<td>".$requireA."</td>";
	$requiredetails = $requiredetails."<td>".$requireB."</td>";
	$requiredetails = $requiredetails."<td>".$requireC."</td>";
	$requiredetails = $requiredetails."<td>".$requireD."</td>";
	$requiredetails = $requiredetails."<td>".$requireE."</td>";
	$requiredetails = $requiredetails."<td>".$requireF."</td>";
	$requiredetails = $requiredetails."<td>".$requireG."</td>";
	$requiredetails = $requiredetails."<td>".$requireH."</td>";
	$requiredetails = $requiredetails."<td>".$requireI."</td>";
	$requiredetails = $requiredetails."</tr>";
}
$pdf->AddPage();

$html = <<<EOD
<style type="text/css">
table.inner{width:100%; border-collapse:collapse;table-layout:fixed; text-align:center; font-size:10px; font-family:'宋体'}
table.inner td{vertical-align:middle;line-height:28px;border:solid 2px #000; }
</style>

<p style="text-align:center;font-size:14px; font-weight:bold;height:15px;font-family:'黑体'">$requiresYear 学年度非在编教师需求申请表</p>
<p style="font-size:12px;height:15px;font-family:'黑体'">学校：$companyName</p>

<table width="100%" class="inner" cellpadding="0" cellspacing="0">
	<tr>
		<td width="5%" style="font-weight:bold">序号</td>
		<td width="10%" style="font-weight:bold">学段</td>
		<td width="10%" style="font-weight:bold">任教年级</td>
		<td width="10%" style="font-weight:bold">岗位性质</td>
		<td width="10%" style="font-weight:bold">任教学科</td>
		<td width="10%" style="font-weight:bold">需求人数</td>
		<td width="15%" style="font-weight:bold">岗位要求</td>
		<td width="10%" style="font-weight:bold">代课时间</td>
		<td width="10%" style="font-weight:bold">代课原因</td>
		<td width="10%" style="font-weight:bold">有效期</td>
	</tr>
	
	$requiredetails
				
	<tr>
		<td style="font-weight:bold;line-height:18px;">说<br/>明</td>
		<td colspan="9" style="text-align:left">&nbsp;&nbsp;&nbsp;&nbsp;$requiresDescription</td>
	</tr>
	<tr>
		<td rowspan="3" style="font-weight:bold;line-height:18px">学<br/>校<br/>意<br/>见</td>
		<td colspan="9" style="text-align:left;border-bottom:solid 2px #fff;line-height:22px">
			&nbsp;&nbsp;&nbsp;&nbsp;学校申请非在编教师 $requiresApplyNum 人（其中：教师 $requiresApplys1 人、教练 $requiresApplys2 人）
		</td>
	</tr>
	<tr>
		<td colspan="9" style="text-align:right;border-bottom:solid 2px #fff;line-height:22px">
				经办人：$requiresApplyUser
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				校（园）长：&nbsp;&nbsp;&nbsp;
		</td>
	</tr>
	<tr>
		<td colspan="9" style="text-align:right;line-height:22px">日期：$requiresApplyTime &nbsp;&nbsp;</td>
	</tr>
	
	<tr>
		<td rowspan="4" style="font-weight:bold;line-height:18px">教<br/>育<br/>局<br/>意<br/>见</td>
		<td colspan="9" style="text-align:left;border-bottom:solid 2px #fff;line-height:22px">
			&nbsp;&nbsp;&nbsp;&nbsp;同意使用非在编教师 $requiresOptionNum 人（其中：教师 $requiresOptions1 人、教练 $requiresOptions2 人）；
		</td>
	</tr>
	<tr>
		<td colspan="9" style="text-align:left;border:solid 2px #fff;border-right:solid 2px #000;line-height:22px">
		&nbsp;&nbsp;&nbsp;&nbsp;其中核定缺额教师：$requiresOptions3 人
		</td>
	</tr>
	<tr>
		<td colspan="9" style="border:solid 2px #fff;text-align:right;border-right:solid 2px #000;line-height:22px">
		审核人：教育局人事科 &nbsp;&nbsp;
		</td>
	</tr>
	<tr>
		<td colspan="9" style="border:solid 2px #fff;text-align:right;border-right:solid 2px #000;border-bottom:solid 2px #000;line-height:22px">
		日期：$requiresOptionTime &nbsp;&nbsp;
		</td>
	</tr>
	
    </table>
	
	<p>注：本表经教育局审核后学校打印一式三份，学校盖章后交中心二份（学校、中心和人事科各存档一份）。</p>
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
