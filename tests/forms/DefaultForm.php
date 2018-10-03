<?php

namespace raoptimus\validators\tests\forms;

use raoptimus\validators\tests\validators\StringDefaultValidator;
use yii\base\Model;

/**
 * This file is part of the raoptimus/yii2-composite-validator library
 *
 * @copyright Copyright (c) Evgeniy Urvantsev <resmus@gmail.com>
 * @license https://github.com/raoptimus/yii2-composite-validator/blob/master/LICENSE.md
 * @link https://github.com/raoptimus/yii2-composite-validator
 */
class DefaultForm extends Model
{
    /**
     * @var string
     */
    public $field;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['field'], StringDefaultValidator::class, 'max' => 50, 'defaultValue' => 'test'],
        ];
    }
}
