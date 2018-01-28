<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use app\assets\AppAsset;
AppAsset::register($this);
$this->registerJsFile("@web/js/common/jquery-1.9.1.min.js", ['depends' => ['yii\web\YiiAsset'], 'position' => $this::POS_HEAD]);
$this->title = '';
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
<meta charset="<?= Yii::$app->charset ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?= Html::csrfMetaTags() ?>
<title><?= Html::encode($this->title) ?></title>
<?php $this->head() ?>
<style>
	.star{color:red;}
	td{
		padding: 0 3px;
	}
</style>
</head>
<body>
<?php $this->beginBody() ?>
<div style="padding: 10px;">
	<center>
	<div id="print_view" class="layui-form">
		
	</div>
	</center>
</div>
<?php $this->endBody() ?>
	
<script>
$(function(){
	layui.use(['form','layer','laydate'], function(){
		var layer = layui.layer;
		var form = layui.form;
	});
	
	$("#print_view").load("<?= $file ?>",{},function(json){
		var type = "<?= $type ?>";
		var infos = "";
		<?php if(!empty($infos)){ ?>
			infos = <?=json_encode($infos) ?>;
		<?php } ?>		
		init_table_html(type,infos);
	});
});

function init_table_html(type,infos){
	var uploadA = [];
	layui.use(['laydate','upload'], function(){
		var	laydate = layui.laydate;
		var upload = layui.upload;
	
		if(type == "0"){
			$("#print_view").find('table tr td').each(function(){
				var j_v = $(this).attr("title");
				if(typeof j_v !== 'undefined'){
					$(this).html(j_v);
				}else{
					var obj_a = $(this).find("a");
					if(typeof obj_a !=='undefined'){
						$(this).find("a").css("display",'none');
					}
				}
//				var j_v = $(this).html().trim();
//				if(/^[0-9]+.?[0-9]*$/.test(j_v)){
//					if(infos != ""){
//						var len = infos.length;
//						for(var i=0;i<len;i++){
//							if(j_v == infos[i]['FIELD_ID']){
//								var ps = $(this).prev();
//								var pTitle = "";
//								if(infos[i]['FIELD_TYPE'] == "5"){
//									pTitle = "照片";
//								}else{
//									if(typeof ps !== "undefined"){
//										pTitle = $(ps).html();
//									}
//								}
//								var final_title = pTitle.replace(" ",'').replace("<br>","").replace("<br/>","").replace(" ",'');
//								$(this).html(final_title);
//								break;
//							}
//						}
//					}
//				}
			});
		}else if(type == "1"){
			var index = 0;
			$("#print_view").find('table tr td').each(function(){
				var j_v = $(this).attr("id");
				if(typeof j_v !== 'undefined'){
					if(infos != ""){
						var len = infos.length;
						for(var i=0;i<len;i++){
							if(j_v == (infos[i]['FIELD_NAME']+"_"+ infos[i]['FIELD_ID'])){
								var $_html = "";
								switch(infos[i]['FIELD_TYPE']){
									case "301" :
									 	$_html += '<input id="a_'+infos[i]['FIELD_NAME']+'" name="'+infos[i]['FIELD_NAME']+'" title="'+infos[i]['FIELD_DESC']+'" style="border:1px solid #93D1FF;font-family:宋体; font-size:11pt;" type="text"  class="layui-input">'	
									break;
									case "302" :
									  	$_html += '<textarea id="a_'+infos[i]['FIELD_NAME']+'" name="'+infos[i]['FIELD_NAME']+'" title="'+infos[i]['FIELD_DESC']+'" style="border:1px solid #93D1FF;font-family:宋体; font-size:11pt;"  class="layui-textarea"></textarea>'	
									break;
									case "102" :
									 	$_html += '<input id="a_'+infos[i]['FIELD_NAME']+'" name="'+infos[i]['FIELD_NAME']+'" title="'+infos[i]['FIELD_DESC']+'" style="border:1px solid #93D1FF;font-family:宋体; font-size:11pt;" type="text"  class="layui-input">'	
									  	setTimeout(function(){
									  		laydate.render({
										    	elem: '#a_'+infos[i]['FIELD_NAME'],
										    	type: "month"
										  	});
									  	},500);
									  	
									break;
									case "4" :
									 	$_html += '<input hasSearch=1 id="a_'+infos[i]['FIELD_NAME']+'" name="'+infos[i]['FIELD_NAME']+'" title="'+infos[i]['FIELD_DESC']+'" code_index="'+infos[i]['FIELD_CODE']+'"  view_type="1" onclick="show_control_view(this)" readonly style="border:1px solid #93D1FF;font-family:宋体; font-size:11pt;" type="text"  class="layui-input">'	
									break;
									case "5" :
									 	$_html += '<img id="p_'+infos[i]['FIELD_NAME']+'" style="width:90px;" src="../../../web/images/user-default.jpg">'	
									 	$_html += '<input id="a_'+infos[i]['FIELD_NAME']+'" name="'+infos[i]['FIELD_NAME']+'" title="'+infos[i]['FIELD_DESC']+'" style="border:1px solid #93D1FF;font-family:宋体; font-size:11pt;display:none;" type="text"  class="layui-input">'	
										setTimeout(function(){
											upload.render({
											    elem: '#p_'+infos[i]['FIELD_NAME'],
											    url: "<?= yii\helpers\Url::to(['default/uploadimage']); ?>",
											    data:{},
											    accept: 'images',
											    exts: 'jpg|png|gif|bmp|jpeg',
											    size: 1024*1024*2,
											    done: function(res){
											    	if(res.code != '0'){
											        	return parent.layer.msg(res.msg);
											      	}else{
											      		$("#p_"+infos[i]['FIELD_NAME']).attr("src",res.data.src);
											      		$("#a_"+infos[i]['FIELD_NAME']).val(res.data.src);
											      	}
											    }
											});
										},500);		
										
										//uploadA[index] = ["p_"+infos[i]['FIELD_NAME'],"a_"+infos[i]['FIELD_NAME']];
									break;
									case "6" :
									 	$_html += '<input id="a_'+infos[i]['FIELD_NAME']+'" name="'+infos[i]['FIELD_NAME']+'" title="'+infos[i]['FIELD_DESC']+'" code_index="'+infos[i]['FIELD_CODE']+'" singleOrMore=2  view_type="2" onclick="show_control_view(this)" readonly style="border:1px solid #93D1FF;font-family:宋体; font-size:11pt;" type="text"  class="layui-input">'	
									break;
									default:
									  	
									break;
								}
								
								$(this).html($_html);
								break;
							}
							
						}
					}
				}
//				var j_v = $(this).html().trim();
//				if(/^[0-9]+.?[0-9]*$/.test(j_v)){
//					if(infos != ""){
//						var len = infos.length;
//						for(var i=0;i<len;i++){
//							if(j_v == infos[i]['FIELD_ID']){
//								var $_html = "";
//								switch(infos[i]['FIELD_TYPE']){
//									case "301" :
//									 	$_html += '<input id="a_'+infos[i]['FIELD_NAME']+'" name="'+infos[i]['FIELD_NAME']+'" title="'+infos[i]['FIELD_DESC']+'" style="border:1px solid #93D1FF;font-family:宋体; font-size:11pt;" type="text"  class="layui-input">'	
//									break;
//									case "302" :
//									  	$_html += '<textarea id="a_'+infos[i]['FIELD_NAME']+'" name="'+infos[i]['FIELD_NAME']+'" title="'+infos[i]['FIELD_DESC']+'" style="border:1px solid #93D1FF;font-family:宋体; font-size:11pt;"  class="layui-textarea"></textarea>'	
//									break;
//									case "102" :
//									 	$_html += '<input id="a_'+infos[i]['FIELD_NAME']+'" name="'+infos[i]['FIELD_NAME']+'" title="'+infos[i]['FIELD_DESC']+'" style="border:1px solid #93D1FF;font-family:宋体; font-size:11pt;" type="text"  class="layui-input">'	
//									  	setTimeout(function(){
//									  		laydate.render({
//										    	elem: '#a_'+infos[i]['FIELD_NAME'],
//										    	type: "month"
//										  	});
//									  	},500);
//									  	
//									break;
//									case "4" :
//									 	$_html += '<input id="a_'+infos[i]['FIELD_NAME']+'" name="'+infos[i]['FIELD_NAME']+'" title="'+infos[i]['FIELD_DESC']+'" code_index="'+infos[i]['FIELD_CODE']+'"  view_type="1" onclick="show_control_view(this)" readonly style="border:1px solid #93D1FF;font-family:宋体; font-size:11pt;" type="text"  class="layui-input">'	
//									break;
//									case "5" :
//									 	$_html += '<img id="p_'+infos[i]['FIELD_NAME']+'" style="width:90px;" src="../../../web/images/user-default.jpg">'	
//									 	$_html += '<input id="a_'+infos[i]['FIELD_NAME']+'" name="'+infos[i]['FIELD_NAME']+'" title="'+infos[i]['FIELD_DESC']+'" style="border:1px solid #93D1FF;font-family:宋体; font-size:11pt;display:none;" type="text"  class="layui-input">'	
//										setTimeout(function(){
//											upload.render({
//											    elem: '#p_'+infos[i]['FIELD_NAME'],
//											    url: "<= yii\helpers\Url::to(['default/uploadimage']); ?>",
//											    data:{},
//											    accept: 'images',
//											    exts: 'jpg|png|gif|bmp|jpeg',
//											    size: 1024*1024*2,
//											    done: function(res){
//											    	if(res.code != '0'){
//											        	return parent.layer.msg(res.msg);
//											      	}else{
//											      		$("#p_"+infos[i]['FIELD_NAME']).attr("src",res.data.src);
//											      		$("#a_"+infos[i]['FIELD_NAME']).val(res.data.src);
//											      	}
//											    }
//											});
//										},500);		
//										
//										//uploadA[index] = ["p_"+infos[i]['FIELD_NAME'],"a_"+infos[i]['FIELD_NAME']];
//									break;
//									case "6" :
//									 	$_html += '<input id="a_'+infos[i]['FIELD_NAME']+'" name="'+infos[i]['FIELD_NAME']+'" title="'+infos[i]['FIELD_DESC']+'" code_index="'+infos[i]['FIELD_CODE']+'"  view_type="2" onclick="show_control_view(this)" readonly style="border:1px solid #93D1FF;font-family:宋体; font-size:11pt;" type="text"  class="layui-input">'	
//									break;
//									default:
//									  	
//									break;
//								}
//								
//								$(this).html($_html);
//								break;
//							}
//							
//						}
//					}
//				}
			});
		}else if(type == "2"){
			$("#print_view").find('table tr td').each(function(){
				var j_v = $(this).attr("id");
				if(typeof j_v !=='undefined'){
					if(infos != ""){
						var len = infos.length;
						for(var i=0;i<len;i++){
							if(j_v ==(infos[i]['FIELD_NAME']+'_'+ infos[i]['FIELD_ID'])){
								var $_html = "";
								switch(infos[i]['FIELD_TYPE']){
									case "301" :
									 	$_html += '<input id="a_'+infos[i]['FIELD_NAME']+'" name="'+infos[i]['FIELD_NAME']+'" title="'+infos[i]['FIELD_DESC']+'" style="border:1px solid #93D1FF;font-family:宋体; font-size:11pt;" type="text" disabled  class="layui-input">'	
									break;
									case "302" :
									  	$_html += '<textarea id="a_'+infos[i]['FIELD_NAME']+'" name="'+infos[i]['FIELD_NAME']+'" title="'+infos[i]['FIELD_DESC']+'" style="border:1px solid #93D1FF;font-family:宋体; font-size:11pt;" disabled  class="layui-textarea"></textarea>'	
									break;
									case "102" :
									 	$_html += '<input id="a_'+infos[i]['FIELD_NAME']+'" name="'+infos[i]['FIELD_NAME']+'" title="'+infos[i]['FIELD_DESC']+'" style="border:1px solid #93D1FF;font-family:宋体; font-size:11pt;" disabled type="text"  class="layui-input">'	
									break;
									case "4" :
									 	$_html += '<input id="a_'+infos[i]['FIELD_NAME']+'" name="'+infos[i]['FIELD_NAME']+'" title="'+infos[i]['FIELD_DESC']+'" code_index="'+infos[i]['FIELD_CODE']+'" disabled  view_type="1" onclick="show_control_view(this)" readonly style="border:1px solid #93D1FF;font-family:宋体; font-size:11pt;" type="text"  class="layui-input">'	
									break;
									case "5" :
									 	$_html += '<img id="p_'+infos[i]['FIELD_NAME']+'" disabled style="width:90px;" src="../../../web/images/user-default.jpg">'	
									 	$_html += '<input id="a_'+infos[i]['FIELD_NAME']+'" name="'+infos[i]['FIELD_NAME']+'" title="'+infos[i]['FIELD_DESC']+'" style="border:1px solid #93D1FF;font-family:宋体; font-size:11pt;display:none;" type="text"  class="layui-input">'	
//										setTimeout(function(){
//											upload.render({
//											    elem: '#p_'+infos[i]['FIELD_NAME'],
//											    url: "<= yii\helpers\Url::to(['default/uploadimage']); ?>",
//											    data:{},
//											    accept: 'images',
//											    exts: 'jpg|png|gif|bmp|jpeg',
//											    size: 1024*1024*2,
//											    done: function(res){
//											    	if(res.code != '0'){
//											        	return parent.layer.msg(res.msg);
//											      	}else{
//											      		$("#p_"+infos[i]['FIELD_NAME']).attr("src",res.data.src);
//											      		$("#a_"+infos[i]['FIELD_NAME']).val(res.data.src);
//											      	}
//											    }
//											});
//										},500);		
										
										//uploadA[index] = ["p_"+infos[i]['FIELD_NAME'],"a_"+infos[i]['FIELD_NAME']];
									break;
									case "6" :
									 	$_html += '<input id="a_'+infos[i]['FIELD_NAME']+'" name="'+infos[i]['FIELD_NAME']+'" title="'+infos[i]['FIELD_DESC']+'" code_index="'+infos[i]['FIELD_CODE']+'"  view_type="2" disabled onclick="show_control_view(this)" readonly style="border:1px solid #93D1FF;font-family:宋体; font-size:11pt;" type="text"  class="layui-input">'	
									break;
									default:
									  	
									break;
								}
								
								$(this).html($_html);
								break;
							}
							
						}
					}
				}
			});
		}
		
//		console.log(uploadA)
//		if(uploadA != ""){
//			var len = uploadA.length;
//			for(var j=0;j<len;j++){
//				var aa = uploadA[j][0];
//				var bb = uploadA[j][1];
//				upload.render({
//				    elem: '#'+aa,
//				    url: "<= yii\helpers\Url::to(['default/uploadimage']); ?>",
//				    data:{},
//				    accept: 'images',
//				    exts: 'jpg|png|gif|bmp|jpeg',
//				    size: 1024*1024*2,
//				    done: function(res){
//				    	if(res.code != '0'){
//				        	return parent.layer.msg(res.msg);
//				      	}else{
//				      		$("#"+aa).attr("src",res.data.src);
//				      		$("#"+bb).val(res.data.src);
//				      	}
//				    }
//				});
//			}
//		}
		
	});
}

function open_mul_record(th){
	alert($(th).attr('name'));
}
</script>	
</body>
</html>
<?php $this->endPage() ?>