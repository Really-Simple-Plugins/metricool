<?php

declare(strict_types=1);

namespace Metricool\Features\UserSettings\Fields;

use Metricool\Features\UserSettings\Exceptions\ValidatorFailedException;
use Metricool\Features\UserSettings\Factories\ValidatorFactory;
use Metricool\Features\UserSettings\Storage\AbstractStorage;
use Metricool\Features\UserSettings\Validators\AbstractValidator;
use Metricool\Features\UserSettings\Validators\FieldTypeValidator;

class Field
{
    /**
     * Field identifier. Used as key when registering and as default setting name.
     */
    public string $name;

    /**
     * Field data type (boolean|integer|float|string|array|object). Used for
     * casting and validation.
     */
    public string $type;

    /**
     * Section name within settings UI. Used for grouping fields in the UI.
     */
    public ?string $section;

    /**
     * Storage key name (e.g. `default`). Used to select the storage implementation.
     */
    public string $storageName;

    /**
     * Setting name for persistence; if null, the field {@see $name} is used.
     */
    public ?string $settingName;

    /**
     * @var mixed Default value returned when no stored value exists.
     */
    public $defaultValue;

    /**
     * @var mixed Current value set via setValue(); if null, value is read from
     * storage.
     */
    public $value = null;

    /**
     * Storage instance used to update/read the field value.
     */
    public AbstractStorage $storage;

    /**
     * @var AbstractValidator[] Validators applied to this field during validate().
     */
    protected array $validators = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Gets the name of the field. If no {@see $settingName} is set, this value
     * is used as the setting name.
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
     * Gets the setting name of the field
     */
    public function getSettingName(): string
    {
        return $this->settingName ?? $this->name;
    }

    /**
     * Gets the storage name of the field
     */
    public function getStorageName(): string
    {
        return $this->storageName;
    }

    /**
     * Set the storage object for this field
     */
    public function setStorage(AbstractStorage $storage): void
    {
        $this->storage = $storage;
    }

    /**
     * Gets the storage object for this field
     */
    public function getStorage(): AbstractStorage
    {
        return $this->storage;
    }

    /** @return mixed */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Sets the value of the field after validating it
     * @param mixed $value
     * @param \WP_REST_Request|null $request Pass the request object for
     * context-aware validation
     * @throws \LogicException when storage is not set by developer
     * @throws ValidatorFailedException when validation fails
     */
    public function setValue($value, ?\WP_REST_Request $request = null): void
    {
        if (empty($this->storage)) {
            throw new \LogicException('Storage not set for field: ' . esc_html($this->name) . '. First call setStorage() before setValue().');
        }

        $this->validate($value, $request);
        $this->value = $value;
    }

    /**
     * Retrieves the value of the field from storage but only if {@see setValue}
     * was not called before. If it was, the set value is returned. Method
     * returns default value when no value is found in storage.
     * @return mixed
     * @throws \LogicException when storage is not set by developer
     * @throws \Exception
     */
    public function getValue()
    {
        if (!empty($this->value)) {
            return $this->castValue($this->value);
        }

        if (empty($this->storage)) {
            throw new \LogicException('Storage not set for field: ' . esc_html($this->name) . '. First call setStorage() before getValue().');
        }

        $value = $this->storage->get($this->getSettingName());

        return is_null($value) ? $this->getDefaultValue() : $this->castValue($value);
    }

    /**
     * Casts the value to the type of the field
     * @param mixed $value
     * @return mixed
     */
    protected function castValue($value)
    {
        if ($this->isBoolean()) {
            return (bool) $value;
        }

        if ($this->isInteger()) {
            return (int) $value;
        }

        if ($this->isFloat()) {
            return (float) $value;
        }

        if ($this->isString()) {
            return (string) $value;
        }

        if ($this->isArray()) {
            return (array) $value;
        }

        if ($this->isObject()) {
            return (object) $value;
        }

        return $value;
    }

    /**
     * Adds a validator of type {@see AbstractValidator} to the field. Each
     * validator will be used to validate a given value for the field.
     */
    public function addValidator(AbstractValidator $validator): void
    {
        $this->validators[] = $validator;
    }

    /**
     * Validates the value of the field against this field's validators
     * @param mixed $value
     * @throws ValidatorFailedException
     */
    public function validate($value, ?\WP_REST_Request $request = null): void
    {
        foreach ($this->validators as $validator) {
            $validator->validate($value, $request);
        }
    }

    /**
     * Apply the configuration array to the field
     */
    public function applyConfig(array $config = []): self
    {
        $this->type = $config['type'] ?? 'string';
        $this->section = $config['section'] ?? '';
        $this->storageName = $config['storage'] ?? 'default';
        $this->settingName = $config['settingName'] ?? null;
        $this->defaultValue = $config['defaultValue'] ?? null;

        // Check if we should add the type validator, default is true
        // Override by setting 'validateType' to false in config
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

    public function isBoolean(): bool
    {
        return $this->type === 'boolean';
    }

    public function isInteger(): bool
    {
        return $this->type === 'integer';
    }

    public function isFloat(): bool
    {
        return $this->type === 'float';
    }

    public function isString(): bool
    {
        return $this->type === 'string';
    }

    public function isArray(): bool
    {
        return $this->type === 'array';
    }

    public function isObject(): bool
    {
        return $this->type === 'object';
    }
}
