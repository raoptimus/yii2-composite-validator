<?php

namespace raoptimus\validators\tests\validators;

use raoptimus\validators\CompositeValidator;
use yii\validators\DefaultValueValidator;
use yii\validators\StringValidator;

/**
 * This file is part of the raoptimus/yii2-composite-validator library
 *
 * @copyright Copyright (c) Evgeniy Urvantsev <resmus@gmail.com>
 * @license https://github.com/raoptimus/yii2-composite-validator/blob/master/LICENSE.md
 * @link https://github.com/raoptimus/yii2-composite-validator
 */
class StringDefaultValidator extends CompositeValidator
{
    /** @var string */
    public $defaultValue;
    /** @var int */
    public $max;
    /** @var int */
    public $min;

    /**
     * @inheritdoc
     */
    protected function validators(): array
    {
        return [
            [StringValidator::class, 'max' => $this->max, 'min' => $this->min],
            [DefaultValueValidator::class, 'value' => $this->defaultValue],
        ];
    }
}
