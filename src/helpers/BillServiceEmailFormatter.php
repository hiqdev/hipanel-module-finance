<?php
declare(strict_types=1);

namespace hipanel\modules\finance\helpers;

use Symfony\Component\Yaml\Yaml;

class BillServiceEmailFormatter
{
    public static function prepareBody(array $models): string
    {
        $output = PHP_EOL;
        foreach ($models as $billForm) {
            $output .= self::prepareSubject([$billForm]) . PHP_EOL;
            $output .= PHP_EOL . str_repeat('-', 3) . PHP_EOL;
            $output .= Yaml::dump($billForm->toArray());
            $output .= PHP_EOL . str_repeat('=', 21) . PHP_EOL;
        }

        return $output;
    }

    public static function prepareSubject(array $models): string
    {
        $output = [];
        foreach ($models as $billForm) {
            $output[] = sprintf(
                '%s: %s %s %s',
                $billForm->client_id,
                $billForm->sum,
                $billForm->currency,
                $billForm->label
            );
        }

        return implode(',', $output);
    }
}
