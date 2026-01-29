import { FlexContainer } from "@/components";
import type { ReactNode } from "react";

type OnboardingHeaderProps = {
    logo: {
        src: string,
        alt: string,
    },
    actions: ReactNode | ReactNode[],
};
const OnboardingHeader = ({ logo, children, actions }: React.ComponentProps<"div"> & OnboardingHeaderProps) => {
    return (
        <FlexContainer direction={"row"} className={"justify-between items-center pb-4"}>
            <FlexContainer direction={"row"} className={"text-base font-bold font-nunito items-center"}>
                <img src={logo.src} alt={logo.alt}/>
                {children}
            </FlexContainer>
            <FlexContainer direction={"row"} className={"text-md font-semibold font-nunito items-center"}>
                {actions}
            </FlexContainer>
        </FlexContainer>
    );
};

export default OnboardingHeader;