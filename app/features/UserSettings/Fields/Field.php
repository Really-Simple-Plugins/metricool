<?php

namespace Metricool\Features\UserSettings\Fields;

use Metricool\Features\UserSettings\Factories\ValidatorFactory;
use Metricool\Features\UserSettings\Validators\AbstractValidator;
use Metricool\Features\UserSettings\Validators\Exceptions\ValidatorFailedException;
use Metricool\Features\UserSettings\Validators\FieldTypeValidator;

class Field
{
    public string $name;
    public string $type;
    public ?string $section;
    public $defaultValue;
    public string $storage;
    public ?string $storageName;
    /** @var mixed */
    public $value = null;

    /** @var AbstractValidator[] */
    protected array $validators = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Gets the name of the field
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Gets the type of the field
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Gets the section name of this field
     */
    public function getSection(): ?string
    {
        return $this->section;
    }

    /**
     * Gets the storage name of the field
     */
    public function getStorage(): string
    {
        return $this->storage;
    }

    public function getNameForStorage(): string
    {
        return $this->storageName ?? $this->name;
    }

    /** @return mixed */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    /** @return mixed|null */
    public function getValue()
    {
        $value = $this->value ?: $this->getDefaultValue();

        if (is_null($value)) {
            return null;
        }

        return $this->castValue($value);
    }

    /**
     * Casts the value to the type of the field
     * @return mixed
     */
    protected function castValue($value)
    {
        switch ($this->type) {
            case 'boolean':
                return (bool) $value;
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'string':
                return (string) $value;
            case 'array':
                return (array) $value;
            case 'object':
                return (object) $value;
            default:
                return $value;
        }
    }

    public function getValidators(): array
    {
        return $this->validators;
    }

    /** @param AbstractValidator[] $validators */
    public function setValidators(array $validators)
    {
        $this->validators = $validators;
    }

    public function addValidator(AbstractValidator $validator)
    {
        $this->validators[] = $validator;
    }

    /**
     * Validates the value of the field against this field's validators
     * @param mixed $value
     * @throws ValidatorFailedException
     */
    public function validate($value, \WP_REST_Request $request = null)
    {
        foreach ($this->validators as $validator) {
            $validator->validate($value, $request);
        }
    }

    /**
     ** Apply the configuration array to the field
     */
    public function applyConfig(array $config = []): self
    {
        $this->type = $config['type'] ?? 'string';
        $this->section = $config['section'] ?? '';
        $this->storage = $config['storage'] ?? 'default';
        $this->storageName = $config['storageName'] ?? null;
        $this->defaultValue = $config['defaultValue'] ?? null;

        // Check if we should add the type validator
        $validateType = $config['validateType'] ?? true;
        if ($validateType) {
            $this->addValidator(new FieldTypeValidator($this));
        }

        // Add configured validators
        if (isset($config['validators'])) {
            foreach ($config['validators'] as $validatorConfig) {
                $validator = ValidatorFactory::createFromConfig($validatorConfig, $this);
                $this->addValidator($validator);
            }
        }

        return $this;
    }
}