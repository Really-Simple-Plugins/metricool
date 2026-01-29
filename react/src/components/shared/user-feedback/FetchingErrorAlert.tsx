import { Alert, FlexContainer } from "@/components";
import { __, sprintf } from "@wordpress/i18n";
import DOMPurify from "dompurify";

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
                            <div
                                className={"text-rss-blue underline cursor-pointer"}
                                onClick={() => refetch()}
                            >
                                {__("Try again", "{{TEXT_DOMAIN}}")}
                            </div>
                        ) : (
                            <div
                                className={"text-rss-blue underline cursor-pointer"}
                                onClick={() => window.location.reload()}
                            >
                                {__("Refresh page", "{{TEXT_DOMAIN}}")}
                            </div>)
                        }
                    </>
                )}
            >
                <div>
                    {__("There was an error fetching this data.", "{{TEXT_DOMAIN}}")}
                </div>
                {errorUpdateCount > 1 && (
                    <>
                        <div>
                            {__("Please try refreshing the page.", "{{TEXT_DOMAIN}}")}
                        </div>
                        <div
                            dangerouslySetInnerHTML={{
                                __html:
                                    DOMPurify.sanitize(
                                        sprintf(
                                            /*! translators: the two variables are opening and closing anchor tags */
                                            __("If the error persists, please contact our %ssupport team%s.", "{{TEXT_DOMAIN}}"),
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