<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html class='accounts sign-in' lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <style>
    	.layui-form-switch{
    		width:60px;
    	}
    </style>
</head>
<body>
<?php $this->beginBody() ?>

<div class='wrap'>
	<div class="page-body">
		<div class="logo">
			<img alt="" src="/images/logo.png">
		</div>
		<div class="box">
			<div class="panel mb-none">
				<div class="layui-form layui-form-pane1">
				  	<div class="layui-form-item" pane>
					    <label class="layui-form-label">账号</label>
					    <div class="layui-input-block">
					      <input type="text" name="name"  lay-verify="name" placeholder="请输入账号" autocomplete="off" class="layui-input" onKeyDown="if (event.keyCode == 13) javascript:return $('#login_sub').click();">
					    </div>
				  	</div>
			  		<div class="layui-form-item" pane>
					    <label class="layui-form-label">密码</label>
					    <div class="layui-input-block">
					      <input type="password" name="password" lay-verify="password" placeholder="请输入密码" autocomplete="off" class="layui-input" onKeyDown="if (event.keyCode == 13) javascript:return $('#login_sub').click();">
					    </div>
			  		</div>
			  
				  	<div class="layui-form-item" pane>
					    <div class="layui-input-block">
					      <button id="login_sub" class="layui-btn" lay-submit>登录</button>
					      <button id="login_ret" type="reset" class="layui-btn layui-btn-primary">重置</button>
					    </div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php $this->endBody() ?>

<script>
	$(function(){
		layui.use('form', function(){
		 	var form = layui.form;
		  	form.verify({
			    name: function(value){
			      if(value == ""){
			        return '账号不能为空';
			      }
			    },
			    password: function(value){
			    	if(value == ""){
			    		return '密码不能为空';
			    	}
			    }
			});
		});
	});
	
	$("#login_sub").bind("click",function(){
		layui.form.on('submit',function(data){
			var df = data.field;
			var password = MD5(df.password);
			$.post("<?= Url::to(['site/logindo']) ?>",{'User':{'name':df.name,'password':password}},function(json){
				if(json.result){
					window.location.href = "<?= Url::to(['index/index']) ?>"
				}else{
					layui.layer.alert(json.msg);
				}
			},'json');
		});
	});
	
	$("#login_ret").bind("click",function(){
		$("input[name='name']").val('');
		$("input[name='password']").val('');
	});
</script>

</body>
</html>
<?php $this->endPage() ?>
