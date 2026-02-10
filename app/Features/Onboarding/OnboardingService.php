<?php

declare(strict_types=1);

namespace Metricool\Features\Onboarding;

use Metricool\Http\Metricool\MetricoolApi;
use Metricool\Support\Helpers\Storage;
use Metricool\Traits\HasRestAccess;

class OnboardingService
{
    use HasRestAccess;

    private MetricoolApi $api;

    public function __construct(MetricoolApi $api)
    {
        $this->api = $api;
    }

    public function isOnboardingCompleted(): bool
    {
        return get_option('metricool_onboarding_completed', false);
    }

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

    /**
     * Check if there is only one brand connected to the blog and store it.
     * Doesn't succeed when there are multiple brands connected to the blog.
     */
    public function attemptToStoreBlogId(array $brands): bool
    {
        if (empty($brands)) {
            throw new \RuntimeException('Something went wrong. No blogs found.');
        }

        $canStoreBlogId = count($brands) > 1;
        if (!$canStoreBlogId) {
            return false;
        }

        // Pick the only brand and store it's BlogId
        $brand = reset($brands);
        $this->api->storeBlogId((string) $brand['id']);

        return true;
    }
}
