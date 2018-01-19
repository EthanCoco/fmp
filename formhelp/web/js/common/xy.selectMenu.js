/** 
 * 下拉控件
 * @author Vic.z 2012-05-18
 */ 
	
var selectMenu_obj = null; //当前输入框对象
var selectMenu_flag = false; //是否移除下拉控件的标识
var selectMenu_scrollDiv = null; //外层存在滚动条件的父对象
/**
 * 控件入口
 * input属性说明：
 * view_type：显示方式（1是列表型下拉控件，2是树形下拉控件）
 * code_index: 代码索引
 * method_url：点击时触发的事件路径
 * value: 显示值（默认为空，读取数据时，放置对应数据的值）
 * @param objth 当前对象
 * @param divId 当前对象外层存在滚动条的父对象的id
 */
function show_control_view(objth, divId) {
	hideSelectMenu();
	selectMenu_flag = true;
	var type = $(objth).attr("view_type"); //控件类型
	var codeindex = $(objth).attr("code_index"); //代码索引
	selectMenu_obj = $(objth);//当前对象
	if(divId){
		selectMenu_scrollDiv = $("#"+divId);
	}
	//var cName = $(objth).attr("colname"); //字段名
	var method_url = $(objth).attr("method_url"); //点击时触发的事件路径
	var hasSearch = $(objth).attr("hasSearch")!=null ? $(objth).attr("hasSearch") : "true";; //是否出现搜索框
	var singleOrMore = $(objth).attr("singleOrMore")!=null ? $(objth).attr("singleOrMore") : "1";; //单选或多选
	var params = {}; //请求参数
	if(!method_url && type ){ //判断是否是常用控件
		if(type==1 || type==2){ //下拉控件 （列表或树形） 
			method_url = 'controlpage.html?code_index='+codeindex+'&view_type='+type+'&singleOrMore='+singleOrMore;
		}else{
			return;
		}
	}
	
	//加载下拉控件至body
	var selectDiv = $("#selectMenu");
	if(!selectDiv || !selectDiv.html()){
		selectDiv = '<div id="selectMenu" class="level">'+
		  	'<a class="rubber" title="清空">&nbsp;</a>'+
		    '<a class="seach_erro" title="关闭">&nbsp;</a>'+
			'<ul class="level_search">'+
				'<li class="first">'+
					'<a><input name="treeSearchInput" type="text" class="input1" /></a>'+
				  	'<a class="btn" style="position: relative;">'+
				    	'<font></font>'+
				        '<font class="fontzi">搜索</font>'+
				        '<font></font>'+
				        '<div class="clear"></div>'+
				    '</a>'+
				    '<a class="btn" style="position: relative;">'+
				    	'<font></font>'+
				        '<font class="fontzi">清空</font>'+
				        '<font></font>'+
				        '<div class="clear"></div>'+
				    '</a>'+
				'</li>'+
			'</ul>'+
			'<div class="levelcontent">'+
			'</div>'+
		'</div>';
		$("body").append(selectDiv)
		selectDiv = $("#selectMenu");
		//selectDiv.appendTo("body");
		if(hasSearch=="false"){ //判断是否出现搜索框
			selectDiv.find(".level_search").css("display","none");
		}
//		if(isShowStopedBtn=="false"){ //判断是否出现已停用代码按钮
			selectDiv.find(".showhistory").css("display","none");
//		}
	}
	//添加清空按钮事件
	selectDiv.find(".rubber").bind("click", function(){
		selectMenu_obj.attr("title","");
		selectMenu_obj.attr("code","");
		selectMenu_obj.val("");
		hideSelectMenu();
	});
	//添加关闭按钮事件
	selectDiv.find(".seach_erro").bind("click", function(){
		hideSelectMenu();
	});
	
	/*** 处理控件定位 start ***/
	selectMenu_position();
	/*** 处理控件定位 end ***/
	
	/*** 处理控件搜索及内容的异步加载 start ***/
	var contentDiv = selectDiv.find(".levelcontent").eq(0);
	
	if(hasSearch!="false"){
		var searchInputObj = selectDiv.find("input[name='searchInput']");
		var searchButtonObj = selectDiv.find("a[name='search']");
		searchInputObj.val("");
		var hisWord = "";
	}
	
	//加载控件内容
	contentDiv.html("");
	contentDiv.load(method_url, params, function(){
		setTimeout(function(){
			selectMenu_position();
		}, 300);
	});
	
	/*** 处理控件搜索及内容的异步加载 end ***/
	
	
	selectDiv.unbind("click").bind("click", function(){
		//selectMenu_flag = true;
		return false;
	});
	
	//滚动条绑定事件，移除div
	if(selectMenu_scrollDiv){
		selectMenu_scrollDiv.scroll(function() {
			hideAllControl();
			selectMenu_flag = false;
		});
	}
	$(document).unbind("click", onBodyDown).bind("click", onBodyDown);

}
/**
 * 处理下拉控件定位
 */
