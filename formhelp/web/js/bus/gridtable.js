/*人才招聘-招聘设置-招聘列表*/
var __rczp_zpsz_stepIndex_one_urls__ = {};

function init_stepIndex_one_grid(stepIndex_one_urls){
	__rczp_zpsz_stepIndex_one_urls__ = stepIndex_one_urls;
    $('#stepIndex_one').datagrid({
        width:'auto',
        height:'auto',
	    url:stepIndex_one_urls.__list_url,
	    method: "post",
	    queryParams: {},
	    striped: true,
	    fixed: true,
	    fitColumns: false,
	    singleSelect: false,
        pagination: true,  
	    rownumbers: true, 
	    pageNumber:1,
	    pageSize:20,
	    pageList:[20,50,100,200],
        columns:[[
        	{field:'ck',checkbox:true,width:'10%'},
            {field:'recYear',title:'招聘年度',width:'10%',align:'center',},
            {field:'recBatch',title:'招聘批次',width:'10%',align:'center',},
            {field:'recStart',title:'报名起始时间',width:'15%',align:'center',},
            {field:'recEnd',title:'报名终止时间',width:'15%',align:'center',},
            {field:'recViewPlace',title:'考试地点',width:'15%',align:'center',},
            {field:'recHealthPlace',title:'体检地点',width:'15%',align:'center',},
            {field:'recDefault',title:'是否进行中',width:'9%',align:'center',
            	formatter:function(value,row,index){
            		return value == "1" ? "是" : (value == 2 ? "已结束" : "否");
            	}
            },
            {field:'operation',title:'操作',width:'10%',align:'center',
            	formatter:function(value,row,index){
            		if(row.recDefault == "1" || row.recDefault == "2"){
            			//return "<button onclick=\"javascript: return;\" class=\"layui-btn layui-btn-primary layui-btn-small  layui-btn-radius  layui-btn-disabled \">发布</button>";
            			return "";
            		}else{
            			return "<button onclick=\"pubRecruit("+row.recID+",'"+row.recYear+"','"+row.recBatch+"','"+row.recEnd+"')\" class=\"layui-btn layui-btn-primary layui-btn-small  layui-btn-radius \">发布</button>";
            		}
            	}
            },
        ]],
        onDblClickRow: function(index,row){
        	layui.use('layer', function(){
				var layer = layui.layer;
	        	var recDefault = row.recDefault;
	        	if(recDefault == 1){
	        		layui.layer.alert("该招聘正在进行中，不允许修改");
					return;
	        	}else if(recDefault == 2){
	        		layui.layer.alert("该招聘已结束归档，不允许修改");
					return;
	        	}
	        	
	        	layer.open({
	        		type:2,
	        		title:'修改招聘年度',
	        		area:["600px",'420px'],
	        		content:stepIndex_one_urls.__repair_url+"&flag=mod&recID="+row.recID
	        	});
	        	
	        	
        	});
	    },
        onLoadSuccess: function(data){
			$('#stepIndex_one').datagrid('resize',{
	    		height: $(window).height()-124-25
	    	});
		    $("#stepIndex_one").datagrid('getPager').pagination({buttons:[
				{
				   	iconCls:'icon-add',
				   	text:'添加',
				   	handler:function(){
						layui.use('layer', function(){
						 	var layer = layui.layer;
						  	layer.open({
				        		type:2,
				        		title:'添加招聘年度',
				        		area:["600px",'420px'],
				        		content:stepIndex_one_urls.__repair_url+"&flag=add"
				        	}); 
						});
				   	}
			   	},'-',{
				  	iconCls:'icon-remove',
				   	text:'删除',
				   	handler:function(){
				   		layui.use('layer', function(){
						 	var layer = layui.layer;
						 	var rows = $("#stepIndex_one").datagrid("getSelections");
							var len = rows.length;
							if(len == 0){
								layui.layer.alert("请选择要删除的招聘信息");
								return;
							}
							
							var selectInfoIds = [];
							var flag = 0;
							for(var i = 0; i < len; i++){
						   		if(rows[i]["recDefault"] != "0"){
						   			flag = 1;
			           				break;
						   		}else{
						   			selectInfoIds.push(rows[i].recID);
						   		}
						   	}
							
							if(!flag){
								layer.confirm('您确定要删除勾选的【'+len+'】条数据么', function(index){
								  	$.post(__rczp_zpsz_stepIndex_one_urls__.__recdel_url,{'recIDs':selectInfoIds},function(json){
										if(json.result){
											layer.msg(json.msg);
											init_stepIndex_one_grid(__rczp_zpsz_stepIndex_one_urls__);
											layer.close(index);
										}else{
											layer.alert(json.msg);
										}
									},'json');
								}); 
							}else{
								layui.layer.alert("选择删除的数据中包含已结束或进行中招聘信息");
								return;
							}
						});
						
					}
			   	}]
			});
		}
    });
}

