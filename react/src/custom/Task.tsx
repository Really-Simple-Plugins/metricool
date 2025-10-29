import { FlexContainer } from "../components";
import { Badge, Button } from "../components";
import { capitalizeFirstCharacter } from "../functions/utils.tsx";

export type TaskProps = {
    id: string,
    text: string,
    label: string,
    status: "open" | "urgent" | "completed" | "dismissed" | "hidden" | "premium",
    type: "required" | "optional",
    premium: boolean;
    special_feature: boolean,
    priority: number,
    action?: {
        text: string,
        link?: string,
        login_link?: string,
        target?: string,
        modal?: {
            id: string,
        }
    }
};

const Task = ({ task: { status, text, action }, onDismiss }: { task: TaskProps, onDismiss?: () => void }) => {
    return (
        <FlexContainer direction={"row"} className={"justify-between"}>
            <FlexContainer direction={"row"} className={"items-center"}>
                <Badge variant={status}>{capitalizeFirstCharacter(status)}</Badge>
                <div className={"font-semibold"}>{text}</div>
            </FlexContainer>
            <FlexContainer direction={"row"} className={"items-center"}>
                {action && (
                    <div className={"underline cursor-pointer"} onClick={()=> {window.open(action?.link, action?.target); window.focus();}}>
                        <span className={"text-nowrap"}>
                            {action.text}
                        </span>
                    </div>
                )}
                <div className={"w-4 h-4"}>
                    {!(status === "completed" || status === "dismissed") && (
                        <Button variant={"icon"} size={"icon"} icon={"close"} iconPosition={"right"} onClick={onDismiss}/>
                    )}
                </div>
            </FlexContainer>
        </FlexContainer>
    );
};

export default Task;