function selectMenu_position(){
	if(!selectMenu_obj){
		return;
	}
	var divHeight = $("#selectMenu").height();
	var divWidth = $("#selectMenu").width();
	
	var inputWidth = selectMenu_obj.width();
	if(inputWidth<150){
		inputWidth = 150;
	}
	var inputHeight = selectMenu_obj.outerHeight();
//	selectDiv.css({height:"200px", minWidth:inputWidth+"px", maxWidth:"300px", display:"block", overflow:"auto", position:"absolute", zIndex:"1001", textAlign: "left", border:"1px solid #000000", backgroundColor:"#FFF"});;

	var bodyHeight=$(window).height();//整个body的高度
	var bodyWidth= $(window).width();//整个body的宽度
	
	var divScrollTop = 0;
	if(selectMenu_scrollDiv){
		divScrollTop = selectMenu_scrollDiv.scrollTop();
	}
	var up = selectMenu_obj.offset().top;//当前对象到body(头)的高度
	var down = bodyHeight - up - inputHeight - divScrollTop;//当前对象距离body顶部的高度，（减去了当前对象本身的高度以及外层div滚动条件的scrollTop）
	var leftPos = selectMenu_obj.offset().left; //生成div的left
	var right = bodyWidth - leftPos;//当前对象距离body右侧的宽度
	var topPos = 0; //生成div的top

	//下拉控件的定位
	if(down < divHeight){
		topPos = up - divHeight - 11;//生成的div在当前对象的上面 15
	}else{
		topPos= up + inputHeight + 1;//生成的div在当前对象的下面
	}
	
	if(right < divWidth){
		leftPos = leftPos - divWidth - 10;//生成的div在右边的水平位置
	}
	$("#selectMenu").css({
		left:leftPos,
		top:topPos
	}).slideDown("normal");
	
	var width = parseInt($("#selectMenu").find(".levelcontent").width());
	if(width<380)
	{
		$("#selectMenu").find(".levelcontent").css("overflow-x","hidden")
	}
	else $("#selectMenu").find(".levelcontent").css("overflow-x","auto")
}
/**
 * 销毁下拉控件
 */
function hideSelectMenu() {
	$("#selectMenu").slideUp("fast");
	$("#selectMenu").remove();
	//selectMenu_obj = null;
	selectMenu_scrollDiv = null
	$(document).unbind("click", onBodyDown);
}
/**
 * 销毁所有当前div中所有的下拉控件
 */
function hideAllControl(){
	$(document).trigger("click");
	
//	hideSelectMenu();
//	lhgcalendar().hide();
//	showDateYM().hide();
//	showDateY().hide();
}
/**
 * 全局点击事件
 * @param event
 */
function onBodyDown(event) {
	if(!selectMenu_flag){
		hideSelectMenu();
	}
	selectMenu_flag = false;
	return true;
}

/**
 * 手机端下拉文本
 */
function mobileSelectNote(selectCodeInfo,selectDivId){
    $("#"+selectDivId).append('<option value="'+selectCodeInfo[i].codeID+'">'+selectCodeInfo[i].codeName+'</option>');
}
/**
 * 手机端下拉树形
 */
function mobileSelectTree(selectCodeInfo,selectDivId){
	
}