function pubRecruit(recID,recYear,recBatch,recEnd){
	var nowData = formatDateTime();
	layui.use('layer', function(){
	 	var layer = layui.layer;
	 	
	 	if(nowData > recEnd){
	 		return layer.alert('该招聘批次的报名结束时间已经过去了，请重新编辑');
	 	}
	 	
	 	layer.confirm('您确定要发布招聘年度【'+recYear+'】，招聘批次【'+recBatch+'】么?<br/><span style="color:red;">注意：一旦发布，招聘截止时间没有结束将不可修改操作！</span>', function(index){
		  	$.post(__rczp_zpsz_stepIndex_one_urls__.__recpub_url,{'recID':recID},function(json){
				if(json.result){
					layer.msg(json.msg);
					init_stepIndex_one_grid(__rczp_zpsz_stepIndex_one_urls__);
					layer.close(index);
				}else{
					layer.alert(json.msg);
				}
			},'json');
		}); 
	});
}


/*人才招聘-招聘公告-公告列表*/
var __rczp_zpgg_stepIndex_two_urls__ = {};
var __rczp_zpgg_stepIndex_two_datagrid_flag__ = "A";
var __rczp_zpgg_stepIndex_two_show_flag__ = "";
var __rczp_zpgg_stepIndex_two_recID__ = "";

function init_stepIndex_two_grid_AB(stepIndex_two_urls,stepIndex_two_recID,stepIndex_two_datagrid_flag,stepIndex_two_show_flag){
	__rczp_zpgg_stepIndex_two_urls__ = stepIndex_two_urls;
	__rczp_zpgg_stepIndex_two_recID__ = stepIndex_two_recID;
	__rczp_zpgg_stepIndex_two_datagrid_flag__ = stepIndex_two_datagrid_flag;
	__rczp_zpgg_stepIndex_two_show_flag__ = stepIndex_two_show_flag;
	if(stepIndex_two_datagrid_flag == "A"){
		var cloumns = [[
        	{field:'ck',checkbox:true,width:'10%'},
            {field:'ancName',title:'公告名称',width:'25%',align:'center',},
            {field:'ancTime',title:'发布时间',width:'25%',align:'center',},
            {field:'ancStatus',title:'发布状态',width:'25%',align:'center',
            	formatter:function(value,row,index){
            		return value == "1" ? "已发布" : (value == 2 ? "已归档" : "未发布");
            	}
            },
            {field:'operation',title:'操作',width:'24%',align:'center',
            	formatter:function(value,row,index){
            		var _html = "<button onclick=\"viewAnnounce("+row.ancID+")\" class=\"layui-btn layui-btn-primary layui-btn-small  layui-btn-radius \">预览</button>";
            		if(row.ancStatus == "2"){
            			return _html+"";
            		}else if(row.ancStatus == "1"){
            			return _html+"<button onclick=\"pubAnnounce("+row.ancID+",0,'"+row.ancName+"')\" class=\"layui-btn layui-btn-primary layui-btn-small  layui-btn-radius \">取消</button>";
            		}else{
            			return _html+"<button onclick=\"pubAnnounce("+row.ancID+",1,'"+row.ancName+"')\" class=\"layui-btn layui-btn-primary layui-btn-small  layui-btn-radius \">发布</button>";
            		}
            	}
            },
        ]];
	}else{
		var cloumns = [[
        	{field:'ck',checkbox:true,width:'10%'},
            {field:'ancName',title:'单位简介',width:'25%',align:'center',},
            {field:'ancTime',title:'发布时间',width:'25%',align:'center',},
            {field:'ancStatus',title:'发布状态',width:'25%',align:'center',
            	formatter:function(value,row,index){
            		return value == "1" ? "已发布" : (value == 2 ? "已归档" : "未发布");
            	}
            },
            {field:'operation',title:'操作',width:'24%',align:'center',
            	formatter:function(value,row,index){
            		var _html = "<button onclick=\"viewAnnounce("+row.ancID+")\" class=\"layui-btn layui-btn-primary layui-btn-small  layui-btn-radius \">预览</button>";
            		if(row.ancStatus == "2"){
            			return _html+"";
            		}else if(row.ancStatus == "1"){
            			return _html+"<button onclick=\"pubAnnounce("+row.ancID+",0,'"+row.ancName+"')\" class=\"layui-btn layui-btn-primary layui-btn-small  layui-btn-radius \">取消</button>";
            		}else{
            			return _html+"<button onclick=\"pubAnnounce("+row.ancID+",1,'"+row.ancName+"')\" class=\"layui-btn layui-btn-primary layui-btn-small  layui-btn-radius \">发布</button>";
            		}
            	}
            },
        ]];
	}
	
	$('#stepIndex_two_' + stepIndex_two_datagrid_flag).datagrid({
        width:'auto',
        height:'auto',
	    url:stepIndex_two_urls.__list_url,
	    method: "post",
	    queryParams: {'recID':stepIndex_two_recID,'ancType':stepIndex_two_datagrid_flag},
	    striped: true,
	    fixed: true,
	    fitColumns: false,
	    singleSelect: false,
        pagination: true,  
	    rownumbers: true, 
	    pageNumber:1,
	    pageSize:20,
	    pageList:[20,50,100,200],
        columns:cloumns,
        onDblClickRow: function(index,row){
        	var msg = "";
		 	if(__rczp_zpgg_stepIndex_two_datagrid_flag__ == "A"){
		 		msg = "招聘公告";
		 	}else{
		 		msg = "单位简介";
		 	}
        	layui.use('layer', function(){
				var layer = layui.layer;
				if(__rczp_zpgg_stepIndex_two_show_flag__ == "1"){
					if(row.ancStatus == "1"){
						layer.alert('此'+msg+"【"+row.ancName+"】已经发布了，如需修改，请先取消发布");
						return;
					}
					
					layer.open({
		        		type:2,
		        		title:'修改'+msg,
		        		area:["650px",'505px'],
		        		content:__rczp_zpgg_stepIndex_two_urls__.__repair_url+"&flag=mod&ancID="+row.ancID+"&ancType="+__rczp_zpgg_stepIndex_two_datagrid_flag__
		        	}); 
				}
        	});
	    },
        onLoadSuccess: function(data){
			$('#stepIndex_two_' + stepIndex_two_datagrid_flag).datagrid('resize',{
	    		height: $(window).height()-124-25-60
	    	});
	
			if(__rczp_zpgg_stepIndex_two_show_flag__ == "1"){
				var msg = "";
			 	if(__rczp_zpgg_stepIndex_two_datagrid_flag__ == "A"){
			 		msg = "招聘公告";
			 	}else{
			 		msg = "单位简介";
			 	}
				$('#stepIndex_two_' + __rczp_zpgg_stepIndex_two_datagrid_flag__).datagrid('getPager').pagination({buttons:[
					{
					   	iconCls:'icon-add',
					   	text:'添加',
					   	handler:function(){
							layui.use('layer', function(){
							 	var layer = layui.layer;
							 	layer.open({
					        		type:2,
					        		title:'添加'+msg,
					        		area:["650px",'505px'],
					        		content:__rczp_zpgg_stepIndex_two_urls__.__repair_url+"&flag=add&ancType="+__rczp_zpgg_stepIndex_two_datagrid_flag__
					        	}); 
							});
					   	}
				   	},'-',{
					  	iconCls:'icon-remove',
					   	text:'删除',
					   	handler:function(){
					   		layui.use('layer', function(){
							 	var layer = layui.layer;
							 	var rows = $('#stepIndex_two_' + __rczp_zpgg_stepIndex_two_datagrid_flag__).datagrid("getSelections");
								var len = rows.length;
								if(len == 0){
									layui.layer.alert("请选择要删除的"+msg);
									return;
								}
								
								var selectInfoIds = [];
								var flag = 0;
								for(var i = 0; i < len; i++){
							   		if(rows[i].ancStatus != "0"){
							   			flag = 1;
				           				break;
							   		}else{
							   			selectInfoIds.push(rows[i].ancID);
							   		}
							   	}
								
								if(!flag){
									layer.confirm('您确定要删除勾选的【'+len+'】条'+msg+'数据么', function(index){
									  	$.post(__rczp_zpgg_stepIndex_two_urls__.__recdel_url,{'ancIDs':selectInfoIds},function(json){
											if(json.result){
												layer.msg(json.msg);
												init_stepIndex_two_grid_AB(__rczp_zpgg_stepIndex_two_urls__,__rczp_zpgg_stepIndex_two_recID__,__rczp_zpgg_stepIndex_two_datagrid_flag__,__rczp_zpgg_stepIndex_two_show_flag__);
												layer.close(index);
											}else{
												layer.alert(json.msg);
											}
										},'json');
									}); 
								}else{
									layui.layer.alert("选择删除的数据中包含已发布的"+msg+"信息");
									return;
								}
							});
						}
				   	}]
				});
			}
		}
    });
}

