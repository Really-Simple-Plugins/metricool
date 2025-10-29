<?php

namespace Metricool\Http\Endpoints\Responses;

class ConnectedNetworksResponse extends Response
{
    /** @var array $brandSettings Response from Metricool /v2/settings/brands/{blogId} */
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

        foreach ($this->brandSettings['networksData'] as $network => $networkData) {
            $networkName = str_replace('Data', '', $network);
            $networks[$networkName] = $networkData;
        }

        return $networks;
    }

    /**
     * @inheritDocX
     */
    public function body(): array
    {
        $results = $this->parse();

        return [
            'connected_networks' => $results,
        ];
    }
}