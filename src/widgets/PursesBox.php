<?php declare(strict_types=1);

namespace hipanel\modules\finance\widgets;

use hipanel\modules\client\models\Client;
use hipanel\modules\finance\assets\PursesBox\PursesBoxAsset;
use hipanel\modules\finance\models\Purse;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\Application;

class PursesBox extends Widget
{
    /** * @var Purse[] */
    public array $purses = [];
    public Client $client;
    private Application $app;

    public function init()
    {
        $this->app = Yii::$app;
    }

    public function run(): string
    {
        if (!$this->app->user->can('bill.read')) {
            return '';
        }

        PursesBoxAsset::register($this->view);

        $props = $this->buildProps();
        $this->registerMountScript($props);

        return Html::tag('div', '', ['id' => $this->id]);
    }

    private function buildProps(): string
    {
        return Json::encode(
            [
                'language' => $this->app->language,
                'purses' => array_map([$this, 'serializePurse'], $this->purses),
                'permissions' => $this->buildPermissionList(),
            ],
            JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP
        );
    }

    private function buildPermissionList(): array
    {
        $currentUser = $this->app->user;

        $permissionFlags = [
            'document.read' => $currentUser->can('document.read'),
            'document.generate' => $currentUser->can('document.generate'),
            'purse.update' => $currentUser->can('purse.update'),
            'client.update' => $currentUser->can('client.update'),
            'owner-staff' => $currentUser->can('owner-staff'),
            'has-own-seller' => $currentUser->identity->hasOwnSeller($this->client->id),
            'is-employee' => $currentUser->can('is-employee'),
        ];

        return array_keys(array_filter($permissionFlags));
    }

    private function registerMountScript(string $props): void
    {
        $this->getView()->registerJs("window.PursesBox.mount(document.getElementById('$this->id'), $props);");
    }

    private function serializePurse(Purse $purseModel): array
    {
        $i18n = $this->app->i18n;
        $purse = $purseModel->toArray();

        $purse['contact'] = $purseModel->contact->toArray();
        $purse['requisite'] = $purseModel->requisite->toArray();

        foreach (['contact', 'requisite'] as $attribute) {
            $purse[$attribute]['bankDetails'] = $purseModel->{$attribute}->hasBankDetails() ? array_map(
                static fn($p) => $p->toArray(),
                $purseModel->{$attribute}->bankDetails
            ) : [];
        }

        $purse['documents'] = [];
        if ($purseModel->isRelationPopulated('documents')) {
            $purse['documents'] = array_map(
                static function ($document) use ($i18n): array {
                    $data = array_map(
                        static fn($v) => $i18n::removeLegacyLangTags($v),
                        $document->toArray()
                    );

                    $data['date'] = explode(' ', $document->validity_start ?? '', 2)[0];


                    return $data;
                },
                $purseModel->documents
            );
        }

        return $purse;
    }
}