function pubAnnounce(ancID,type,ancName){
	var msg = ['取消发布','发布'];
	layui.use('layer', function(){
	 	var layer = layui.layer;
	 	layer.confirm('您确定要'+msg[type]+'【'+ancName+'】么?', function(index){
		  	$.post(__rczp_zpgg_stepIndex_two_urls__.__recpub_url,{'ancID':ancID,'ancStatus':type},function(json){
				if(json.result){
					layer.msg(json.msg);
					init_stepIndex_two_grid_AB(__rczp_zpgg_stepIndex_two_urls__,__rczp_zpgg_stepIndex_two_recID__,__rczp_zpgg_stepIndex_two_datagrid_flag__,__rczp_zpgg_stepIndex_two_show_flag__);
					layer.close(index);
				}else{
					layer.alert(json.msg);
				}
			},'json');
		}); 
	});
}

function viewAnnounce(ancID){
	layui.use('layer', function(){
	 	var layer = layui.layer;
	 	layer.open({
    		type:2,
    		title:'预览',
    		area:[$(window).width()/2+"px",'520px'],
    		content:__rczp_zpgg_stepIndex_two_urls__.__recpre_url+"&ancID="+ancID,
    		btn:['关闭'],
    		btn2:function(){
    			layer.closeAll();
    		}
    	}); 
	});
}



