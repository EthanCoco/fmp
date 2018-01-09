<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'js/plugin/layui/css/layui.css',
        'js/plugin/easyui/themes/bootstrap/easyui.css',
        'js/plugin/easyui/themes/color.css',
        'js/plugin/easyui/themes/icon.css',
        'js/plugin/ztree/zTreeStyle.css',
        'css/index.css',
        'css/mobile.css',
        
    ];
    public $js = [
    	'js/common/md5.js',
    	'js/plugin/layui/layui.js',
    	'js/plugin/easyui/jquery.easyui.min.js',
    	'js/plugin/easyui/locale/easyui-lang-zh_CN.js',
    	
		'js/bus/gridtable.js',
		'js/common/public.js',
		'js/plugin/Highcharts-6.0.2/code/highcharts.js',
		'js/plugin/ztree/jquery.ztree.all-3.5.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
