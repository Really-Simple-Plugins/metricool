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
     * Function to obtain the string value of the DynamicUrl using the URL api's
     * built in .href stringifier.
     * @see https://developer.mozilla.org/en-US/docs/Web/API/URL/href
     * @returns the dynamicUrl as a string
     */
    public toString() {
        return this.dynamicUrl.href;
    }

    /**
     * This method can be used to combine {@link setPath} and
     * {@link toString} in a single call.
     * @param {string} pathname - string representing the new pathname.
     * @returns the dynamicUrl as a string including the new pathname.
     */
    public withPath(pathname: string) {
        return this.setPath(pathname).toString();
    }


    /**
     * Sets optional search parameters.
     * Overwrites an existing search param if the key is the same, otherwise adds it.
     * @param {Object} parameters - Object with parameters to be set.
     * @returns The DynamicUrl instance.
     */
    public setSearchParams(parameters: Record<string, string>) {
        Object.entries(parameters).forEach(([paramKey, paramValue]) => {
            this.dynamicUrl.searchParams.set(paramKey, paramValue);
        });

        return this;
    }

    /**
     * Sets optional pathname.
     * Overwrites any previous pathname.
     * @param {string} pathname - string representing the new pathname.
     * @returns The DynamicUrl instance.
     */
    public setPath(pathname: string) {
        this.dynamicUrl.pathname = pathname.trim();
        return this;
    }
}

export default DynamicUrl;