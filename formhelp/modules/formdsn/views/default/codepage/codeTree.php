<script type="text/javascript">
$(function(){
	var codeindex = '<?php echo $codeindex; ?>';
	$.getJSON("tree.html",{"codeindex":codeindex},function(json){
		buildCodeTree(json);
	});
});
/**
 * 构建单选代码树
 */
function buildCodeTree(json){
	var treeInfo = json;
	var treeSet = {
			view: {
				dblClickExpand: true,
				showLine: true,
				selectedMulti: false,
				showTitle: true,
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
						if(treeNode.isleaf==1){
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
//							selectMenu_obj.val(treeNode.name);
							selectMenu_obj.attr("title", treeNode.name);
							
							selectMenu_obj.focus();
							hideSelectMenu();
						}else{
							return;
						}
					}
				}
			}
		};
	$.fn.zTree.init($("#codeTree_single"), treeSet, treeInfo);
	var codeTreeObj = $.fn.zTree.getZTreeObj("codeTree_single");
	codeTreeObj.expandNode(codeTreeObj.getNodes()[0], true, false, true);
	// 搜索
	var function_inputObj = $('#selectMenu input[name="treeSearchInput"]');
	var searchBtn = function_inputObj.parent().next();
	var clearBtn = searchBtn.next();
	bindTreeSearchFun("codeTree_single", function_inputObj, searchBtn, clearBtn);
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
	<ul id="codeTree_single" class="ztree"></ul>
</div>