import { FlexContainer } from "@/components/shared/general/FlexContainer.tsx";
import { Alert } from "@/components/shared/user-feedback/Alert.tsx";
import { __, sprintf } from "@wordpress/i18n";
import DOMPurify from "dompurify";
import { Button } from "@/components/shared/forms/Button.tsx";

type FetchingErrorFeedbackNoticeProps = {
    errorUpdateCount: number,
    refetch: () => void,
    supportTicketLink: string,
};

/**
 *
 * @version 1.0.0
 */
const FetchingErrorAlert = ({ errorUpdateCount, refetch, supportTicketLink }: FetchingErrorFeedbackNoticeProps) => {
    return (
        <FlexContainer direction={"column"} className={"justify-center items-center w-full grow"}>
            <Alert
                variant={"error"}
                action={(
                    <>
                        {errorUpdateCount <= 1 ? (
                            <Button
                                variant={"link"}
                                className={"text-rss-blue font-semibold"}
                                onClick={() => refetch()}
                            >
                                {__("Try again", "metricool")}
                            </Button>
                        ) : (
                            <Button
                                variant={"link"}
                                className={"text-rss-blue font-semibold"}
                                onClick={() => window.location.reload()}
                            >
                                {__("Refresh page", "metricool")}
                            </Button>)
                        }
                    </>
                )}
            >
                <div>
                    {__("There was an error fetching this data.", "metricool")}
                </div>
                {errorUpdateCount > 1 && (
                    <>
                        <div>
                            {__("Please try refreshing the page.", "metricool")}
                        </div>
                        <div
                            dangerouslySetInnerHTML={{
                                __html:
                                    DOMPurify.sanitize(
                                        sprintf(
                                            /*translators: the two variables are opening and closing anchor tags */
                                            __("If the error persists, please contact our %1$ssupport team%2$s.", "metricool"),
                                            `<a href=${supportTicketLink} target="_blank">`,
                                            `</a>`),
                                        { ADD_ATTR: ["target"] }
                                    )
                            }}
                        >

                        </div>
                    </>

                )}
            </Alert>
        </FlexContainer>
    );
};

export { FetchingErrorAlert };