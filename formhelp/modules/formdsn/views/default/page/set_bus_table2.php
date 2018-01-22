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

<!--<link href="../../../web/formdesign/css/bootstrap/css/bootstrap.css?2023" rel="stylesheet" type="text/css" />-->
<!--[if lte IE 6]>
<link rel="stylesheet" type="text/css" href="../../../web/formdesign/css/bootstrap/css/bootstrap-ie6.css?2023">
<![endif]-->
<!--[if lte IE 7]>
<link rel="stylesheet" type="text/css" href="../../../web/formdesign/css/bootstrap/css/ie.css?2023">
<![endif]-->
<link href="../../../web/formdesign/css/site.css?2023" rel="stylesheet" type="text/css" />
	
<style>
	.star{color:red;}
	div#rMenu {position:absolute; visibility:hidden; top:0; text-align: left;padding:4px;z-index: 999999;box-shadow: #E0E0E0 1px 1px 1px 1px;}   
    div#rMenu a{  
        padding: 3px 15px 3px 15px;  
        background-color:#FFF;  
        vertical-align:middle; 
        display:block; 
    }
    div#rMenu_bus {position:absolute; visibility:hidden; top:0; text-align: left;padding:4px;z-index: 999999;box-shadow: #E0E0E0 1px 1px 1px 1px;}   
    div#rMenu_bus a{  
        padding: 3px 15px 3px 15px;  
        background-color:#FFF;  
        vertical-align:middle; 
        display:block; 
    }
    
    .field_list{
		list-style:none;
		padding-top:10px;
		height:100%;
	}
    .item{
    	padding:0 10px;
    	width:180px;
    	background: #F3F3F3;
    	display: inline-block;
    	height: 30px;
    	line-height: 30px;
    	border-bottom: 1px dashed #C9C9C9;
    	font-size: 12px;
    	color: #000000;
    }
    .item:hover{
    	background: #E0E0E0;
    }
</style>
</head>
<body>
<?php $this->beginBody() ?>
<div id="rMenu">  
    <a href="javascript:;" onclick="operate_bus_table(0)">新建业务表</a>  
</div>

<div id="rMenu_bus">  
    <a href="javascript:;" onclick="operate_bus_table(1)">修改</a>  
    <a href="javascript:;" onclick="operate_bus_table(2)">删除</a>
</div>

<div style="padding: 10px;position: relative;">
	<div class="layui-side-formdsn" style="width: 200px;border:#fff;border-right: 1px solid #93D1FF;border-bottom: 1px solid #93D1FF;">
	    <div class="layui-side-scroll" style="height: 100%;width: 200px;">
	      	<div class="ztree" id="flow_node_tree" style="border-bottom: 1px solid #93D1FF;height: 500px;">
	      		
	      	</div>
	      	
	      	<div class="ztree" id="flow_node_tree_table">
	      		
	      	</div>
	    </div>
	</div>
	
	<div class="layui-side-formdsn" style="overflow-x:visible;   width: 200px;border:#fff;border-right: 1px solid #93D1FF;border-bottom: 1px solid #93D1FF;margin-left: 200px;">
	    <div class="layui-side-scroll" style="height: 100%;width: 200px;">
	      	<div id="bus_table_field_div">
	      		
	      	</div>
	    </div>
	</div>
	
	<div id="table_excel_content" class="layui-side-formdsn" style="display: none; width: auto;border:#fff;border-right: 1px solid #93D1FF;border-bottom: 1px solid #93D1FF;margin-left: 400px;">
	    <div class="layui-side-scroll" style="height: 100%;width: auto;">
	      	<div id="bus_designer_content" style="z-index:1000;height: 697px;position: relative;">
				<div  style="text-align: center;">
					<center>
						<div id="file_contents" style="height: 640px;z-index:1000;position: relative;text-align: center;">
							<!--start-->
							<center>
								<div class="span10" style="text-align: center;margin: 0 auto;width: auto;margin-left: 5px;">
									<script id="myFormDesign" type="text/plain" style="width:100%;">
									
									</script>
								</div>
							</center>
							<!--end-->
						</div>
					</center>
				</div>
				<div style="position:fixed;bottom:0;z-index:1001;padding: 10px;background: #EEEEEE;border-bottom: 1px solid #93D1FF;height: 37px;width: 100%;">
					<button onclick="leipiFormDesign.exec('listctrl');" class="layui-btn">列表控件</button>
					<div class="layui-btn-group" style="">
					  	<button onclick="print_view(0)" class="layui-btn">查看预览</button>
					  	<button onclick="print_view(1)" class="layui-btn">编辑预览</button>
					  	<button onclick="print_view(2)" class="layui-btn">审核预览</button>
					</div>
				</div>	 
	      	</div>
	    </div>
	</div>
