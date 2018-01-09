<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\assets\AppAsset;
AppAsset::register($this);
$this->registerJsFile("@web/js/common/jquery-1.9.1.min.js", ['depends' => ['yii\web\YiiAsset'], 'position' => $this::POS_HEAD]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?> FOR FLOW</title>
    <?php $this->head() ?>
</head>
<body class="layui-layout-body">
<?php $this->beginBody() ?>

<div class="layui-layout layui-layout-admin">
	<div class="layui-header">
	    <div class="layui-logo"><img alt="" src="/images/logo.png"></div>
	    <ul class="layui-nav layui-layout-right">
	      	<li class="layui-nav-item">
		        <a href="javascript:;">
		          	<img src="/images/user-default.jpg" class="layui-nav-img"><?php echo Yii::$app->user->identity->name; ?>
		        </a>
		        <dl class="layui-nav-child">
	          	<dd><a href=""><a href="javascript:;" onclick="close_current_window();">[关闭]</a></a></dd>
		      	</dl>
	      	</li>
	    </ul>
  	</div>
  	
  	<?= $content ?>
  		
</div>
<?php $this->endBody() ?>
<script>
$(function(){
	layui.use('element', function(){
	  	var element = layui.element;
	});
});

function close_current_window(){
	layui.use('layer', function(){
	 	var layer = layui.layer;
	  	layer.confirm('确定要关闭当前窗口?', function(index){
		  	layer.close(index);
		  	window.close();
		}); 
	});
}

</script>
</body>
</html>
<?php $this->endPage() ?>
