<?php

namespace Metricool\Http\Endpoints\Responses;

class ConnectedNetworksResponse extends Response
{
    /**
     * Response from Metricool /v2/settings/brands/{blogId}
     * @var array $brandSettings
     */
    protected array $brandSettings = [];

    public function __construct(array $brandSettings)
    {
        $this->brandSettings = $brandSettings;
    }

    /**
     * Parse the brandSettings response and extract just the connected networks
     */
    protected function parse(): array
    {
        $networks = [];

        if (!isset($this->brandSettings['networksData'])) {
            return $networks;
        }

        foreach ($this->brandSettings['networksData'] as $network => $networkData) {
            $networkName = str_replace('Data', '', $network);
            $networks[$networkName] = $networkData;
        }

        return $networks;
    }

    /**
     * @inheritDoc
     */
    public function body(): array
    {
        return $this->parse();
    }
}