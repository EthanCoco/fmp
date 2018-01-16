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
</style>
</head>
<body>
<?php $this->beginBody() ?>
<div style="padding: 10px;">
	<div class="layui-side-formdsn" style="width: 350px;border:#fff;border-right: 1px solid #93D1FF;border-bottom: 1px solid #93D1FF;">
	    <div class="layui-side-scroll" style="height: 100%;width: 350px;">
	      	<div class="ztree" id="flow_node_tree">
	      		
	      	</div>
	    </div>
	</div>
	<div class="layui-side-formdsn" style="width: 430px;border:#fff;border-bottom: 1px solid #93D1FF;margin-left: 352px;">
	    <div class="layui-side-scroll" style="height: 100%;width: 430px;">
	      	<div id="datagrid_node">
	      		
	      	</div>
	    </div>
	</div>
</div>
<?php $this->endBody() ?>
	
<script>
var node_id = '';
var datagrid_row = undefined;
var dg = "#datagrid_node";
var layer = undefined;
$(function(){
	layui.use('layer', function(){
		layer = layui.layer;
	});
	load_flow_node_tree();
});

function load_flow_node_tree(){
	layui.use('layer',function(){
		var layer = layui.layer;
		$.getJSON("<?= yii\helpers\Url::to(['default/flownodetree']); ?>",{'flowID':parent.flow_node_id}, function(json){
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
									node_id = treeNode.id;
									load_table_field_node();
								}
							}
						};
		    	
		    	var treeObj = $.fn.zTree.init($("#flow_node_tree"), setting, treeData);// 生成树形结构
		    	var zTree = $.fn.zTree.getZTreeObj("flow_node_tree");
		    	zTree.expandAll(true); 
		    	var node_obj = treeObj.getNodesByFilter(function(node){
		    		return node.isChild == 1;
		    	}, true); 
		    	
		    	if(node_obj == null){
		    		
		    	}else{
		    		zTree.selectNode(node_obj);
		    		node_id = node_obj.id;
		    		load_table_field_node();
		    	}
			}else{
				layer.alert(json.msg);
			}
		});
	});
}

function load_table_field_node(){
    $('#datagrid_node').datagrid({
        width:'auto',
        height:'auto',
	    url:"<?= yii\helpers\Url::to(['default/nodeorderlist']); ?>",
	    method: "get",
	    queryParams: {'flowID':parent.flow_node_id,"nodeID":node_id},
	    striped: true,
	    fixed: true,
	    fitColumns: false,
	    singleSelect: true,
        pagination: false,  
	    rownumbers: true, 
	    toolbar: 
	    [{
			iconCls: 'icon-up',
			text:'上移',
			handler: function(){
				moveupRow(dg, datagrid_row);
			}
		},'-',{
			iconCls: 'icon-down',
			text:'下移',
			handler: function(){
				movedownRow(dg, datagrid_row);
			}
		},'-',{
			iconCls: 'icon-save',
			text:'保存',
			handler: function(){
				saveAllRows();
			}
		},'-',{
			iconCls: 'icon-redo',
			text:'重置',
			handler: function(){
				$(dg).datagrid("rejectChanges");
				datagrid_row = undefined;
			}
		}],
        columns:[[
        	{field:'FIELD_ID',title:'',width:'10%',align:'center',hidden:true},
            {field:'FIELD_NAME',title:'字段名',width:'10%',align:'center',hidden:true},
            {field:'FIELD_DESC',title:'中文名',width:'98%',align:'center'},
        ]],
        onClickRow:function(index, row){
        	datagrid_row = row;
        },
        onLoadSuccess: function(data){
		
		}
    });
}

/*向上移动一行 */
function moveupRow(dg, row) {  
	if(row == undefined){
		return layer.msg("请先选择要移动的行");
	}
    var datagrid = $(dg);  
    var index = datagrid.datagrid("getRowIndex", row);  
    if (isFirstRow(dg, row)) {  
        layer.msg("已经是第一条！");  
        return;  
    }  
    datagrid.datagrid("deleteRow", index);  
    datagrid.datagrid("insertRow", {  
        index : index - 1, // 索引从0开始  
        row : row  
    });  
    datagrid.datagrid("selectRow", index - 1);  
}

/*向下移动一行 */ 
function movedownRow(dg, row) {
	if(row == undefined){
		return layer.msg("请先选择要移动的行");
	} 
    var datagrid = $(dg);  
    var index = datagrid.datagrid("getRowIndex", row);  
    if (isLastRow(dg, row)) {  
        layer.msg("已经是最后一条！");  
        return;  
    }  
    datagrid.datagrid("deleteRow", index);  
    datagrid.datagrid("insertRow", {  
        index : index + 1, // 索引从0开始  
        row : row  
    });  
    datagrid.datagrid("selectRow", index + 1);  
} 

/*是否是第一条数据 */ 
function isFirstRow(dg, row) {  
    var index = $(dg).datagrid("getRowIndex", row);  
    if (index == 0) {  
        return true;  
    }  
    return false;  
}

/*是否是最后一条数据 */  
function isLastRow(dg, row) {  
    var rowNum = $(dg).datagrid("getRows").length;  
    var index = $(dg).datagrid("getRowIndex", row);  
    if (index == (rowNum - 1)) {  
        return true;  
    }  
    return false;  
} 

/*保存信息*/
function saveAllRows(){
	var string_data = JSON.stringify($(dg).datagrid("getRows"));
	if(string_data == ""){
		return layer.msg('没有信息，不需要保存');
	}
	$.post("<?= yii\helpers\Url::to(['default/savenodeorder']); ?>",
		{
			'order_data':string_data,
			'flowID':parent.flow_node_id,
			"nodeID":node_id	
		},
	function(json){
		if(json.result){
			layer.msg(json.msg);
			$(dg).datagrid("reload");
		}else{
			layer.alert(json.msg);
		}
	},'json');
}
</script>	
</body>
</html>
<?php $this->endPage() ?>