/*人才招聘-资格审查-数据列表*/
var __rczp_zgsc_stepIndex_three_urls__ = {};
var __rczp_zgsc_stepIndex_three_tab__ = "1";
var __rczp_zgsc_stepIndex_three_show_flag__ = "";
var __rczp_zgsc_stepIndex_three_recID__ = "";
var __rczp_zgsc_stepIndex_three_recend__ = "";
var __rezp_zgsc_stepIndex_three_total = 0;
var __rezp_zgsc_stepIndex_three_condition = {};
var __rczp_zgsc_stepIndex_three_sendmsg__content__ = "";
var __rczp_zgsc_stepIndex_three_btnOperate__ = {};
var __rczp_zgsc_stepIndex_three_tab_header_info = {};
var __rczp_zgsc_stepIndex_three_header_info = {};
function init_stepIndex_three_grid(stepIndex_three_urls,stepIndex_three_recID,stepIndex_three_tab,stepIndex_three_show_flag,stepIndex_three_recend){
	var __stepIndex_three_search = {
		'perName' : "",
		'perGender' : "",
		'perBirth' : "",
		'perReResult1' : "",
	};
	if(stepIndex_three_tab == __rczp_zgsc_stepIndex_three_tab__){
		__stepIndex_three_search.perName = $("#stepIndex_three_search #perName").val();
		__stepIndex_three_search.perGender = $("#stepIndex_three_search #perGender").val();
		__stepIndex_three_search.perBirth = $("#stepIndex_three_search #perBirth").val();
		__stepIndex_three_search.perReResult1 = $("#stepIndex_three_search #perReResult1").val();
	}
	
	__rczp_zgsc_stepIndex_three_urls__ = stepIndex_three_urls;
	__rczp_zgsc_stepIndex_three_recID__ = stepIndex_three_recID;
	__rczp_zgsc_stepIndex_three_tab__ = stepIndex_three_tab;
	__rczp_zgsc_stepIndex_three_show_flag__ = stepIndex_three_show_flag;
	__rczp_zgsc_stepIndex_three_recend__ = stepIndex_three_recend;
	
	$('#stepIndex_three').datagrid({
        width:'auto',
        height:'auto',
	    url:stepIndex_three_urls.__list_url,
	    method: "post",
	    queryParams: {'recID':stepIndex_three_recID,'type':stepIndex_three_tab,'search':__stepIndex_three_search},
	    striped: true,
	    fixed: true,
	    fitColumns: false,
	    singleSelect: false,
        pagination: true,  
	    rownumbers: true, 
	    pageNumber:1,
	    pageSize:20,
	    pageList:[20,50,100,200],
	    sortName:'perIndex',
	    sortOrder:'ASC',
	    toolbar:"#stepIndex_three_search",
	    frozenColumns:[[
    		{field:'ck',checkbox:true},
	        {field:'perIndex',title:'报名序号',width:'80',align:'center',sortable:true},
	        {field:'perName',title:'姓名',width:'70',align:'center',sortable:true},
        ]], 
        columns:[[
	        {field:'perIDCard',title:'身份证号',width:'180',align:'center',rowspan:2,sortable:true},
	        {field:'perGender',title:'性别',width:'5%',align:'center',rowspan:2,sortable:true,
	        	formatter:function(value,row,index){
	        		return value == "1" ? "男" : "女";
	        	}
	        },
	        {field:'perBirth',title:'出生年月',width:'100',align:'center',rowspan:2,sortable:true},
	        {field:'perJob',title:'应聘岗位性质',width:'8%',align:'center',rowspan:2,sortable:true,
	        	formatter:function(value,row,index){
	        		return value == "01" ? "事业" : "企业";
	        	}
	        },
	        {field:'perPhone',title:'手机号码',width:'100',align:'center',rowspan:2},
	        {field:'perStatus',title:'审查结果',width:'100',align:'center',rowspan:2,sortable:true,
	        	formatter:function(value,row,index){
	        		return value == "1" ? "待审" : (value == "2" ? "通过" : "不通过");
	        	}
	        },
	        {field:'perPub',title:'公示结果',width:'80',align:'center',rowspan:2,sortable:true,
	        	formatter:function(value,row,index){
	        		return value == "0" ? "未公示" : "<span style='color:red;'>已公示</span>";
	        	}
	        },
	        {field:'perReason',title:'审查不通过原因',width:'10%',align:'center',rowspan:2},
	        {field:'perCheckTime',title:'审查时间',width:'130',align:'center',rowspan:2,sortable:true,
	        	formatter:function(value,row,index){
	        		return value == "0000-00-00 00:00:00" ? "" : value;
	        	}
	        },
	        {field:'perZGSC',title:'考试反馈结果',width:'300',colspan:3,align:'center'}
	        ],[
		    	{field:'perReResult1',title:'反馈结果',width:'10%',align:'center',sortable:true,
		    		formatter:function(value,row,index){
		        		return value == "01" ? "确定参加" : (value == "02" ? "放弃参加" : "未反馈");
		        	}
		    	},
		    	{field:'perReGiveup1',title:'放弃原因',width:'10%',align:'center'},
		    	{field:'perReTime1',title:'反馈时间',width:'130',align:'center',sortable:true,
		    		formatter:function(value,row,index){
		        		return value == "0000-00-00 00:00:00" ? "" : value;
		        	}
		    	}
	    ]],
        onDblClickRow: function(index,row){
        	layui.use('layer', function(){
			 	var layer = layui.layer;
			 	layer.open({
		    		type:2,
		    		title:'详细信息【'+row.perName+'】',
		    		area:[$(window).width()/5*4+"px",$(window).height()-100+'px'],
		    		content:__rczp_zgsc_stepIndex_three_urls__.__qamdetial_url+"&perID="+row.perID+"&recID="+__rczp_zgsc_stepIndex_three_recID__,
		    		btn:['关闭'],
		    		btn2:function(){
		    			layer.closeAll();
		    		}
		    	}); 
			});
	    },
        onLoadSuccess: function(data){
        	$("#stepIndex_three_head_pubinfo").html("");
        	$("#stepIndex_three_head_pubinfo").html("【已公示："+data.headerInfo.pub+"人，未公示："+data.headerInfo.nopub+"人】");
        	
        	__rczp_zgsc_stepIndex_three_header_info = data.headerInfo;
        	__rczp_zgsc_stepIndex_three_tab_header_info = data.tabInfo;
        	__rezp_zgsc_stepIndex_three_total = data.total;
        	__rezp_zgsc_stepIndex_three_condition = data.exportInfo.condition;
        	__rczp_zgsc_stepIndex_three_btnOperate__ = data.btnOperate;
        	$("#stepIndex_three_tab #stepIndex_three_tabli1").html("");
        	$("#stepIndex_three_tab #stepIndex_three_tabli2").html("");
        	$("#stepIndex_three_tab #stepIndex_three_tabli3").html("");
        	$("#stepIndex_three_tab #stepIndex_three_tabli4").html("");
        	
        	$("#stepIndex_three_tab #stepIndex_three_tabli1").html("("+__rczp_zgsc_stepIndex_three_tab_header_info.tab1+")");
        	$("#stepIndex_three_tab #stepIndex_three_tabli2").html("("+__rczp_zgsc_stepIndex_three_tab_header_info.tab2+")");
        	$("#stepIndex_three_tab #stepIndex_three_tabli3").html("("+__rczp_zgsc_stepIndex_three_tab_header_info.tab3+")");
        	$("#stepIndex_three_tab #stepIndex_three_tabli4").html("("+__rczp_zgsc_stepIndex_three_tab_header_info.tab4+")");
        	
        	$("#stepIndex_three_tab #stepIndex_three_tabli1").css("display","");
        	$("#stepIndex_three_tab #stepIndex_three_tabli2").css("display","");
        	$("#stepIndex_three_tab #stepIndex_three_tabli3").css("display","");
        	$("#stepIndex_three_tab #stepIndex_three_tabli4").css("display","");
        	
			$('#stepIndex_three').datagrid('resize',{
	    		height: $(window).height()-124-25-50
	    	});
	    	
		    var nowData = formatDateTime();
		    if(stepIndex_three_show_flag == "1"){
		    	if(nowData > __rczp_zgsc_stepIndex_three_recend__){
		    		var pageShow = __rczp_zgsc_stepIndex_three_header_info.nopub + __rczp_zgsc_stepIndex_three_tab_header_info.tab1;
		    		if(pageShow == 0){
		    			$("#stepIndex_three").datagrid('getPager').pagination({buttons:[
							{
							  	iconCls:'icon-export',
							   	text:'导出Excel',
							   	handler:function(){
							   		manager_showMore(this,'stepIndex_three_export');
							   	}
						   	},'-',{
							  	iconCls:'icon-print',
							   	text:'打印报名表',
							   	handler:function(){
							   		manager_showMore(this,'stepIndex_three_perprint');
							   	}
						   	}]
						});
		    		}else{
			    		$("#stepIndex_three").datagrid('getPager').pagination({buttons:[
							{
							   	iconCls:'icon-ok',
							   	text:'审核',
							   	handler:function(){
									manager_showMore(this,'stepIndex_three_checklist');
							   	}
						   	},'-',{
							  	iconCls:'icon-pub',
							   	text:'公示',
							   	handler:function(){
							   		manager_showMore(this,'stepIndex_three_checkpub');
							   	}
						   	},'-',{
							  	iconCls:'icon-export',
							   	text:'导出Excel',
							   	handler:function(){
							   		manager_showMore(this,'stepIndex_three_export');
							   	}
						   	},'-',{
							  	iconCls:'icon-print',
							   	text:'打印报名表',
							   	handler:function(){
							   		manager_showMore(this,'stepIndex_three_perprint');
							   	}
						   	},'-',{
							  	iconCls:'icon-tip',
							   	text:'短信提醒',
							   	handler:function(){
							   		manager_showMore(this,'stepIndex_three_msgtip');
							   	}
						   	},'-',{
							  	iconCls:'icon-edit',
							   	text:'额外通知设置',
							   	handler:function(){
							   		stepIndex_three_extraset();
							   	}
						   	}]
						});
					}
		    	}else{
		    		$("#stepIndex_three").datagrid('getPager').pagination({buttons:[
						{
						   	iconCls:'icon-undo',
						   	text:'报名撤回',
						   	handler:function(){
								stepIndex_three_check(0);
						   	}
					   	},'-',{
						   	iconCls:'icon-ok',
						   	text:'审核',
						   	handler:function(){
								manager_showMore(this,'stepIndex_three_checklist');
						   	}
					   	},'-',{
						  	iconCls:'icon-export',
						   	text:'导出Excel',
						   	handler:function(){
						   		manager_showMore(this,'stepIndex_three_export');
						   	}
					   	},'-',{
						  	iconCls:'icon-print',
						   	text:'打印报名表',
						   	handler:function(){
						   		manager_showMore(this,'stepIndex_three_perprint');
						   	}
					   	},'-',{
						  	iconCls:'icon-tip',
						   	text:'短信提醒',
						   	handler:function(){
						   		manager_showMore(this,'stepIndex_three_msgtip');
						   	}
					   	},'-',{
						  	iconCls:'icon-edit',
						   	text:'额外通知设置',
						   	handler:function(){
						   		stepIndex_three_extraset();
						   	}
					   	}]
					});
		    	}
		    }else{
		    	$("#stepIndex_three").datagrid('getPager').pagination({buttons:[
					{
					  	iconCls:'icon-export',
					   	text:'导出Excel',
					   	handler:function(){
					   		manager_showMore(this,'stepIndex_three_export');
					   	}
				   	},'-',{
					  	iconCls:'icon-print',
					   	text:'打印报名表',
					   	handler:function(){
					   		manager_showMore(this,'stepIndex_three_perprint');
					   	}
				   	}]
				});
		    }
        }
    });
}

