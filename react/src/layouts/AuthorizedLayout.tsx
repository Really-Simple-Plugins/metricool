import { FlexContainer } from "@/components/shared";
import { Header } from "@/components/custom";

const AuthorizedLayout = ({ children }: React.ComponentProps<"div">) => {
    return (
        <FlexContainer direction={"column"} className={"h-full w-full min-[125rem]:items-center"}>
            <Header/>
            {children}
        </FlexContainer>
    );
};

export default AuthorizedLayout;