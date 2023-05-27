<?php

declare(strict_types=1);

namespace Superpayments\SuperPayment\Model\Config\Source;

class ColorScheme
{
    public const ORANGE = 'orange';
    public const YELLOW = 'yellow';
    public const BLACK_ORANGE = 'black-orange';
    public const BLACK_WHITE = 'black-white';

    public function toOptionArray(): array
    {
        return [
            ['value' => self::ORANGE, 'label' => __('Orange')],
            ['value' => self::YELLOW, 'label' => __('Yellow')],
            ['value' => self::BLACK_ORANGE, 'label' => __('Black Orange')],
            ['value' => self::BLACK_WHITE, 'label' => __('Black White')],
        ];
    }

    public function toArray(): array
    {
        return [
            self::ORANGE => __('Orange'),
            self::YELLOW => __('Yellow'),
            self::BLACK_ORANGE => __('Black Orange'),
            self::BLACK_WHITE => __('Black White'),
        ];
    }
}
