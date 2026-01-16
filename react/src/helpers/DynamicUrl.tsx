type DynamicUrlProps = {
    baseUrl: string;
}

/**
 * DynamicUrl class to handle urls with a single host but dynamic paths and parameters.
 */
class DynamicUrl {
    private dynamicUrl: URL;

    /**
     * Constructor to initialize the DynamicUrl.
     * @param properties - Object with baseUrl
     */
    constructor({ baseUrl }: DynamicUrlProps) {
        this.dynamicUrl = new URL(baseUrl);
    }

    /**
     * Returns the dynamicUrl as a string.
     * @returns the dynamicUrl as a string
     */
    public get() {
        return this.dynamicUrl.href;
    }

    /**
     * Sets optional search parameters.
     * @param parameters - Object with parameters to be set.
     * @returns The DynamicUrl instance.
     */
    public setParameters(parameters: Record<string, string>) {
        // if (!this.route || this.route === this.httpClientSettings.MC_API_URL) {
        //     throw new Error(__("Route not set. Please use setRoute before using setFilters.", "metricool"));
        // }

        Object.entries(parameters).forEach(([paramKey, paramValue]) => {
            this.dynamicUrl.searchParams.set(`filters[${paramKey}]`, paramValue);
        });

        return this;
    }

    /**
     * Sets optional pathname.
     * @param pathname - Object with parameters to be set.
     * @returns The DynamicUrl instance.
     */
    public setPath(pathname: string) {
        this.dynamicUrl.pathname = pathname;
        return this;
    }


}

export default DynamicUrl;