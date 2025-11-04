<?php

namespace Metricool\Features\TaskManagement\Tasks;

use Metricool\Interfaces\TaskInterface;

abstract class AbstractTask implements TaskInterface
{
    protected const DEFAULT_PRIORITY = 10;

    public const STATUS_URGENT = 'urgent';
    public const STATUS_OPEN = 'open';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_DISMISSED = 'dismissed';
    public const STATUS_HIDDEN = 'hidden';

    /**
     * Statuses that are used to determine the state of the task. A state holds
     * properties that are used for a task with this status.
     */
    protected const STATUS_STATES = [
        self::STATUS_URGENT => ['priority' => 0],
        self::STATUS_OPEN => ['priority' => self::DEFAULT_PRIORITY],
        self::STATUS_COMPLETED => ['priority' => 20],
        self::STATUS_DISMISSED => ['priority' => 30],
        self::STATUS_HIDDEN => ['priority' => 40],
    ];

    /**
     * These states are used to determine the properties of a task that is not
     * based on a status.
     * @see self::getPriority()
     */
    protected const STATE_PREMIUM = ['priority' => 15];
    protected const STATE_SPECIAL_FEATURE = ['priority' => 15];

    /**
     * Override this constant to define the identifier of the task. This
     * identifier is used to identify the task in the database and in the UI.
     */
    const IDENTIFIER = '';

    /**
     * Override this property to define the version of the task. This version is
     * used to determine if the task should be upgraded during a plugin update.
     */
    protected string $version;

    /**
     * Override this property to define if the task is required or not. If the
     * task is required, the user will not be able to dismiss the task.
     */
    protected bool $required;

    /**
     * Override this property to define if the task should be reactivated when
     * the task is upgraded. This is useful for tasks that are dismissed by the
     * user but should be reactivated when the task is upgraded to a new
     * version.
     */
    protected bool $reactivateOnUpgrade;

    /**
     * Use this property to define if the task is a premium task. Useful for
     * the UI.
     */
    protected bool $premium;

    /**
     * Use this property to define if the task is related to a special feature
     * or not. Useful for the UI.
     */
    protected bool $specialFeature;

    /**
     * By default, a task is active on construct. This is because the $status
     * property is not set. The {@see getStatus()} method will therefore return
     * the default status 'open'. If you want to set a different default status
     * use the {@see setStatus()} method in the construct of the task. See
     * {@see AddMandatoryProviderTask} for an example.
     */
    protected string $status;

    /**
     * Override this method to define the text that should be displayed to the
     * user in the tasks dashboard component
     * @abstract
     */
    abstract public function getText(): string;

    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        return static::IDENTIFIER;
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return $this->version ?? '1.0.0';
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): string
    {
        return $this->status ?? self::STATUS_OPEN;
    }

    /**
     * @inheritDoc
     */
    public function getPriority(): int
    {
        if ($this->isPremium()) {
            return self::STATE_PREMIUM['priority'];
        }
        if ($this->isSpecialFeature()) {
            return self::STATE_SPECIAL_FEATURE['priority'];
        }

        $state = self::getStateFromStatus($this->getStatus());

        return $state['priority'] ?? self::DEFAULT_PRIORITY;
    }

    /**
     * Returns the priority of the task based on the status.
     * @param string $status
     * @return array|null
     */
    protected function getStateFromStatus(string $status): ?array
    {
        return self::STATUS_STATES[$status] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function reactivateOnUpgrade(): bool
    {
        return $this->reactivateOnUpgrade ?? false;
    }

    /**
     * @inheritDoc
     */
    public function getAction(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function setStatus(string $status): void
    {
        if ($status == self::STATUS_DISMISSED && $this->isDismissable() === false) {
            throw new \Exception('Task is required and cannot be dismissed');
        }

        $this->status = $status;
    }

    /**
     * Sets the premium property of the task.
     */
    public function setPremium(bool $isPremium): void
    {
        $this->premium = $isPremium;;
    }

    /**
     * Sets the special feature property of the task.
     */
    public function setSpecialFeature(bool $isSpecialFeature): void
    {
        $this->specialFeature = $isSpecialFeature;
    }

    /**
     * Activate the task by setting the state to 'open'
     * @throws \Exception
     */
    public function open(): void
    {
        $this->setStatus(self::STATUS_OPEN);
    }

    /**
     * Set the task to 'urgent' state
     * @throws \Exception
     */
    public function urgent(): void
    {
        $this->setStatus(self::STATUS_URGENT);
    }

    /**
     * Check if the task can be dismissed.
     */
    public function isDismissable(): bool
    {
        return $this->required === false;
    }

    /**
     * Dismiss the task by setting the state to 'dismissed'
     * @throws \Exception
     */
    public function dismiss(): void
    {
        $this->setStatus(self::STATUS_DISMISSED);
    }

    /**
     * Complete the task by setting the state to 'completed'
     * @throws \Exception
     */
    public function completed(): void
    {
        $this->setStatus(self::STATUS_COMPLETED);
    }

    /**
     * Hide the task by setting the state to 'hidden'
     * @throws \Exception
     */
    public function hide(): void
    {
        $this->setStatus(self::STATUS_HIDDEN);
    }

    /**
     * Reads if the task is completed
     */
    public function isCompleted(): bool
    {
        return $this->getStatus() === self::STATUS_COMPLETED;
    }

    /**
     * Reads if the task is dismissed
     */
    public function isDismissed(): bool
    {
        return $this->status === self::STATUS_DISMISSED;
    }

    /**
     * Reads if the task is required
     */
    public function isHidden(): bool
    {
        return $this->status === self::STATUS_HIDDEN;
    }

    /**
     * Reads if the task is required
     */
    public function isRequired(): bool
    {
        return $this->required ?? false;
    }

    /**
     * Reads if the task is premium
     */
    public function isPremium(): bool
    {
        return $this->premium ?? false;
    }

    /**
     * Reads if the task is related to a special feature
     */
    public function isSpecialFeature(): bool
    {
        return $this->specialFeature ?? false;
    }

    /**
     * Build the label for the task. This is used to display the task in the
     * tasks dashboard component. The label is used to indicate if the task
     * is premium or a special feature. If not, the label reflects the status.
     */
    public function getLabel(): string
    {
        if ($this->isPremium()) {
            return esc_html__('Premium', 'metricool');
        }

        if ($this->isSpecialFeature()) {
            return esc_html__('Special feature', 'metricool');
        }

        return ucfirst($this->getStatus());
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'text' => $this->getText(),
            'label' => $this->getLabel(),
            'status' => $this->getStatus(),
            'priority' => $this->getPriority(),
            'premium' => $this->isPremium(),
            'special_feature' => $this->isSpecialFeature(),
            'type' => $this->isRequired() ? 'required' : 'optional',
            'action' => $this->getAction(),
        ];
    }

}