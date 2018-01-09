<?php

namespace app\modules\formdsn;

use Yii;
use yii\base\BootstrapInterface;
use yii\web\ForbiddenHttpException;

/**
 * mobile module definition class
 */
class Module extends \yii\base\Module implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\formdsn\controllers';

	public $allowedIPs = ['127.0.0.1', '::1'];
	
    public $generators = [];
    
    public $newFileMode = 0666;
    
    public $newDirMode = 0777;
	
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
	
	public function bootstrap($app)
    {
        if ($app instanceof \yii\web\Application) {
            $app->getUrlManager()->addRules([
                ['class' => 'yii\web\UrlRule', 'pattern' => $this->id, 'route' => $this->id . '/default/index'],
                ['class' => 'yii\web\UrlRule', 'pattern' => $this->id . '/<id:\w+>', 'route' => $this->id . '/default/view'],
                ['class' => 'yii\web\UrlRule', 'pattern' => $this->id . '/<controller:[\w\-]+>/<action:[\w\-]+>', 'route' => $this->id . '/<controller>/<action>'],
            ], false);
        } elseif ($app instanceof \yii\console\Application) {
            
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        return true;
    }

}
