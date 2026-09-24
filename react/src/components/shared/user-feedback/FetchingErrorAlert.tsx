import { FlexContainer } from "@/components/shared/general/FlexContainer";
import { Alert } from "@/components/shared/user-feedback/Alert";
import { __, sprintf } from "@wordpress/i18n";
import DOMPurify from "dompurify";
import { Link } from "@/components/shared/general/Link";

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
                            <Link
                                className={"text-rss-blue font-semibold whitespace-nowrap"}
                                onClick={() => refetch()}
                            >
                                {__("Try again", "metricool")}
                            </Link>
                        ) : (
                            <Link
                                className="text-rss-blue font-semibold whitespace-nowrap"
                                onClick={() => window.location.reload()}
                            >
                                {__("Refresh page", "metricool")}
                            </Link>)
                        }
                    </>
                )}
            >
                <div>
                    {__("There was an error fetching this data.", "metricool")} 
                </div>
                {errorUpdateCount > 1 && (
                    <>
                        {__("Please try refreshing the page.", "metricool")}
                        <div
                            dangerouslySetInnerHTML={{
                                __html:
                                    DOMPurify.sanitize(
                                        sprintf(
                                            /*translators: the two variables are opening and closing anchor tags */
                                            __("If the error persists, please contact our %1$ssupport team%2$s.", "metricool"),
                                            `<a href=${supportTicketLink} target="_blank" rel="noopener noreferrer">`,
                                            `</a>`),
                                        { ADD_ATTR: ["target"] }
                                    )
                            }}
                        />
                    </>
                )}
            </Alert>
        </FlexContainer>
    );
};

export { FetchingErrorAlert };