<?php

namespace Metricool\Features\UserSettings\Interfaces;

interface SubmittableStorageInterface
{
    /**
     * This method should be used to submit any pending changes to the storage.
     */
    public function submit(): void;
}