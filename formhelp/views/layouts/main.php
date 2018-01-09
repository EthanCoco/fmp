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
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="layui-layout-body">
<?php $this->beginBody() ?>

<div class="layui-layout layui-layout-admin">
	<div class="layui-header">
	    <div class="layui-logo">
	    	<img alt="" src="/images/logo.png">
	    </div>
	    <ul class="layui-nav layui-layout-right">
	      	<li class="layui-nav-item">
		        <a href="javascript:;">
		          	<img src="/images/user-default.jpg" class="layui-nav-img"><?php echo Yii::$app->user->identity->name; ?>
		        </a>
		        <dl class="layui-nav-child">
		          	<!--<dd><a href="">基本资料</a></dd>-->
		          	<?php if(Yii::$app->user->identity->userType=='0'){ ?>
		          	<!--formdsn模块-->
		          	<dd><a href="<?= Url::to(['/index.php/formdsn']); ?>" target="_blank" title="设计" class="">表单设计</a></dd>
		          	<dd><a href=""><a href="javascript:;" onclick="logout();">[退出]</a></a></dd>
		          	<?php }?>
		        </dl>
	      	</li>
	    </ul>
  	</div>
  	<div class="layui-header2">
  		<ul class="layui-nav layui-layout-left">
			<li class="layui-nav-item">
				<a index="1000102582" href="<?= Url::to(['index/index']); ?>" title="人才招聘">首页</a>
			</li>
			<?php if(Yii::$app->user->identity->userType=='0'){ ?>
			<li class="layui-nav-item">
				<a index="1000021899" href="<?= Url::to(['index/system']); ?>" title="系统管理">系统管理</a>
			</li>
			<?php }?>
		</ul>
  	</div>
  	
  	<?= $content ?>
  	
  	<!--<div class="layui-footer" style="text-align: center;">
    	©备案信息
  	</div>-->
</div>
<?php $this->endBody() ?>
<script>
$(function(){
	layui.use('element', function(){
	  	var element = layui.element;
	});
});

function changeTop(currtab){
	$(".layui-header2 .layui-nav a").removeClass('current');
	var aObj = $(".layui-header2 .layui-nav a[index='"+currtab+"']");
	aObj.addClass('current');
}

function logout(){
	layui.use('layer', function(){
	 	var layer = layui.layer;
	  	layer.confirm('确定要退出系统么?', function(index){
		  	layer.close(index);
		  	window.location.href = "<?= Url::to(['site/logout']); ?>";
		}); 
	});
}

</script>
</body>
</html>
<?php $this->endPage() ?>
