import { FeedbackNotice, FlexContainer } from "../components";
import { __, sprintf } from "@wordpress/i18n";
import DOMPurify from "dompurify";
import { useGlobalContext } from "../context/GlobalContext.tsx";

type FetchingErrorFeedbackNoticeProps = {
    errorUpdateCount: number,
    refetch: () => void
};
const FetchingErrorFeedbackNotice = ({ errorUpdateCount, refetch }: FetchingErrorFeedbackNoticeProps) => {
    const { metricool } = useGlobalContext();
    return (
        <FlexContainer direction={"column"} className={"justify-center items-center w-full grow"}>
            <FeedbackNotice
                status={"error"}
                action={(
                    <>
                        {errorUpdateCount <= 1 ? (
                            <div
                                className={"text-rss-blue underline cursor-pointer"}
                                onClick={() => refetch()}
                            >
                                {__("Try again", "metricool")}
                            </div>
                        ) : (
                            <div
                                className={"text-rss-blue underline cursor-pointer"}
                                onClick={() => window.location.reload()}
                            >
                                {__("Refresh page", "metricool")}
                            </div>)
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
                                            /*! translators: the two variables are opening and closing anchor tags */
                                            __("If the error persists, please contact our %ssupport team%s.", "metricool"),
                                            `<a href=${metricool.trusted_urls.new_support_ticket} target="_blank">`,
                                            `</a>`),
                                        { ADD_ATTR: ["target"] }
                                    )
                            }}
                        >

                        </div>
                    </>

                )}
            </FeedbackNotice>
        </FlexContainer>
    );
};

export default FetchingErrorFeedbackNotice;