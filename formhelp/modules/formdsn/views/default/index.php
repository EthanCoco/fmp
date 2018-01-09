<div class="layui-side-formdsn">
    <div class="layui-side-scroll" style="height: 100%;">
      	<div class="ztree" id="flow_tree">
      		
      	</div>
    </div>
</div>

<div class="layui-side-formdsn" style="margin-left: 200PX;">
    <div class="layui-side-scroll" style="height: 380px;">
      	<div class="ztree left_block_right" id="table_tree" style="height: 320px;display: none;">
      		
      	</div>
      	<div style="border-top: 1px solid #93D1FF;height: 45px;display: none;" class="left_block_right">
      		<div class="layui-form-item layui-form-center">
			    <div class="layui-input-block1" style="margin-top: 5px;margin-left: -20px;">
			      	<button onclick="table_modify_name()" class="layui-btn layui-btn-primary">修改</button>
			    </div>
		  	</div>
      	</div>
    </div>
    <div class="layui-side-scroll" style="height: auto;">
      	<div style="border-top: 1px solid #93D1FF;display: none;" class="left_block_right">
  			<fieldset class="layui-elem-field layui-field-title" style="margin-top: 10px;">
			  <legend>添加表</legend>
			</fieldset>
			<div class="layui-form">
				<div class="layui-form-item">
				    <div class="layui-inline" >
				      	<label class="layui-form-label" style="width:40px">表属性</label>
				      	<div class="layui-input-inline" style="width: 100px;">
				        	<select name="table_type" id="table_type" lay-filter="table_type">
				          		<option value="1">主表</option>
				          		<option value="2">副表</option>
				        	</select>
				    	</div>
					</div>
				</div>
				<div class="layui-form-item">
				    <div class="layui-inline" >
				      	<label class="layui-form-label" style="width:40px">表名称</label>
				      	<div class="layui-input-inline" style="width: 100px;">
			    			<input name="tablename" id="tablename" placeholder="请输入正整数" disabled="disabled" class="layui-input" type="text">
				    	</div>
					</div>
				</div>
				<div class="layui-form-item">
				    <div class="layui-inline" >
				      	<label class="layui-form-label" style="width:40px">表描述</label>
				      	<div class="layui-input-inline" style="width: 100px;">
			    			<input name="tabledesc" id="tabledesc"  class="layui-input" type="text">
				    	</div>
					</div>
				</div>
				<p class="star" style="padding:0 10px;">注：主表默认为[BZ_ZGJOB_]前缀自动生成，副表默认前缀为[主表_SE_]</p>
				
				<hr class="layui-bg-gray">
			  	<div class="layui-form-item layui-form-center">
				    <div class="layui-input-block1" style="margin-left: -20px;height: 40px;">
				      	<button onclick="flow_add_table()" class="layui-btn layui-btn-primary">添加</button>
				    </div>
			  	</div>
				
  			</div>
    	</div>
    </div>
</div>

<div class="layui-body-fmp" id="field_grid_table_list_parent">
	<div id="field_grid_table_list">
		
	</div>
</div>

<script>
var flow_node_id = "";
var flow_table_id = "";
var editIndex = undefined;
var tempEditIndex = undefined;
$(function(){
	layui.use('form',function(){
		var form = layui.form;
		
		form.on('select(table_type)', function(data){
		  	var table_type_val = data.value;
		  	if(table_type_val == "1"){
		  		$("#tablename").attr("disabled","disabled");
		  	}else{
		  		$("#tablename").removeAttr("disabled");
		  	}
		});
		
		form.render('select');
	});
	load_flow_tree();
});

