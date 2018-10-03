# yii2-composite-validator
Composite Validator for Yii2 Framework

## Installation

Install with composer:

```bash
composer require raoptimus/yii2-composite-validator
```

## Usage samples

Create any simple composite validator:

```php
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
```

Create any form with composite validator

```php
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

```

Use form validate

```php
$form = new DefaultForm();
$form->validate();
```
$form->field returns string 'test' 