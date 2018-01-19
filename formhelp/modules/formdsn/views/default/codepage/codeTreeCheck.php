<script type="text/javascript">
$(function(){
	var codeindex = '<?php echo $codeindex; ?>';
	$.getJSON("index.php?r=widget/tree",{"codeindex":codeindex},function(json){
		buildCodeTree(json);
	});
});
/**
 * 构建单选代码树
 */
function buildCodeTree(json){
	var treeInfo = json;
	var treeSet = {
			check: {
				enable: true,
				chkboxType: {"Y": "","N": ""}
			},
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
				onCheck: function(event, treeId, treeNode){
					if(selectMenu_obj){
						var checkedNodes = codeTreeObj.getCheckedNodes(true);
						var checkInfos = "";
						var checkIds = "";
						for(var i in checkedNodes){
//							if(checkedNodes[i].isLeaf == "1"){
								checkInfos += checkedNodes[i].name + "、";
								checkIds += checkedNodes[i].code + "/";
//							}
						}
						if(checkInfos!=""){
							checkInfos = checkInfos.substring(0, checkInfos.length-1);
							checkIds = checkIds.substring(0, checkIds.length-1);
							selectMenu_obj.val(checkInfos);
							selectMenu_obj.attr("title", checkInfos);
							selectMenu_obj.attr("code", checkIds);
							selectMenu_obj.focus();
						}else{
							selectMenu_obj.val("");
							selectMenu_obj.attr("title", "");
							selectMenu_obj.attr("code", "");
							selectMenu_obj.focus();
						}
					}
				},
				onExpand: function(event, treeId, treeNode){
					selectMenu_position();
				},
				onCollapse: function(event, treeId, treeNode){
					selectMenu_position();
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
	//初始化选中节点
	if(selectMenu_obj && selectMenu_obj.val()!=""){
		var code = selectMenu_obj.attr("code");
		var arr = code.split("/");
		for(var i=0; i<arr.length; i++){
			if(arr[i] && arr[i]!=""){
				var node = codeTreeObj.getNodeByParam("code", arr[i]);
				if(node){
					codeTreeObj.checkNode(node, true, true);
				}
			}
		}
	}
}
</script>
<div>
	<ul id="codeTree_single" class="ztree"></ul>
</div>