function load_flow_tree(){
	$.getJSON("<?= yii\helpers\Url::to(['default/flowtree']); ?>",{}, function(json){
		var treeData = json;
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
							flow_node_id = treeNode.id;
							load_table_tree();
						}
					}
				};
    	
    	var treeObj = $.fn.zTree.init($("#flow_tree"), setting, treeData);// 生成树形结构
    	var zTree = $.fn.zTree.getZTreeObj("flow_tree");
    	zTree.expandAll(true); 
    	var node_obj = treeObj.getNodesByFilter(function(node){
    		return node.isChild== 1;
    	}, true); 
    	
    	if(node_obj == null){
    		flow_node_id = "";
    	}else{
    		flow_node_id = node_obj.id;
    		$(".left_block_right").css("display","");
    		load_table_tree();
    	}
	});
}

function load_table_tree(){
	$.getJSON("<?= yii\helpers\Url::to(['default/tabletree']); ?>",
	{
		"flow_node_id":flow_node_id,
	}, function(json){
		var treeData = json;
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
							flow_table_id = treeNode.id;
							load_table_field();
						}
					}
				};
    	
    	var treeObj = $.fn.zTree.init($("#table_tree"), setting, treeData);// 生成树形结构
    	var zTree = $.fn.zTree.getZTreeObj("table_tree");
    	zTree.expandAll(true); 
    	var node_obj = treeObj.getNodesByFilter(function(node){
    		return node.isChild== 1;
    	}, true); 
    	
    	if(node_obj == null){
    		flow_table_id = "";
    	}else{
    		flow_table_id = node_obj.id;
    		load_table_field();
    	}
	});
}

function flow_add_table(){
	layui.use('layer',function(){
		if(flow_node_id == ""){
			return;
		}
		
		var table_type = $("#table_type").val();
		var table_name = "";
		if(table_type == "2"){
			table_name = $("#tablename").val().trim();
			if(!validatePosNum(table_name)){
				return layer.alert("请输入正整数");
			}
		}
		var table_desc = $("#tabledesc").val().trim();
		layer.confirm("确定添加该表么？", function(index){
		  	$.getJSON("<?= yii\helpers\Url::to(['default/flowtableadd']); ?>",
			{
				"flow_node_id":flow_node_id,
				"table_type":table_type,
				"table_name":table_name,
				"table_desc":table_desc
			}, function(json){
				if(json.result){
					layer.msg(json.msg);
					layer.close(index);
				}else{
					layer.alert(json.msg);
				}
			});
		});
	});
}

function table_modify_name(){
	layui.use('layer',function(){
		if(flow_table_id == ""){
			return;
		}
		layer.open({
    		type:2,
    		title:'修改',
    		area:["350px","230px"],
    		content:"<?= yii\helpers\Url::to(['default/modtabledesc']); ?>"+"?flow_table_id="+flow_table_id,
    		btn:['确定','取消'],
    		yes: function(){
    			$("iframe[id*='layui-layer-iframe'")[0].contentWindow.table_modify_name_desc_sure(); 
	        },
    		btn2:function(){
    			layer.closeAll();
    		}
	    });
	});
}

