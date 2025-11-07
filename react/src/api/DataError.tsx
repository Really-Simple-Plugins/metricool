export class DataError extends Error {
    public fields: Record<string, unknown> | undefined;

    constructor(message: string, fields?: Record<string, unknown> , ...params: undefined[]) {
        super(message, ...params);
        this.name = "DataError";
        this.fields = fields;

        // Maintains proper stack trace for where our error was thrown (non-standard)
        if (Error.captureStackTrace) {
            Error.captureStackTrace(this, DataError);
        }
    }
}