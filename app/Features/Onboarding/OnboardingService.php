<?php

declare(strict_types=1);

namespace Metricool\Features\Onboarding;

use Metricool\Support\Helpers\Storage;
use Metricool\Traits\HasRestAccess;

class OnboardingService
{
    use HasRestAccess;

    /**
     * Store the onboarding step in the general options without autoload
     */
    public function setCompletedStep(int $step): void
    {
        update_option('metricool_completed_step', $step, false);
    }

    /**
     * Set the onboarding as completed in the general options without autoload
     */
    public function setOnboardingCompleted(): bool
    {
        $this->setCompletedStep(5); // todo
        $this->clearTemporaryData();

        $completedPreviously = get_option('metricool_onboarding_completed', false);
        if ($completedPreviously) {
            return true;
        }

        update_option('metricool_onboarding_completed_unix_timestamp', time(), false);
        return update_option('metricool_onboarding_completed', true, false);
    }

    /**
     * Method can be used to set temporary data for the onboarding process.
     */
    public function setTemporaryData(array $data): void
    {
        $options = get_option('metricool_temporary_onboarding_data', []);
        $options = array_merge($options, $data);
        update_option('metricool_temporary_onboarding_data', $options, false);
    }

    /**
     * Method can be used to retrieve temporary data for the onboarding process.
     * Returns the array of data as a Storage object for easier access.
     */
    public function getTemporaryDataStorage(): Storage
    {
        return new Storage(
            get_option('metricool_temporary_onboarding_data', [])
        );
    }

    /**
     * Method should be used to clear the temporary data for the onboarding.
     */
    public function clearTemporaryData(): void
    {
        delete_option('metricool_temporary_onboarding_data');
    }
}