function stepIndex_three_check(perStatus){
	var msg = "";
	if(perStatus == "0"){
		msg = "撤回报名";
	}else if(perStatus == "2"){
		msg = "审核通过";
	}else if(perStatus == "3"){
		msg = "审核不通过";
	}
	layui.use('layer', function(){
	 	var layer = layui.layer;
	 	var rows = $("#stepIndex_three").datagrid('getSelections');
		var len = rows.length;
		if(len == 0){
			layer.alert("请勾选要"+msg+"的数据！");
			return;
		}
		var flag = 0;
		var perIDs = [];
		for(var i = 0; i < len; i++){
			if(rows[i]['perPub'] == "1"){
				flag = 1;
				break;
			}
			perIDs.push(rows[i]['perID']);
		}
		
		if(!flag){
			if(perStatus == "0" || perStatus == "2"){
				layer.confirm('您确定要'+msg+'勾选的【'+len+'】条数据么', function(index){
				  	$.post(__rczp_zgsc_stepIndex_three_urls__.__qamstatus_url,{'perIDs':perIDs,'recID':__rczp_zgsc_stepIndex_three_recID__,'perStatus':perStatus},function(json){
						if(json.result){
							layer.msg(json.msg);
							init_stepIndex_three_grid(__rczp_zgsc_stepIndex_three_urls__,__rczp_zgsc_stepIndex_three_recID__,__rczp_zgsc_stepIndex_three_tab__,__rczp_zgsc_stepIndex_three_show_flag__,__rczp_zgsc_stepIndex_three_recend__);
							layer.close(index);
						}else{
							layer.alert(json.msg);
						}
					},'json');
				}); 
			}else{
				layer.confirm('您确定要'+msg+'勾选的【'+len+'】条数据么', function(index){
					layer.prompt({
					  	formType: 2,
					  	value: '',
					  	title: '审核不通过原因',
					  	area: ['300px', '150px']
					}, function(value, index, elem){
					  	$.post(__rczp_zgsc_stepIndex_three_urls__.__qamstatus_url,{'perIDs':perIDs,'recID':__rczp_zgsc_stepIndex_three_recID__,'perStatus':perStatus,'perReason':value},function(json){
							if(json.result){
								layer.msg(json.msg);
								init_stepIndex_three_grid(__rczp_zgsc_stepIndex_three_urls__,__rczp_zgsc_stepIndex_three_recID__,__rczp_zgsc_stepIndex_three_tab__,__rczp_zgsc_stepIndex_three_show_flag__,__rczp_zgsc_stepIndex_three_recend__);
								layer.closeAll();
							}else{
								layer.alert(json.msg);
							}
						},'json');
					});
				}); 
			}
		}else{
			layer.alert("勾选的人员中存在已经公示过结果的人员，不允许操作！");
			return;
		}
	});
}

