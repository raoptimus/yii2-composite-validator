<?php

namespace raoptimus\validators;

use ArrayObject;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\validators\Validator;

/**
 * This file is part of the raoptimus/yii2-composite-validator library
 *
 * @copyright Copyright (c) Evgeniy Urvantsev <resmus@gmail.com>
 * @license https://github.com/raoptimus/yii2-composite-validator/blob/master/LICENSE.md
 * @link https://github.com/raoptimus/yii2-composite-validator
 */
abstract class CompositeValidator extends Validator
{
    /**
     * @var bool whether this validation rule should be skipped if the attribute value
     * is null or an empty string.
     */
    public $skipOnEmpty = false;
    /**
     * @var ArrayObject|Validator[] list of validators
     */
    private $validators;

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function validateAttribute($model, $attribute): void
    {
        $validators = $this->getActiveValidators($model, $attribute);
        foreach ($validators as $validator) {
            $validator->validateAttributes($model, [$attribute]);
        }
    }

    /**
     * Creates validator objects based on the validation rules specified in [[rules()]].
     * Unlike [[getValidators()]], each time this method is called, a new list of validators will be returned.
     *
     * @param Model $model
     *
     * @param string $validateAttribute
     *
     * @return ArrayObject validators
     * @throws InvalidConfigException
     */
    protected function createValidators(Model $model, string $validateAttribute): ArrayObject
    {
        $validators = new ArrayObject();
        foreach ($this->validators() as $validator) {
            if ($validator instanceof Validator) {
                $this->prepareValidator($validator);
                $validators->append($validator);
            } elseif (\is_array($validator) && isset($validator[0])) { // validator type
                $validator = Validator::createValidator(
                    $validator[0],
                    $model,
                    $validateAttribute,
                    \array_slice($validator, 1)
                );
                $this->prepareValidator($validator);
                $validators->append($validator);
            } else {
                throw new InvalidConfigException(
                    'Invalid validation rule: a rule must specify both attribute names and validator type.'
                );
            }
        }

        return $validators;
    }

    /**
     * Move local options to each validator
     *
     * @param Validator $validator
     */
    protected function prepareValidator(Validator $validator): void
    {
        $options = [
            'on' => $this->on,
            'when' => $this->when,
            'except' => $this->except,
            'skipOnEmpty' => $this->skipOnEmpty,
            'skipOnError' => $this->skipOnError,
        ];
        $options = array_filter($options);

        foreach ($options as $attribute => $value) {
            if ($validator->$attribute === null) {
                $validator->$attribute = $value;
            }
        }
    }

    /**
     * Returns the validators applicable to the current [[scenario]].
     *
     * @param Model $model
     * @param string $validateAttribute
     *
     * @return Validator[] the validators applicable to the current [[scenario]].
     * @throws InvalidConfigException
     */
    protected function getActiveValidators(Model $model, string $validateAttribute): array
    {
        $validators = [];
        $scenario = $model->getScenario();
        foreach ($this->getValidators($model, $validateAttribute) as $validator) {
            if ($validator->isActive($scenario)) {
                $validators[] = $validator;
            }
        }

        return $validators;
    }

    /**
     * Returns all the validators declared in [[rules()]].
     *
     * This method differs from [[getActiveValidators()]] in that the latter
     * only returns the validators applicable to the current [[scenario]].
     *
     * Because this method returns an ArrayObject object, you may
     * manipulate it by inserting or removing validators (useful in model behaviors).
     * For example,
     *
     * ```php
     * $model->validators[] = $newValidator;
     * ```
     *
     * @param Model $model
     * @param string $validateAttribute
     *
     * @return ArrayObject|Validator[] all the validators declared in the model.
     * @throws InvalidConfigException
     */
    protected function getValidators(Model $model, string $validateAttribute)
    {
        if ($this->validators === null) {
            $this->validators = $this->createValidators($model, $validateAttribute);
        }

        return $this->validators;
    }

    /**
     * Returns the validation rules or list of validators.
     *
     * Validation rules are used by [[validateAttribute()]] to check if attribute values are valid.
     *
     * Each rule is an array with the following structure:
     *
     * ```php
     * [
     *     'validator type',
     *     'on' => ['scenario1', 'scenario2'],
     *     //...other parameters...
     * ]
     * ```
     *
     * where
     *
     *  - validator type: required, specifies the validator to be used. It can be a built-in validator name,
     *    a method name of the model class, an anonymous function, or a validator class name.
     *  - on: optional, specifies the [[scenario|scenarios]] array in which the validation
     *    rule can be applied. If this option is not set, the rule will apply to all scenarios.
     *  - additional name-value pairs can be specified to initialize the corresponding validator properties.
     *    Please refer to individual validator class API for possible properties.
     *
     * A validator can be either an object of a class extending [[Validator]], or a model class method
     * (called *inline validator*) that has the following signature:
     *
     * ```php
     * // $params refers to validation parameters given in the rule
     * function validatorName($attribute, $params)
     * ```
     *
     * In the above `$attribute` refers to the attribute currently being validated while `$params` contains an array of
     * validator configuration options such as `max` in case of `string` validator. The value of the attribute currently being validated
     * can be accessed as `$this->$attribute`. Note the `$` before `attribute`; this is taking the value of the variable
     * `$attribute` and using it as the name of the property to access.
     *
     * Yii also provides a set of [[Validator::builtInValidators|built-in validators]].
     * Each one has an alias name which can be used when specifying a validation rule.
     *
     * Below are some examples:
     *
     * ```php
     * [
     *     // built-in "required" validator
     *     ['required'],
     *     // built-in "string" validator customized with "min" and "max" properties
     *     ['string', 'min' => 3, 'max' => 12],
     *     // built-in "compare" validator that is used in "register" scenario only
     *     ['compare', 'compareAttribute' => 'password2'],
     *     // an inline validator defined via the "authenticate()" method in the model class
     *     ['authenticate'],
     *     // a validator of class "DateRangeValidator"
     *     ['DateRangeValidator'],
     * ];
     * ```
     *
     * @return array|Validator[] validation rules or validators
     */
    abstract protected function validators(): array;
}