</div>
<script type="text/javascript" charset="utf-8" src="../../../web/formdesign/js/ueditor/ueditor.config.js?2023"></script>
<script type="text/javascript" charset="utf-8" src="../../../web/formdesign/js/ueditor/ueditor.all.js?2023"> </script>
<script type="text/javascript" charset="utf-8" src="../../../web/formdesign/js/ueditor/lang/zh-cn/zh-cn.js?2023"></script>
<script type="text/javascript" charset="utf-8" src="../../../web/formdesign/js/ueditor/formdesign/leipi.formdesign.v4.js?2023"></script>



<?php $this->endBody() ?>


	
<script>
var node_id = '';
var bus_id = '';
var bus_table_name = '';
var dg = "#datagrid_node";
var rMenu = $("#rMenu");
var rMenu_bus = $("#rMenu_bus");
var layer = undefined;
$(function(){
	$("#bus_designer_content").css("width",$(window).width()-400 + "px");
	layui.use('layer', function(){
		layer = layui.layer;
	});
	load_flow_node_tree();
	load_table_tree_bus();
	
	$('#bus_designer_content').droppable({
		onDragEnter:function(e,source){
			$(source).draggable('options').cursor='auto';
		},
		onDragLeave:function(e,source){
			$(source).draggable('options').cursor='not-allowed';
		},
		onDrop:function(e,source){
			console.log($(source).attr('name'))
			console.log(source)
		}
	});
	
});

//流程业务表树
function load_flow_node_tree(){
	layui.use('layer',function(){
		var layer = layui.layer;
		$.getJSON("<?= yii\helpers\Url::to(['default/flownodetreetable']); ?>",{'flowID':parent.flow_node_id}, function(json){
			if(json.result){
				var treeData = json.infos;
				var setting = {
				    		edit : {
								enable : false,
								showRemoveBtn: false,
								showRenameBtn: false
							},
							data : {
								simpleData : {
									enable : true,
									idKey : "id",
									pIdKey : "pId"
								}
							},
							check:{
								enable:false
							},
							view : {
								dblClickExpand : false,	
								showLine : true,
								selectedMulti : false
							},
							callback: {
								onRightClick : OnRightClick,
								beforeClick:function(treeId,treeNode,clickFlag){
									if(treeNode.isRmenu == "0")
								    	return false;
								},
								onClick:function(event, treeId, treeNode){
									if(treeNode.isRmenu == "1"){
										node_id = treeNode.id;
										bus_id = '';
										$("#table_excel_content").css("display","none");
									}else if(treeNode.isRmenu == "2"){
										node_id = treeNode.pId;
										bus_id = treeNode.id;
										$("#table_excel_content").css("display","");
										//init_upload_file();
										init_editor_info();
									}
								}
							}
						};
		    	
		    	var treeObj = $.fn.zTree.init($("#flow_node_tree"), setting, treeData);// 生成树形结构
		    	var zTree = $.fn.zTree.getZTreeObj("flow_node_tree");
		    	zTree.expandAll(true); 
		    	var node_obj = treeObj.getNodesByFilter(function(node){
		    		return node.isNode == 1;
		    	}, true); 
		    	
		    	if(node_obj == null){
		    		
		    	}else{
		    		zTree.selectNode(node_obj);
		    		node_id = node_obj.id;
		    	}
			}else{
				layer.alert(json.msg);
			}
		});
	});
}

// 在ztree上的右击事件  
function OnRightClick(event, treeId, treeNode) { 
	if(treeNode.isRmenu == "0")
		return false;
	var zTree = $.fn.zTree.getZTreeObj("flow_node_tree");
	zTree.selectNode(treeNode);
	if(treeNode.isRmenu == "1"){
		node_id = treeNode.id;
		bus_id = '';
	}else if(treeNode.isRmenu == "2"){
		node_id = treeNode.pId;
		bus_id = treeNode.id;
	}
	
    if (!treeNode && event.target.tagName.toLowerCase() != "button" && $(event.target).parents("a").length == 0) {  
        showRMenu("root", event.clientX, event.clientY);  
    } else if (treeNode && !treeNode.noR) {  
        showRMenu(treeNode.isRmenu, event.clientX, event.clientY); 
    }  
}  

//显示右键菜单  
function showRMenu(type, x, y) { 
	if(type == "1"){
		$("#rMenu ul").show();  
		$("#table_excel_content").css("display","none");
	    rMenu.css({"top":y+"px", "left":x+"px", "visibility":"visible"}); //设置右键菜单的位置、可见  
	    $("body").bind("mousedown", onBodyMouseDown);  
	}else{
		if(bus_id == '')
			return;
		$("#rMenu_bus ul").show(); 
		$("#table_excel_content").css("display","");
		//init_upload_file(); 
		init_editor_info();
	    rMenu_bus.css({"top":y+"px", "left":x+"px", "visibility":"visible"}); //设置右键菜单的位置、可见  
	    $("body").bind("mousedown", onBodyMouseDown);  
	}
    
}