function stepIndex_three_export(type){
	layui.use('layer', function(){
	 	var layer = layui.layer;
	 	
	 	if(__rezp_zgsc_stepIndex_three_total == 0){
			layer.alert('当前列表没有任何数据，不需要导出！');
			return;
		}
	 	
	 	if(type == 0){
	 		//var index = layer.load(0, {time: 1000*1000}); 
		 	//layer.close(index); 
		 	$("#stepIndex_three_exportForm").find("input[name='condition']").val(JSON.stringify(__rezp_zgsc_stepIndex_three_condition));
		 	$("#stepIndex_three_exportForm").find("input[name='type']").val(type);
		 	$("#stepIndex_three_exportForm").find("input[name='flag']").val(__rczp_zgsc_stepIndex_three_tab__);
		 	$("#stepIndex_three_exportForm").find("input[name='recID']").val(__rczp_zgsc_stepIndex_three_recID__);
			$("#stepIndex_three_exportForm").submit();
	 	}else{
	 		//alert("'"+JSON.stringify(__rezp_zgsc_stepIndex_three_condition)+"'");return;
	 		layer.open({
	    		type:2,
	    		title:'自定义导出字段',
	    		area:[$(window).width()/2+"px",$(window).height()-100+'px'],
	    		content:__rczp_zgsc_stepIndex_three_urls__.__qamexpclm_url+"&type="+type,
	    		btn:['确定','关闭'],
	    		yes: function(){
	    			$("iframe[id*='layui-layer-iframe'")[0].contentWindow.stepIndexThreeExpclm(); 
	    			//var rows = $("iframe[id*='layui-layer-iframe'").contents().find("#stepIndex_three_expclm").datagrid('getSelections');
		        },
	    		btn2:function(){
	    			layer.closeAll();
	    		}
		    });
	 	}
	 	
	 	
	});
}

