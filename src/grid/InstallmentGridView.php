<?php

declare(strict_types=1);

namespace hipanel\modules\finance\grid;

use hipanel\components\User;
use hipanel\grid\BoxedGridView;
use hipanel\modules\finance\models\Sale;
use hipanel\modules\finance\models\Target;
use hipanel\modules\server\models\Server;
use Yii;
use yii\helpers\Html;

class InstallmentGridView extends BoxedGridView
{
    private User $user;

    public function init()
    {
        parent::init();
        $this->user = Yii::$app->user;
    }

    public function columns(): array
    {
        return array_merge(parent::columns(), [
        ]);
    }
}