//隐藏右键菜单  
function hideRMenu() {  
    if (rMenu) rMenu.css({"visibility": "hidden"}); //设置右键菜单不可见  
    $("body").unbind("mousedown", onBodyMouseDown);  
    
    if (rMenu_bus){
    	rMenu_bus.css({"visibility": "hidden"}); 
    	$("body").unbind("mousedown", onBodyMouseDown);  
    	bus_id = '';
    }
}  

//鼠标按下事件  
function onBodyMouseDown(event){  
    if (!(event.target.id == "rMenu" || $(event.target).parents("#rMenu").length>0)) {  
        rMenu.css({"visibility" : "hidden"});  
    }  
    if (!(event.target.id == "rMenu_bus" || $(event.target).parents("#rMenu_bus").length>0)) {  
        rMenu_bus.css({"visibility" : "hidden"});  
    }
} 

//操作业务表
function operate_bus_table(type){
	var msg = ['新建','修改','删除'];
	if(type != "2"){
		parent.layer.open({
			type:2,
			title:msg[type]+'业务表',
			area:["300px","180px"],
			content:"<?= yii\helpers\Url::to(['default/operatebustable']); ?>"+"?nodeID="+node_id+"&busName="+bus_id,
			btn:['确定','关闭'],
			yes: function(index){
				parent.$('#layui-layer-iframe'+index)[0].contentWindow.operate_bus_table_sure(); 
	       	},
	       	btn2:function(index){
	       		layer.close(index); 
	       	}
		});
	}else{
		parent.layer.confirm("确定"+msg[type]+"么？",function(index){
			$.getJSON("<?= yii\helpers\Url::to(['default/delbustable']); ?>",
			{
				"bus_id":bus_id
			},function(json){
				if(json.result){
					layer.close(index);
					parent.layer.msg(json.msg);
					load_flow_node_tree(); 
					hideRMenu();
				}else{
					parent.layer.alert(json.msg);
				}
			});
			
		});
	}
}

//业务表树
function load_table_tree_bus(){
	layui.use('layer',function(){
		var layer = layui.layer;
		$.getJSON("<?= yii\helpers\Url::to(['default/tabletree']); ?>",
		{
			"flow_node_id":parent.flow_node_id,
		}, function(json){
			if(json.result){
				var treeData = json.infos;
				var setting = {
				    		edit : {
								enable : false,
								showRemoveBtn: false,
								showRenameBtn: false
							},
							data : {
								simpleData : {
									enable : true,
									idKey : "id",
									pIdKey : "pId"
								}
							},
							check:{
								enable:false
							},
							view : {
								dblClickExpand : false,	
								showLine : true,
								selectedMulti : false
							},
							callback: {
								beforeClick:function(treeId,treeNode,clickFlag){
									if(treeNode.isChild == "0")
								    	return false;
								},
								onClick:function(event, treeId, treeNode){
									bus_table_name = treeNode.id;
									load_table_field_bus();
								}
							}
						};
		    	
		    	var treeObj = $.fn.zTree.init($("#flow_node_tree_table"), setting, treeData);// 生成树形结构
		    	var zTree = $.fn.zTree.getZTreeObj("flow_node_tree_table");
		    	zTree.expandAll(true); 
		    	var node_obj = treeObj.getNodesByFilter(function(node){
		    		return node.isChild== 1;
		    	}, true); 
		    	
		    	if(node_obj == null){
		    		bus_table_name = "";
		    	}else{
		    		zTree.selectNode(node_obj);
		    		bus_table_name = node_obj.id;
		    		load_table_field_bus();
		    	}
			}else{
				layer.alert(json.msg);
			}
		});
	});
}

//业务表字段
function load_table_field_bus(){
	$("#bus_table_field_div").empty();
	$.getJSON("<?= yii\helpers\Url::to(['default/fieldtablelistbus']); ?>",
	{
		"flow_table_id":bus_table_name,"flow_node_id":parent.flow_node_id
	},
	function(json){
		if(json.result){
			var infos = json.infos;
			if(infos == ""){
				$("#bus_table_field_div").html("<p style='padding:5px;text-align:center;'>请先添加字段信息</p>");
			}else{
				var obj = $("#bus_table_field_div");
				var _html = '<ul class="field-list">';
				var len = infos.length;
				for(var i=0;i<len;i++){
					_html += '<li><a id="'+infos[i]['FIELD_ID']+'" href="#" name="'+infos[i]['FIELD_NAME']+'" class="item">'+infos[i]['FIELD_ID']+'='+infos[i]['FIELD_DESC']+'</a></li>';
				}
				_html += '</ul>';
				obj.html(_html);
			}
			
			$('.item').draggable({
				revert:true,
				proxy:'clone',
				onStartDrag:function(){
					$(this).draggable('options').cursor = 'not-allowed';
					$(this).draggable('proxy').css('z-index',9999);
				},
				onStopDrag:function(){
					$(this).draggable('options').cursor='move';
				}
			});
			
		}else{
			layer.alert(json.msg);
		}
	});
}

