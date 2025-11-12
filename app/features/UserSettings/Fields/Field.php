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

    /** @var mixed */
    protected $value = null;
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

    public function getValidators(): array
    {
        return $this->validators;
    }

    /** @param AbstractValidator[] $validators */
    public function setValidators(array $validators): self
    {
        $this->validators = $validators;
        return $this;
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
        $this->defaultValue = $config['defaultValue'] ?? null;
        $this->validators = $this->createValidatorsFromConfig($config['validators'] ?? [], $config['validateType'] ?? true);

        return $this;
    }

    /**
     * Create validators for a field from the configuration and add the TypeValidator if needed
     * @return AbstractValidator[]
     */
    public function createValidatorsFromConfig(array $validatorNames, bool $typeValidator): array
    {
        $validators = [];

        // Add the type validator before any other validators
        if ($typeValidator) {
            $validators[] = new FieldTypeValidator($this);
        }

        foreach ($validatorNames as $validator) {
            $validators[] = ValidatorFactory::create($validator, $this);
        }

        return $validators;
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
}