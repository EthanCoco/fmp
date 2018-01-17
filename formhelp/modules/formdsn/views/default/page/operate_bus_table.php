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
	<div class="layui-form">
		
		<div class="layui-form-item">
		    <label class="layui-form-label">业务表名称</label>
		    <div class="layui-input-block">
			    <input name="busTableName" id="busTableName" value="" class="layui-input" type="text">
		    </div>
		</div>
	</div>
</div>
<?php $this->endBody() ?>
	
<script>
$(function(){
	layui.use('form', function(){
		var form = layui.form;
	});
});

function operate_bus_table_sure(){
	layui.use('layer',function(){
		var layer = layui.layer;
		var busTableName = $("#busTableName").val().trim();
		if(busTableName == ""){
			return parent.layer.alert("业务表名称不能为空");
		}
		parent.layer.confirm("确定保存么？",function(index){
			$.getJSON("<?= yii\helpers\Url::to(['default/operatebustabledo']); ?>",
			{
				"bus_table_name":busTableName,
				"node_id":"<?= $nodeID ?>"
			},function(json){
				if(json.result){
					parent.layer.close(index-1);
					parent.layer.msg(json.msg);
					parent.$('#layui-layer-iframe'+(index-2))[0].contentWindow.load_flow_node_tree(); 
				}else{
					parent.layer.alert(json.msg);
				}
			});
		});
	});
}

</script>	
</body>
</html>
<?php $this->endPage() ?>