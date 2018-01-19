<script type="text/javascript">
$(function(){
	var codeindex = '<?= $codeindex; ?>';
	$.getJSON("list.html",{"codeindex":codeindex},function(json){
		buildCodeList(json);
	});
});
/**
 * 构建单选代码树
 */
function buildCodeList(json){
	var treeInfo = json;
	var treeSet = {
			view: {
				dblClickExpand: true,
				showLine: true,
				selectedMulti: false,
				showTitle: true,
				showLine: false,
				fontCss : function(treeId, treeNode) {
					return (!!treeNode.highlight) ? {
						color : "#A60000",
						"font-weight" : "bold"
					} : {
						color : "#333",
						"font-weight" : "normal"
					};
				}
//				expandSpeed: ($.browser.msie && parseInt($.browser.version)<=6)?"":"fast"
			},
			data: {
				simpleData: {
					enable:true,
					idKey: "id",
					pIdKey: "pId"
				}
			},
			callback: {
				onClick: function(event, treeId, treeNode){
					if(selectMenu_obj){
						selectMenu_obj.attr("code", treeNode.code);
						/* if(selectMenu_obj.val()==treeNode.name){
							selectMenu_obj.css("color","green");
							selectMenu_obj.attr("isupdate","1");
						} */
						if(treeNode.name.indexOf('其他')!='-1'){
							selectMenu_obj.val('');
							selectMenu_obj.attr('placeholder','请手动输入');
							selectMenu_obj.attr('code',-1);
							selectMenu_obj.attr('readonly',false);
						}else{
							selectMenu_obj.attr('placeholder','');
							selectMenu_obj.val(treeNode.name);
							selectMenu_obj.attr('readonly',true);
							selectMenu_obj.attr("code", treeNode.code);
						}
						//是否参加过高校宣讲会 ,选是，则让选择具体高校
						if(selectMenu_obj.attr("id")=="p37"){
							if(treeNode.code=='1'){
								$("#p37gxshow").show();
								$("#p37gx").attr('placeholder','请选择高校！');
								$("#p37gx").val('');
								$("#p37gx").attr('code','');
							}else{
								$("#p37gxshow").hide();
								$("#p37gx").attr('placeholder','');
								$("#p37gx").val('');
								$("#p37gx").attr('code','');
							}
						}
						selectMenu_obj.attr("title", treeNode.name);
						hideSelectMenu();
						selectMenu_obj.focus();
					}
				}
			}
		};
	$.fn.zTree.init($("#codeList_single"), treeSet, treeInfo);
	var codeTreeObj = $.fn.zTree.getZTreeObj("codeList_single");
	codeTreeObj.expandNode(codeTreeObj.getNodes()[0], true, false, true);
	// 搜索
	var function_inputObj = $('#selectMenu input[name="treeSearchInput"]');
	var searchBtn = function_inputObj.parent().next();
	var clearBtn = searchBtn.next();
	bindTreeSearchFun("codeList_single", function_inputObj, searchBtn, clearBtn);
	//初始化选中
	if(selectMenu_obj){
		var curCode = selectMenu_obj.attr("code");
		if(selectMenu_obj.val()!="" && curCode){
			codeTreeObj.selectNode(codeTreeObj.getNodeByParam("code", curCode));
		}
	}
}
</script>
<div>
	<ul id="codeList_single" class="ztree"></ul>
</div>