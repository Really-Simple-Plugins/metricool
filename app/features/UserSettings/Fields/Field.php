<?php

namespace Metricool\Features\UserSettings\Fields;

use Metricool\Features\UserSettings\Factories\ValidatorFactory;
use Metricool\Features\UserSettings\Fields\Exceptions\ValidationErrors;
use Metricool\Features\UserSettings\Validators\AbstractValidator;
use Metricool\Features\UserSettings\Validators\Exceptions\ValidatorFailedException;

class Field
{
    protected const DEFAULT_VALIDATORS = ['required', 'builtin'];

    public string $name;
    public string $type;
    public ?string $section;
    public ?string $defaultValue;
    public string $storage;
    public bool $required = false;
    public $value = null;
    public $validateCallback = null;
    /** @var AbstractValidator[] */
    public array $validators = [];

    public function __construct(string $name, array $config)
    {
        $this->name = $name;
        $this->type = $config['type'] ?? 'string';
        $this->section = $config['section'] ?? null;
        $this->storage = $config['storage'] ?? 'default';
        $this->defaultValue = $config['default_value'] ?? null;
        $this->required = $config['required'] ?? false;
        $this->validateCallback = $config['validate'] ?? null;

        $this->initializeValidators($config['validators'] ?? self::DEFAULT_VALIDATORS);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getSection(): ?string
    {
        return $this->section;
    }

    public function getStorageName(): string
    {
        return $this->storage;
    }

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        $value = $this->value ?: $this->getDefaultValue();

        return $this->castValue($value);
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @throws ValidatorFailedException
     */
    public function validate($value, \WP_REST_Request $request = null)
    {
        $validationErrors = [];

        foreach ($this->validators as $validator) {
            try {
                $validator->validate($value, $request);
            } catch (ValidatorFailedException $e) {
                $validationErrors[] = $e;
            }
        }

        if (!empty($validationErrors)) {
            throw new ValidationErrors($validationErrors);
        }
    }

    /**
     * Casts the value to the type of the field
     */
    protected function castValue($value)
    {
        switch ($this->type) {
            case 'boolean':
            case 'bool':
                return (bool) $value;
            case 'integer':
            case 'int':
                return (int) $value;
            case 'float':
            case 'double':
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

    private function initializeValidators(array $validators)
    {
        foreach ($validators as $validator) {
            $this->validators[] = ValidatorFactory::create($validator, $this);
        }
    }
}