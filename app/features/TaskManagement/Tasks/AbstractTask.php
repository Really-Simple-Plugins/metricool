<?php

namespace Metricool\Features\TaskManagement\Tasks;

use Metricool\Interfaces\TaskInterface;

abstract class AbstractTask implements TaskInterface
{
    public const STATUS_URGENT = 'urgent';
    public const STATUS_OPEN = 'open';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_DISMISSED = 'dismissed';
    public const STATUS_HIDDEN = 'hidden';

    public const STATE_URGENT = ['status' => self::STATUS_URGENT, 'priority' => 0];
    public const STATE_OPEN = ['status' => self::STATUS_OPEN, 'priority' => 10];
    public const STATE_COMPLETED = ['status' => self::STATUS_COMPLETED, 'priority' => 20];
    public const STATE_DISMISSED = ['status' => self::STATUS_DISMISSED, 'priority' => 30];
    public const STATE_HIDDEN = ['status' => self::STATUS_HIDDEN, 'priority' => 40];
    public const STATE_PREMIUM = ['priority' => 15, 'is_premium' => true];
    public const STATE_SPECIAL_FEATURE = ['priority' => 15, 'is_special_feature' => true];

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
    private string $status;

    private string $priority;

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

        // Return a different priority for tasks that are premium or special features
        if ($this->isPremium()) {
            return self::STATE_PREMIUM['priority'];
        }
        if ($this->isSpecialFeature()) {
            return self::STATE_SPECIAL_FEATURE['priority'];
        }

        return $this->priority ?? self::STATE_OPEN['priority'];
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
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * Gets the priority of the task.
     */
    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
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
     * Method is used to set that state of the task. For all available
     * states {@see AbstractTask::TASK_STATES} constant.
     */
    public function fill(array $data): void
    {
        if (isset($data['status'])) {
            $this->setStatus($data['status']);
        }

        if (isset($data['priority'])) {
            $this->setPriority($data['priority']);
        }

        if (isset($data['is_premium'])) {
            $this->setPremium($data['is_premium']);
        }

        if (isset($data['is_special_feature'])) {
            $this->setSpecialFeature($data['is_special_feature']);
        }
    }

    /**
     * Activate the task by setting the state to 'open'
     */
    public function open(): void
    {
        $this->fill(self::STATE_OPEN);
    }

    /**
     * Set the task to 'urgent' state
     */
    public function urgent(): void
    {
        $this->fill(self::STATE_URGENT);
    }

    /**
     * Dismiss the task by setting the state to 'dismissed'. Only allowed if
     * the task is not required.
     */
    public function dismiss(): void
    {
        if ($this->required) {
            return; // Not allowed
        }

        $this->fill(self::STATE_DISMISSED);
    }

    /**
     * Complete the task by setting the state to 'completed'
     */
    public function completed(): void
    {
        $this->fill(self::STATE_COMPLETED);
    }

    /**
     * Hide the task by setting the state to 'hidden'
     */
    public function hide(): void
    {
        $this->fill(self::STATE_HIDDEN);
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