/*预览*/
function print_view(type){
	parent.layer.open({
		type:2,
		area:["800px","800px"],
		content:"<?= yii\helpers\Url::to(['default/printview']); ?>"+"?busName="+bus_id+"&flow_id="+parent.flow_node_id+"&bus_table_name="+bus_table_name+"&type="+type,
	});
}

function init_editor_info(){
	var leipiEditor = UE.getEditor('myFormDesign',{
            //allowDivTransToP: false,//阻止转换div 为p
            toolleipi:true,//是否显示，设计器的 toolbars
            textarea: 'design_content',   
            //这里可以选择自己需要的工具按钮名称,此处仅选择如下五个
           toolbars:[[
            'fullscreen', 'source', '|', 'undo', 'redo', '|','bold', 'italic', 'underline', 'fontborder', 'strikethrough',  'removeformat', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist','|', 'fontfamily', 'fontsize', '|', 'indent', '|', 'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|',  'link', 'unlink',  '|',  'horizontal',  'spechars',  'wordimage', '|', 'inserttable', 'deletetable',  'mergecells',  'splittocells']],
            //focus时自动清空初始化时的内容
            //autoClearinitialContent:true,
            //关闭字数统计
            wordCount:false,
            //关闭elementPath
            elementPathEnabled:false,
            //默认的编辑区域高度
            initialFrameHeight:600
            ///,iframeCssUrl:"css/bootstrap/css/bootstrap.css" //引入自身 css使编辑器兼容你网站css
            //更多其他参数，请参考ueditor.config.js中的配置项
        });

		$.getJSON("<?= yii\helpers\Url::to(['default/getbushtml']); ?>",
		{
			'bus_id':bus_id,
			'flow_id':parent.flow_node_id
		},
		function(json){
			if(json.result){
				leipiEditor.setContent(json.content);
			}else{
				alert(json.msg);
			}
		});

	var leipiFormDesign = {
	    /*执行控件*/
	    exec : function (method) {
	        leipiEditor.execCommand(method);
	    },
	    /*type  =  save 保存设计 versions 保存版本  close关闭 */
	    fnCheckForm : function ( type ) {
	        if(leipiEditor.queryCommandState( 'source' ))
	            leipiEditor.execCommand('source');//切换到编辑模式才提交，否则有bug
	            
	        if(leipiEditor.hasContents()){
	            leipiEditor.sync();/*同步内容*/
	            
	            formeditor=leipiEditor.getContent();
	            var formeditor = leipiEditor.getContent();
				$.post("<?= yii\helpers\Url::to(['default/savehtml']); ?>",{
					"content" : formeditor,
					"busID" : bus_id,
					"flowID" : parent.flow_node_id
				},function(json){
					if(json.result){
						layer.msg(json.msg);
					}else{
						return layer.alert(json.msg);
					}
				});
	            
	        } else {
	            alert('表单内容不能为空！')
	            $('#submitbtn').button('reset');
	            return false;
	        }
	    } ,
	    /*预览表单*/
	    fnReview : function (){
	        if(leipiEditor.queryCommandState( 'source' ))
	            leipiEditor.execCommand('source');/*切换到编辑模式才提交，否则部分浏览器有bug*/
	            
	        if(leipiEditor.hasContents()){
	            leipiEditor.sync();       /*同步内容*/
	            
				var formeditor = leipiEditor.getContent();
	//			var parse_form = this.parse_form(formeditor);
				$.post("<?= yii\helpers\Url::to(['default/savehtml']); ?>",{
					"content" : formeditor,
					"busID" : bus_id,
					"flowID" : parent.flow_node_id
				},function(json){
					if(json.result){
						var filePath = json.filePath;
	            		window.open(filePath,'mywin',"menubar=0,toolbar=0,status=0,resizable=1,left=0,top=0,scrollbars=1,width=" +(screen.availWidth-10) + ",height=" + (screen.availHeight-50) + "\"");
					}else{
						return layer.alert(json.msg);
					}
				});
	        } else {
	            return layer.alert('表单内容不能为空！');
	        }
	    }
	};
}


</script>

<script type="text/javascript">


</script>

</body>
</html>
<?php $this->endPage() ?>