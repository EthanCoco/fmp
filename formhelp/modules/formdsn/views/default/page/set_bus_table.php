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

<div style="padding: 10px;">
	<div class="layui-side-formdsn" style="width: 200px;border:#fff;border-right: 1px solid #93D1FF;border-bottom: 1px solid #93D1FF;">
	    <div class="layui-side-scroll" style="height: 100%;width: 200px;">
	      	<div class="ztree" id="flow_node_tree">
	      		
	      	</div>
	    </div>
	</div>
	<div class="layui-side-formdsn" style="width: auto;border:#fff;border-right: 1px solid #93D1FF;border-bottom: 1px solid #93D1FF;margin-left: 200px;">
	    <div class="layui-side-scroll" style="height: 100%;width: auto;">
	      	<div id="bus_designer_content">
	      		
	      	</div>
	    </div>
	</div>
	
</div>
<?php $this->endBody() ?>
	
<script>
var node_id = '';
var bus_id = '';
var dg = "#datagrid_node";
var rMenu = $("#rMenu");
var rMenu_bus = $("#rMenu_bus");
var layer = undefined;
$(function(){
	$("#bus_designer_content").css("width",$(window).width()-200 + "px");
	layui.use('layer', function(){
		layer = layui.layer;
	});
	load_flow_node_tree();
});

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
									}else if(treeNode.isRmenu == "2"){
										node_id = treeNode.pId;
										bus_id = treeNode.id;
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
	
    if (!treeNode && event.target.tagName.toLowerCase() != "button" && $(event.target).parents("a").length == 0) {  
        showRMenu("root", event.clientX, event.clientY);  
    } else if (treeNode && !treeNode.noR) {  
//      showRMenu("node", event.clientX, event.clientY);         
        showRMenu(treeNode.isRmenu, event.clientX, event.clientY); 
        
    }  
}  

//显示右键菜单  
function showRMenu(type, x, y) {  
	if(type == "1"){
		$("#rMenu ul").show();  
	    rMenu.css({"top":y+"px", "left":x+"px", "visibility":"visible"}); //设置右键菜单的位置、可见  
	    $("body").bind("mousedown", onBodyMouseDown);  
	}else{
		$("#rMenu_bus ul").show();  
	    rMenu_bus.css({"top":y+"px", "left":x+"px", "visibility":"visible"}); //设置右键菜单的位置、可见  
	    $("body").bind("mousedown", onBodyMouseDown);  
	}
    
}

//隐藏右键菜单  
//function hideRMenu() {  
//  if (rMenu) rMenu.css({"visibility": "hidden"}); //设置右键菜单不可见  
//  $("body").unbind("mousedown", onBodyMouseDown);  
//}  

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
function operate_bus_table(){
	parent.layer.open({
		type:2,
		title:'新建业务表',
		area:["300px","180px"],
		content:"<?= yii\helpers\Url::to(['default/operatebustable']); ?>"+"?nodeID="+node_id,
		btn:['确定','关闭'],
		yes: function(index){
			parent.$('#layui-layer-iframe'+index)[0].contentWindow.operate_bus_table_sure(); 
       	},
       	btn2:function(index){
       		layer.close(index); 
       	}
	});
}
</script>	
</body>
</html>
<?php $this->endPage() ?>