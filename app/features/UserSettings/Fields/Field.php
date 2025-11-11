<?php

namespace Metricool\Features\UserSettings\Fields;

use Metricool\Features\UserSettings\Fields\Exceptions\ValidationFailedException;

class Field
{
    public string $name;
    public string $type;
    public ?string $section;
    public ?string $defaultValue;
    public string $storage;
    public bool $required = false;
    public $value = null;
    public $validateCallback = null;

    public function __construct(string $name, array $config)
    {
        $this->name = $name;
        $this->type = $config['type'] ?? 'string';
        $this->section = $config['section'] ?? null;
        $this->storage = $config['storage'] ?? 'default';
        $this->defaultValue = $config['default_value'] ?? null;
        $this->required = $config['required'] ?? false;
        $this->validateCallback = $config['validate'] ?? null;
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

    /**
     * @throws ValidationFailedException
     */
    public function validate($value, \WP_REST_Request $request = null): void
    {
        // Call the validateCallback when not null
        if (!is_null($this->validateCallback)) {
            ($this->validateCallback)($value, $request);
        }

        // Do the built-in validation
        switch ($this->type) {
            case 'boolean':
            case 'bool':
                // accept true, false, 0, and 1 and "1", "0", "true" or "false" as boolean values
                if (!is_bool($value) && !in_array($value, ['0', '1', 'true', 'false'])) {
                    throw new ValidationFailedException(__('Please enter a valid boolean', 'metricool'));
                }
                break;
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    throw new ValidationFailedException(__('Please enter a valid email address', 'metricool'));
                }
                break;
            case 'string':
                if (!is_string($value) && !is_numeric($value)) {
                    throw new ValidationFailedException(__('Please enter a valid string', 'metricool'));
                }
                break;
            case 'integer':
            case 'int':
                if (!is_int($value)) {
                    throw new ValidationFailedException(__('Please enter a valid number', 'metricool'));
                }
                break;
            case 'array':
                if (!is_array($value)) {
                    throw new ValidationFailedException(__('Please enter a valid array', 'metricool'));
                }
                break;
        }

        if ($this->required && ($value === '' || is_null($value))) {
            throw new ValidationFailedException(__('Please enter a value', 'metricool'));
        }
    }

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
}