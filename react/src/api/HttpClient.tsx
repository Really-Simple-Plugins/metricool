import { __ } from "@wordpress/i18n";
import { DataError } from "./DataError.tsx";

type HttpClientSettings = {
    NONCE: string;
    X_WP_NONCE: string;
    METRICOOL_API_URL: string;
}

type HttpMethod = "GET" | "PUT" | "POST" | "DELETE";

type GenericRecord<T> = Record<string, T>;

/**
 * HttpClient class to handle HTTP requests.
 */
class HttpClient {
    private httpClientSettings: HttpClientSettings;

    private readonly defaultHeaders: Record<string, string>;

    /**
     * Constructor to initialize the HttpClient with the right settings.
     * @param settings - Object with NONCE, X_WP_NONCE, and METRICOOL_API_URL
     */
    constructor(settings: HttpClientSettings) {
        this.httpClientSettings = {
            NONCE: settings.NONCE,
            METRICOOL_API_URL: settings.METRICOOL_API_URL,
            X_WP_NONCE: settings.X_WP_NONCE
        };

        this.defaultHeaders = {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-WP-NONCE": settings.X_WP_NONCE,
        };
    }

    /**
     * Performs a GET request.
     * @param route - The route to send the request to.
     * @param params - Search Parameters to be set - optional.
     * @param headers - Headers to be sent - optional.
     * @returns The response data in JSON format.
     * @throws Error if the response is not ok or route is not set.
     */
    async get<T>(route: string, params?: GenericRecord<T>, headers?: Record<string, string>) {
        return this.request(this.buildRoute(route, params), "GET", undefined, headers);
    }

    /**
     * Performs a POST request.
     * @param route - The route to send the request to.
     * @param payload - The body of the POST request.
     * @param headers - Headers to be sent - optional.
     * @returns The response data in JSON format.
     * @throws Error if the response is not ok or route/payload is not set.
     */
    async post(route: string, payload: Record<string, unknown> = {}, headers?: Record<string, string>) {
        return await this.request(this.buildRoute(route), "POST", payload, headers);
    }

    /**
     * Wrapper method for a fetch request.
     * @param route - The route for the request.
     * @param method - The request method.
     * @param payload - The payload to be sent - optional.
     * @param headers - Headers to be sent - optional.
     * @returns The response data in JSON format.
     * @throws Error if the response is not ok.
     */
    private async request(route: string, method: HttpMethod, payload?: object, headers?: Record<string, string>,) {
        const response = await fetch(route, {
            method: method,
            headers: {
                ...this.defaultHeaders,
                ...headers,
            },
            ...(payload && {
                body: JSON.stringify({
                    ...payload,
                    nonce: this.httpClientSettings.NONCE,
                })
            }),
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => response.statusText);
            return this.handleError(errorData);
        }
        return response.json();
    }

    /**
     * Sets the route URL.
     * @param route - The API route to be used.
     * @param params - query parameters - optional
     * @returns The HttpClient instance.
     */
    private buildRoute<T>(route: string, params?: GenericRecord<T>) {
        const url = new URL(this.httpClientSettings.METRICOOL_API_URL + route);
        if (params) {
            Object.entries(params).forEach(([key, value]) => {
                url.searchParams.set(key, String(value));
            });
        }
        return url.toString();
    }

    /**
     * Handles errors from the server response.
     * @param errorData - The error data from the server.
     * @throws An error with a message.
     */
    private handleError(errorData: string | Record<string, string | Record<string, unknown>>) {
        let error: string = __("An error occurred", "metricool");
        let fields = {};

        if (typeof errorData === "string") {
            error = errorData;
        }

        if (typeof errorData === "object") {
            if (errorData.message && typeof errorData.message === "string") {
                error = errorData.message;
            }

            if (errorData.error && typeof errorData.error === "string") {
                error = errorData.error;
            }

            if (errorData.data && typeof errorData.data === "object" && errorData.data.errors) {
                fields = errorData.data.errors;
            }
        }

        throw new DataError(error, fields);
    }
}

export default HttpClient;