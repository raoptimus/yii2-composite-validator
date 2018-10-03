<?php

namespace raoptimus\validators\tests;

use raoptimus\validators\tests\forms\DefaultForm;

/**
 * This file is part of the raoptimus/yii2-composite-validator library
 *
 * @copyright Copyright (c) Evgeniy Urvantsev <resmus@gmail.com>
 * @license https://github.com/raoptimus/yii2-composite-validator/blob/master/LICENSE.md
 * @link https://github.com/raoptimus/yii2-composite-validator
 */
class ValidateTest extends TestCase
{
    public function testValidateDefaultValue(): void
    {
        $form = new DefaultForm();
        $form->validate();
        self::assertTrue($form->validate(), print_r($form->getErrors(), true));
        self::assertEquals('test', $form->field);
    }

    public function testValidateFilledField(): void
    {
        $form = new DefaultForm(['field' => 'hello']);
        self::assertTrue($form->validate(), print_r($form->getErrors(), true));
        self::assertEquals('hello', $form->field);
    }

    public function testValidateInvalidField(): void
    {
        $form = new DefaultForm(
            [
                'field' => 'I never wonder to see men wicked, but I often wonder to see them not ashamed',
            ]
        );
        self::assertFalse($form->validate());
    }
}