function stepIndex_three_extraset(){
	layui.use('layer', function(){
	 	var layer = layui.layer;
	 	
 		layer.open({
    		type:2,
    		title:'额外通知信息设置',
    		area:["600px",$(window).height()*3/4+'px'],
    		content:__rczp_zgsc_stepIndex_three_urls__.__qamextrap_url+"&recID="+__rczp_zgsc_stepIndex_three_recID__,
    		btn:['确定','关闭'],
    		yes: function(){
    			$("iframe[id*='layui-layer-iframe'")[0].contentWindow.stepIndexThreeExtraset(); 
	        },
    		btn2:function(){
    			layer.closeAll();
    		}
	    });
	});
}

function stepIndex_three_pub(type){
	layui.use('layer', function(){
	 	var layer = layui.layer;
	 	if(__rczp_zgsc_stepIndex_three_btnOperate__.isInfos == 0){
	 		layer.alert("当前没有任何数据不需要公示");
	 		return;
	 	}
	 	
	 	var check_flag = 0;
	 	$.ajax({
	 		type:"post",
	 		url:__rczp_zgsc_stepIndex_three_urls__.__pubcheck_url,
	 		async:false,
	 		dataType:'json',
	 		data:{'recID':__rczp_zgsc_stepIndex_three_recID__},
	 		success:function(json){
	 			if(json.result != 0){
	 				check_flag = 1;
	 			}
	 		}
	 	});
	 	
	 	if(check_flag){
	 		layer.alert("存在未审核完成的人员，全部审核完成后才可以公示信息");
	 		return;
	 	}
			 	
	 	var perIDs = [];
	 	var msg = ['您确定要<span style="color:red;">公示全部通过</span>的人员么','您确定要<span style="color:red;">公示全部不通过</span>的人员么','您确定要<span style="color:red;">公示全部</span>人员么','您确定要<span style="color:red;">公示勾选</span>的人员么'];
	 	if(type == 3){
	 		var rows = $("#stepIndex_three").datagrid('getSelections');
			var len = rows.length;
			if(len == 0){
				layer.alert("请勾选需要公示的人员！");
				return;
			}
			for(var i = 0; i < len; i++){
				perIDs.push(rows[i]['perID']);
			}
	 	}else if(type == 0){
	 		if(__rczp_zgsc_stepIndex_three_tab_header_info.tab2 == 0){
	 			layer.alert("没有审核通过的人员数据");
	 			return;
	 		}
	 	}else if(type == 1){
	 		if(__rczp_zgsc_stepIndex_three_tab_header_info.tab3 == 0){
	 			layer.alert("没有审核不通过的人员数据");
	 			return;
	 		}
	 	}else if(type == 2){
	 		var tabscount = __rczp_zgsc_stepIndex_three_tab_header_info.tab2+__rczp_zgsc_stepIndex_three_tab_header_info.tab3;
	 		if(tabscount == 0){
	 			layer.alert("没有需要公示的人员数据");
	 			return;
	 		}
	 	}
		
		layer.confirm(msg[type], function(index){
		  	$.post(__rczp_zgsc_stepIndex_three_urls__.__perpub_url,{'perIDs':perIDs,'recID':__rczp_zgsc_stepIndex_three_recID__,'type':type},function(json){
				if(json.result){
					layer.msg(json.msg);
					init_stepIndex_three_grid(__rczp_zgsc_stepIndex_three_urls__,__rczp_zgsc_stepIndex_three_recID__,__rczp_zgsc_stepIndex_three_tab__,__rczp_zgsc_stepIndex_three_show_flag__,__rczp_zgsc_stepIndex_three_recend__);
					layer.close(index);
				}else{
					layer.alert(json.msg);
				}
			},'json');
		}); 
	});
}

