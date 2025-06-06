<?php declare(strict_types=1);

namespace hipanel\modules\finance\controllers;

use hipanel\filters\EasyAccessControl;
use hiqdev\billing\registry\Application\TariffConfiguration;
use yii\web\Controller;

class BillingRegistryController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => EasyAccessControl::class,
                'actions' => [
                    '*' => 'owner-staff',
                ],
            ]
        ]);
    }

    public function actionIndex()
    {
        $registry = TariffConfiguration::buildRegistry();

        return $this->render('index', [
            'registry' => $registry,
        ]);
    }
}
