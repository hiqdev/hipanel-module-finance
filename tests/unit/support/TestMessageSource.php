<?php
declare(strict_types=1);

namespace hipanel\modules\finance\tests\unit\support;

use yii\i18n\MessageSource;

final class TestMessageSource extends MessageSource
{
    protected function translateMessage($category, $message, $language): string
    {
        return $message;
    }
}