function load_table_field(){
    $('#field_grid_table_list').datagrid({
        width:'auto',
        height:'auto',
	    url:"<?= yii\helpers\Url::to(['default/fieldtablelist']); ?>",
	    method: "get",
	    queryParams: {"flow_table_id":flow_table_id,"flow_node_id":flow_node_id},
	    striped: true,
	    fixed: true,
	    fitColumns: false,
	    singleSelect: true,
        pagination: false,  
	    rownumbers: true, 
	    onClickCell: onClickCell,
    	onEndEdit: onEndEdit,
	    toolbar: 
	    [{
			iconCls: 'icon-add',
			text:'添加',
			handler: function(){
				append();
			}
		},'-','-','-',{
			iconCls: 'icon-save',
			text:'保存',
			handler: function(){
				accept();
			}
		},'-',{
			iconCls: 'icon-redo',
			text:'取消',
			handler: function(){
				reject();
			}
		}],
        columns:[[
            {field:'FIELD_NAME',title:'字段名',width:'10%',align:'center'},
            {field:'FIELD_DESC',title:'中文名',width:'10%',align:'center',
            	editor:{
					type:'textbox',
					options:{
						required:true
					} 
				}
            },
            {field:'FIELD_TYPE',title:'类型',width:'15%',align:'center',
            	editor:{
					type:'combotree',
					options:{
						valueField:'id',
						textField:'text',
						panelHeight:200,
						editable: false,
						required:true,
						data :[{
						    "id":"0",
						    "text":"类型选择",
						    "isChild":0,
						    "checked":"disabled",
						    "children":[{
						        "id":"1",
						        "text":"日期",
						        "isChild":0,
						        "checked":"disabled",
						        "children":[{
						        	"isChild":1,
						            "id":"101",
						            "text":"101=年"
						        },{
						            "id":".102",
						            "text":"102=年月"
						        },{
						            "id":"103",
						            "text":"103=年月日"
						        },{
						            "id":"104",
						            "text":"104=年月日时分秒"
						        },{
						            "id":"105",
						            "text":"105=年月日时分"
						        },{
						            "id":"106",
						            "text":"106=年月日时"
						        },{
						            "id":"107",
						            "text":"107=时分秒"
						        },{
						            "id":"108",
						            "text":"108=时分"
						        },{
						            "id":"109=109",
						            "text":"分"
						        },{
						            "id":"110",
						            "text":"110=秒"
						        }]
						    },{
						        "id":"2",
						        "text":"数字",
						        "children":[{
						            "id":"201",
						            "text":"201=整数"
						        },{
						            "id":"202",
						            "text":"202=实数"
						        }]
						    },{
						        "id":"3",
						        "text":"文本",
						        "children":[{
						            "id":"301",
						            "text":"301=文本框"
						        },{
						            "id":"302",
						            "text":"302=文本域"
						        }]
						    },{
						    	"id":"4",
						        "text":"4=代码",
						    },{
						    	"id":"5",
						        "text":"5=图像",
						    }]
						}],
						onBeforeSelect: function (node) {
			                var tree = $(this).tree;  
							var isLeaf = tree('isLeaf', node.target);  
							if (!isLeaf) {  
					           return false;
					        }  
			            },
						onSelect : function(node){
							if(node.id == "4"){
								var ed = $('#field_grid_table_list').datagrid("getEditor",{
									index:editIndex,
									field:"FIELD_CODE"
								});
								$(ed.target).textbox({editable:true,required:true,disabled:false});
							}else{
								var ed = $('#field_grid_table_list').datagrid("getEditor",{
									index:editIndex,
									field:"FIELD_CODE"
								});
								$(ed.target).textbox({editable:false,required:false,disabled:true,value:""});
							}
                       	},
					}
				}
            },
            {field:'FIELD_CODE',title:'代码值',width:'15%',align:'center',
            	editor:{
					type:'textbox',
					options:{
						editable:false,
						disabled:true
					} 
				}
            },
            {field:'FIELD_VERIFY',title:'校验函数',width:'15%',align:'center',
            	editor:{
					type:'combobox',
					options:{
						valueField:'id',
						textField:'text',
						value:"",
						data :[{}],
						panelHeight:200,
						editable: false,
//						required:true
					}
				}
            },
            {field:'FIELD_BELONG_NODE',title:'所属环节',width:'15%',align:'center',
            	editor:{
					type:'combobox',
					options:{
						valueField:'id',
						textField:'text',
						value:"",
						data :[],
						panelHeight:200,
						editable: false,
//						required:false
					}
				}
            },
            {field:'FIELD_GLOBE_REQUIRE',title:'是否必填',width:'9%',align:'center',
            	editor:{
					type:'combobox',
					options:{
						valueField:'id',
						textField:'text',
						data :[{'id':1,'text':'1=是'},{'id':2,'text':'2=否'}],
						panelHeight:'auto',
						editable: false,
						required:false
					}
				}
            },
            {field:'OPERATE',title:'操作',width:'9%',align:'center',
            	formatter:function(value,index,row){
            		return '<a href="javascript:removeit()"><i class="layui-icon" style=" color: red;">&#xe640;</i></a>';
            	}
            },
        ]],
        onDblClickRow: function(index,row){
        	
	    },
        onLoadSuccess: function(data){
			
		}
    });
}
function endEditing() {
   	if (editIndex == undefined){
   		return true
   	}
	$('#field_grid_table_list').datagrid('endEdit', editIndex);
	editIndex = undefined;
	tempEditIndex = editIndex;
	return true;
}
function onClickCell(index, field){
	layui.use('layer',function(){
		var layer = layui.layer;
	
		if(editIndex !== undefined){
			if(!$('#field_grid_table_list').datagrid('validateRow', editIndex)){
				return layer.msg("存在必填项未填写");
			}
		}
		
	   	if (editIndex != index) {
		    if (endEditing()) {
		     	$('#field_grid_table_list').datagrid('selectRow', index).datagrid('beginEdit', index);
		     	var ed = $('#field_grid_table_list').datagrid('getEditor', { index: index, field: field });
		     	if (ed) {
		      		($(ed.target).data('textbox') ? $(ed.target).textbox('textbox') : $(ed.target)).focus();
		     	}
		     	editIndex = index;
		    } else {
		     	setTimeout(function () {
		      		$('#field_grid_table_list').datagrid('selectRow', editIndex);
		     	}, 0);
		    }
	   	}
   	});
}
function append(){
   	var index = $('#field_grid_table_list').datagrid('getRowIndex', $('#field_grid_table_list').datagrid('getSelected'));
   	if(index == -1)
    	index = 0;
	$('#field_grid_table_list').datagrid("insertRow", {
		index: index+1,
		row: {FIELD_NAME:'kkk'}
	});
}
function removeit(){
   	if (editIndex == undefined)
   		return;
   	$('#field_grid_table_list').datagrid('selectRow', editIndex);
   
    $('#field_grid_table_list').datagrid('cancelEdit', editIndex).datagrid('deleteRow', editIndex);
   	editIndex = undefined;
}
function reject(){
   	$('#field_grid_table_list').datagrid('rejectChanges');
   	editIndex = undefined;
}
function onEndEdit(index, row){
   	var ed = $(this).datagrid('getEditor', {
    	index: index,
    	field: 'FIELD_TYPE'
   	});
   	row.FIELD_TYPE = $(ed.target).combobox('getText');
   	
   	var ed = $(this).datagrid('getEditor', {
    	index: index,
    	field: 'FIELD_GLOBE_REQUIRE'
   	});
   	row.FIELD_GLOBE_REQUIRE = $(ed.target).combobox('getText');
}
function accept(){
	layui.use('layer',function(){
		var layer = layui.layer;
		layer.confirm("确定要保存么？必填项未填写的将不会保存",function(index){
		   	if (endEditing()){
		   		var $dg = $('#field_grid_table_list');
		    	var rows = $dg.datagrid('getChanges');
		    	console.log(rows);
		    	if (rows.length) {
			     	var inserted = $dg.datagrid('getChanges', "inserted");
			     	var deleted = $dg.datagrid('getChanges', "deleted");
			     	var updated = $dg.datagrid('getChanges', "updated");
			     	var effectRow = new Object();
			     	if (inserted.length) {
			      		effectRow["inserted"] = JSON.stringify(inserted);
			     	}
			     	if (deleted.length) {
			      		effectRow["deleted"] = JSON.stringify(deleted);
			     	}
			     	if (updated.length) {
			      		effectRow["updated"] = JSON.stringify(updated);
			     	}
			     	console.log(effectRow);
		    	}
		    }
		    
    		layer.close(index);
		});
	});
    
    
}
</script>