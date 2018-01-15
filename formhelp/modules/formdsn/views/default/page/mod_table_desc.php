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
		    <label class="layui-form-label">表名称</label>
		    <div class="layui-input-block">
			    <input name="tabledesc" id="tabledesc" value="" class="layui-input" type="text">
		    </div>
		</div>
	</div>
</div>
<?php $this->endBody() ?>
	
<script>
$(function(){
	layui.use('form', function(){
		var form = layui.form;
		$("#tabledesc").val(parent.flow_table_title);
	});
});

function table_modify_name_desc_sure(){
	layui.use('layer',function(){
		var layer = layui.layer;
		var table_desc = $("#tabledesc").val().trim();
		if(table_desc == ""){
			return layer.alert("表名称不能为空");
		}
		parent.layer.confirm("确定修改？",function(index){
			$.getJSON("<?= yii\helpers\Url::to(['default/modtabledescdo']); ?>",
			{
				"table_desc":table_desc,
				"table_id":parent.flow_table_id
			},function(json){
				if(json.result){
					parent.load_table_tree();
					parent.layer.closeAll();
					parent.layer.msg(json.msg);
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