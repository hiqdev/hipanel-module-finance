<?php

/*
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\controllers;

use hipanel\actions\IndexAction;
use hipanel\actions\OrientationAction;
use hipanel\actions\SearchAction;
use hipanel\actions\SmartPerformAction;
use hipanel\actions\SmartUpdateAction;
use hipanel\actions\ValidateFormAction;
use hipanel\modules\finance\forms\DomainTariffForm;
use hipanel\modules\finance\logic\DomainTariffManager;
use hipanel\modules\finance\logic\TariffManagerFactory;
use Yii;

class TariffController extends \hipanel\base\CrudController
{
    public function actions()
    {
        return [
            'set-orientation' => [
                'class' => OrientationAction::class,
                'allowedRoutes' => [
                    '@tariff/index'
                ]
            ],
            'index' => [
                'class' => IndexAction::class,
            ],
            'search' => [
                'class' => SearchAction::class,
            ],
            'validate-form' => [
                'class' => ValidateFormAction::class,
            ],
            'set-note' => [
                'class' => SmartUpdateAction::class,
                'success' => Yii::t('hipanel', 'Note updated'),
            ],
            'delete' => [
                'class' => SmartPerformAction::class,
                'success' => Yii::t('hipanel/finance/tariff', 'Tariff deleted'),
            ],
        ];
    }

    public function actionCreateDomain()
    {
        /** @var DomainTariffManager $manager */
        $manager = TariffManagerFactory::createByType('domain', ['scenario' => 'create']);
        $form = $manager->form;

        if (Yii::$app->request->isPost && $form->load(Yii::$app->request->post())) {
            $manager->insert();
            return $this->redirect(['view', 'id' => $form->id]);
        }

        return $this->render('domain/create', ['model' => $form]);
    }

    public function actionUpdate($id)
    {
        $manager = TariffManagerFactory::createById($id, ['scenario' => 'update']);
        $form = $manager->form;

        if (Yii::$app->request->isPost && $form->load(Yii::$app->request->post())) {
            $manager->update();
            return $this->redirect(['view', 'id' => $form->id]);
        }

        return $this->render($manager->getType() . '/update', ['model' => $form]);
    }

    public function actionView($id)
    {
        $manager = TariffManagerFactory::createById($id);

        return $this->render('view', ['manager' => $manager]);
    }
}
