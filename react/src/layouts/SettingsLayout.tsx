import FlexContainer from "../custom/FlexContainer.tsx";
import Header from "../custom/Header.tsx";

export const SettingsLayout = () => {
    return (
        <FlexContainer direction={"column"} className={"h-full w-full"}>
            <Header />
            <FlexContainer direction={"row"} className={"px-4 w-full"}>

            </FlexContainer>
        </FlexContainer>
    );
};