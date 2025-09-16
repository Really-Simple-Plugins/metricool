import { clsx } from "clsx";
import FlexContainer from "./FlexContainer.tsx";
import { Button } from "../components";

const FormFooter = () => {
    return (
        <div className={clsx("sticky bottom-0 start-0 z-10 shadow-md bg-gray-50 w-full transition-all ease-in-out duration-200")}>
            <FlexContainer direction={"row"} className={"justify-end items-center p-2"}>
                <Button variant={"black"}>Save changes</Button>
            </FlexContainer>
        </div>
    );
};

export default FormFooter;