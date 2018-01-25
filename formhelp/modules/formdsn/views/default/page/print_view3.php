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
	td{
		padding: 0 3px;
	}
</style>
</head>
<body>
<?php $this->beginBody() ?>
<div style="padding: 10px;">
	<center>
	<div id="print_view" class="layui-form">
		<table border="1" cellpadding="0" cellspacing="0">
			<?php 
				if(!empty($infos['table'])){
					$data = $infos['table'];
			?>
				<?php foreach($data as $tr){ ?>
					<tr>
						<?php foreach($tr as $td){ ?>
							<td colspan="<?=$td['colspan'] ?>" rowspan="<?=$td['rowspan'] ?>"><?=$td['plaintext'] ?></td>
						<?php } ?>
					</tr>
				<?php } ?>
			<?php } ?>
		</table>
	</div>
	</center>
</div>
<?php $this->endBody() ?>
	
<script>
$(function(){
	layui.use(['form','layer','laydate'], function(){
		var layer = layui.layer;
		var form = layui.form;
	});
});


</script>	
</body>
</html>
<?php $this->endPage() ?>