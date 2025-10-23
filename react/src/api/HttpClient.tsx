import { __ } from "@wordpress/i18n";

type HttpClientSettings = {
    NONCE: string;
    X_WP_NONCE: string;
    MC_API_URL: string;
}

/**
 * HttpClient class to handle HTTP requests.
 */
class HttpClient {
    private route: string | null = null;

    private httpClientSettings: HttpClientSettings;

    private getMethodHeaders: Record<string, string>;

    private postMethodHeaders: Record<string, string>;

    private payload: Record<string, string>;

    /**
     * Constructor to initialize the HttpClient with the right settings and optionally a route.
     * @param settings - Object with NONCE, X_WP_NONCE, and MC_API_URL
     * @param route - The API route to be used.
     */
    constructor(settings: HttpClientSettings, route?: string,) {
        console.log(settings.MC_API_URL);
        this.httpClientSettings = {
            NONCE: settings.NONCE,
            MC_API_URL: settings.MC_API_URL,
            X_WP_NONCE: settings.X_WP_NONCE
        };

        this.getMethodHeaders = {
            "X-WP-NONCE": settings.X_WP_NONCE,
        };

        this.postMethodHeaders = {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-WP-NONCE": settings.X_WP_NONCE,
        };

        this.payload = {
            "nonce": settings.NONCE,
        };

        if (route) {
            this.route = settings.MC_API_URL + route;
        }
    }

    /**
     * Performs a GET request.
     * @returns The response data in JSON format.
     * @throws Error if the response is not ok or route is not set.
     */
    async get() {
        if (!this.route) {
            throw new Error(__("Route is not set", "metricool"));
        }

        return await this.request(this.route, "GET");
    }

    /**
     * Performs a POST request.
     * @param body - The body of the POST request.
     * @returns The response data in JSON format.
     * @throws Error if the response is not ok or route/payload is not set.
     */
    async post(body?: { [key: string | number]: string | number }) {
        const payload = body ?? this.payload;

        if (!payload) {
            throw new Error(__("Payload is not set", "metricool"));
        }

        if (!this.route) {
            throw new Error(__("Route is not set", "metricool"));
        }

        return await this.request(this.route, "POST", payload);
    }

    /**
     * Wrapper method for a fetch request.
     * @param route - The route for the request.
     * @param method - The request method.
     * @param payload - The payload to be sent - optional.
     * @returns The response data in JSON format.
     * @throws Error if the response is not ok.
     */
    private async request(route: string, method: "GET" | "PUT" | "POST" | "DELETE", payload?: object) {
        this.resetRoute();
        this.resetPayload();

        const response = await fetch(route, {
            method: method,
            headers: this.postMethodHeaders,
            ...(payload && {
                body: JSON.stringify({
                    ...payload,
                    nonce: this.httpClientSettings.NONCE,
                })
            }),
        });

        if (!response.ok) {
            const errorData = await response.json();
            return this.handleError(errorData);
        }
        return response.json();
    }

    /**
     * Sets the route URL.
     * @param route - The API route to be used.
     * @returns The HttpClient instance.
     */
    public setRoute(route: string) {
        this.route = this.httpClientSettings.MC_API_URL + route;
        return this;
    }

    /**
     * Sets custom headers for GET or POST requests.
     * @param headers - The headers to be set.
     * @param method - The HTTP method ('get' or 'post').
     * @returns The HttpClient instance.
     */
    public setHeaders(headers: Record<string, string>, method: "get" | "post") {
        if (method === "get") {
            this.getMethodHeaders = {
                ...this.getMethodHeaders,
                ...headers,
            };
            return this;
        }

        if (method === "post") {
            this.postMethodHeaders = {
                ...this.postMethodHeaders,
                ...headers,
            };
            return this;
        }

        return this;
    }

    /**
     * Sets additional payload data.
     * @param payload - The payload data to be set.
     * @returns The HttpClient instance.
     */
    public setPayload(payload: Record<string, any>) {
        this.payload = {
            ...this.payload,
            ...payload,
        };
        return this;
    }

    /**
     * Sets optional filters.
     * @param filters - Object with filters to be set.
     * @returns The HttpClient instance.
     */
    public setFilters(filters: Record<string, string>) {
        if (!this.route || this.route === this.httpClientSettings.MC_API_URL) {
            throw new Error(__("Route not set. Please use setRoute before using setFilters.", "metricool"));
        }
        const metricoolApiUrl = new URL(this.route);
        Object.entries(filters).forEach(([filterKey, filterValue]) => {
            metricoolApiUrl.searchParams.set(`filters[${filterKey}]`, filterValue);
        });
        this.route = metricoolApiUrl.toString();
        return this;
    }

    /**
     * Reset payload data to default value.
     * Needs to be called after every request, as the client instance can
     * be used for multiple request.
     */
    private resetPayload() {
        this.payload = {
            "nonce": this.httpClientSettings.NONCE,
        };
    }

    /**
     * Reset route back to null.
     * Needs to be called after every request, as one client instance can
     * be used for multiple different endpoints.
     */
    private resetRoute() {
        this.route = null;
    }

    /**
     * Handles errors from the server response.
     * @param errorData - The error data from the server.
     * @throws An error with a message.
     */
    private handleError(errorData: any) {
        let error: string = __("An error occurred", "metricool");

        if (typeof errorData === "string") {
            error = errorData;
        }

        if (errorData?.message) {
            error = errorData.message;
        }

        if (errorData?.error) {
            error = errorData.error;
        }

        throw new Error(error);
    }
}

export default HttpClient;