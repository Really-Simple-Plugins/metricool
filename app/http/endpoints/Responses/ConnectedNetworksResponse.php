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

    protected function parse(): array
    {
        $networks = [];

        foreach ($this->brandSettings['networksData'] as $network => $networkData) {
            $networks[] = str_replace('Data', '', $network);
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