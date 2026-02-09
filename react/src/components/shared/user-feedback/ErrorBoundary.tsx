import { __, sprintf } from "@wordpress/i18n";
import { Button } from "@/components/shared/forms/Button.tsx";
import { showToast } from "@/components/shared/user-feedback/Toast.tsx";

type ErrorBoundaryProps = {
    error: Error,
    supportTicketLink: string,
};
const ErrorBoundary = ({ error, supportTicketLink }: ErrorBoundaryProps) => {

    const copyError = () => {
        navigator.clipboard.writeText(
            `${error.message && error.message.toString()}\n${error.stack && "Stack trace: " + error.stack}`,
        ).then(() => showToast.success(__("Error was copied", "metricool")));
    }

    return (
        <div className={"rounded-md bg-white p-5 text-black shadow-md"}>
            <h3 className={"mb-4 text-xl font-bold text-black"}>
                {__("Uh-oh! We stumbled upon an error.", "metricool")}
            </h3>
            <div className={"mb-6 rounded-sm border bg-gray-50 p-4"}>
                <p className={"mb-2 text-base text-black"}>
                    {error.message}
                </p>
                {error.stack && (
                    <>
                        <p className={"max-h-48 overflow-x-scroll text-xs text-black"}>
                            Stack trace:
                        </p>
                        <p className={"max-h-48 overflow-x-scroll text-xs text-black"}>
                            {error.stack}
                        </p>
                    </>
                )}
                <Button
                    variant={"primary"}
                    className={"mt-4 active:bg-rsp-success active:text-rsp-success-dark active:border-rsp-success-dark transition-colors duration-400 ease-in-out"}
                    onClick={copyError}
                >
                    {__("Copy Error", "metricool")}
                </Button>
            </div>
            <p className={"mb-4 text-black"}>
                {__(
                    "We're sorry for the trouble. Please take a moment to report this issue on the WordPress forums so we can work on fixing it. Here’s how you can report the issue:",
                    "metricool",
                )}
            </p>
            <ol className={"list-inside list-decimal space-y-2 text-black"}>
                <li>
                    {sprintf(
                        /*translators: variable is translated string of `Copy Error` */
                        __(
                            "Copy the error details by clicking the %s button above.",
                            "metricool",
                        ),
                        __("Copy Error", "metricool"),
                    )}
                </li>
                <li>
                    <a
                        href={supportTicketLink}
                        className={"text-blue-600 underline hover:text-blue-800"}
                        target={"_blank"}
                    >
                        {__("Navigate to the Support Forum.", "metricool")}
                    </a>
                </li>
                <li>
                    {__(
                        "If you haven’t already, log in to your WordPress.org account or create a new account.",
                        "metricool",
                    )}
                </li>
                <li>
                    {sprintf(
                        /*translators: variable is a string that should not be translated */
                        __(
                            "Once logged in, click on %s.",
                            "metricool",
                        ),
                        "'Create Topic'",
                    )}
                </li>
                <li>
                    {sprintf(
                        /*translators: variable is a string that should not be translated */
                        __(
                            "Title: Mention %s along with a brief hint of the error.",
                            "metricool",
                        ),
                        "'Error Encountered'",
                    )}
                </li>
                <li>
                    {__(
                        "Description: Paste the copied error details and explain what you were doing when the error occurred.",
                        "metricool",
                    )}
                </li>
                <li>
                    {sprintf(
                        /*translators: variable is a string that should not be translated */
                        __(
                            "Click %s to post your topic. Our team will look into the issue and provide assistance.",
                            "metricool",
                        ),
                        "'Submit'",
                    )}
                </li>
            </ol>
        </div>
    );
};

export { ErrorBoundary };