function stepIndex_three_perprint(type){
	layui.use('layer', function(){
	 	var layer = layui.layer;
	 	if(__rczp_zgsc_stepIndex_three_btnOperate__.isInfos == 0){
	 		layer.alert("当前没有任何数据，不需要打印");
	 		return;
	 	}
	 	
	 	
	 	var perIDs = "";
	 	var msg = ['您确定要<span style="color:red;">打印全部人员</span>么','您确定要<span style="color:red;">打印全部审核人员</span>么','您确定要<span style="color:red;">打印全部公示人员</span>么','您确定要<span style="color:red;">打印全部未审核人员</span>么','您确定要<span style="color:red;">打印勾选</span>的人员么'];
	 	if(type == 4){
	 		var rows = $("#stepIndex_three").datagrid('getSelections');
			var len = rows.length;
			if(len == 0){
				layer.alert("请勾选需要打印的人员！");
				return;
			}
			for(var i = 0; i < len; i++){
				if(i == len -1){
					perIDs += rows[i]['perID'];
				}else{
					perIDs += rows[i]['perID']+",";
				}
			}
	 	}else if(type == 0){
	 		if(__rczp_zgsc_stepIndex_three_tab_header_info.tab4 == 0){
	 			layer.alert("没有需要打印的人员信息！");
				return;
	 		}
	 	}else if(type == 1){
	 		var tabscount = __rczp_zgsc_stepIndex_three_tab_header_info.tab2+__rczp_zgsc_stepIndex_three_tab_header_info.tab3;
	 		if(tabscount == 0){
	 			layer.alert("没有全部审核的人员数据，不需要打印");
	 			return;
	 		}
	 	}else if(type == 2){
	 		if(__rczp_zgsc_stepIndex_three_header_info.pub == 0){
	 			layer.alert("没有已公示的人员数据，不需要打印");
	 			return;
	 		}
	 	}else if(type == 3){
	 		if(__rczp_zgsc_stepIndex_three_tab_header_info.tab1 == 0){
	 			layer.alert("没有未审核的人员数据，不需要打印");
	 			return;
	 		}
	 	}
	 	
		layer.confirm(msg[type], function(index){
			window.open(__rczp_zgsc_stepIndex_three_urls__.__perprint_url+"&recID="+__rczp_zgsc_stepIndex_three_recID__+"&type="+type+"&perIDs="+perIDs);
			layer.close(index);
		});
	});
}


function stepIndex_three_msgtip(type){
	layui.use('layer', function(){
	 	var layer = layui.layer;
	 	if(__rczp_zgsc_stepIndex_three_btnOperate__.isInfos == 0){
	 		layer.alert("当前没有任何数据，不需要短信提醒");
	 		return;
	 	}
	 	
	 	var msg = ['您确定要<span style="color:red;">通知所有通过人员</span>么','您确定要<span style="color:red;">通知所有未通过人员</span>么','您确定要<span style="color:red;">通知所有已审核人员（不包括待审人员）</span>么','您确定要<span style="color:red;">通知所勾选人员</span>么'];
	 	var msgtip = ['通过人员','未通过人员','已审核人员','勾选人员'];
	 	var perIDs = "";
	 	var flag = 0;
	 	if(type == 3){
	 		var rows = $("#stepIndex_three").datagrid('getSelections');
			var len = rows.length;
			if(len == 0){
				layer.alert("请勾选需要短信通知的人员！");
				return;
			}
			for(var i = 0; i < len; i++){
				if(rows[i]['perStatus'] == '1'){
					flag = 1;
					break;
				}
				
				if(i == len -1){
					perIDs += rows[i]['perID'];
				}else{
					perIDs += rows[i]['perID']+",";
				}
			}
			
			if(flag == 1){
				layer.alert('勾选的人员中存在未审核的人员');
				return ;
			}
	 	}else if(type == 0){
	 		if(__rczp_zgsc_stepIndex_three_tab_header_info.tab2 == 0){
	 			layer.alert("没有审核通过的人员数据，不需要短信通知");
 				return;
	 		}
	 	}else if(type == 1){
	 		if(__rczp_zgsc_stepIndex_three_tab_header_info.tab3 == 0){
	 			layer.alert("没有审核不通过的人员数据，不需要短信通知");
 				return;
	 		}
	 	}else if(type == 2){
	 		var tabscount = __rczp_zgsc_stepIndex_three_tab_header_info.tab2+__rczp_zgsc_stepIndex_three_tab_header_info.tab3;
	 		if(tabscount == 0){
	 			layer.alert("没有已审核的人员数据，不需要短信通知");
	 			return;
	 		}
	 	}
	 	
		layer.prompt({
		  	formType: 2,
		  	value: '',
		  	title: '设置短信通知内容<span style="color:red;">【'+msgtip[type]+"】</span>",
		  	area: ['300px', '150px']
		}, function(value, index, elem){
		  	__rczp_zgsc_stepIndex_three_sendmsg__content__ = value;
    	 	layer.open({
		  		type:2,
		  		title:'确认信息<span style="color:red;">【'+msgtip[type]+'】</span>',
		  		area:[$(window).width()*3/4+"px",'520px'],
		  		content:__rczp_zgsc_stepIndex_three_urls__.__qumsendmsg_url+"&recID="+__rczp_zgsc_stepIndex_three_recID__+"&type="+type+"&perIDs="+perIDs,
		  		btn:['发送','关闭'],
		  		yes: function(){
		  			$("iframe[id*='layui-layer-iframe'")[0].contentWindow.stepIndexThreeSendMsg(); 
			    },
		  		btn2:function(){
		  			layer.close(layer.getFrameIndex(window.name));
		  		}
		    });
		});
	});
}
