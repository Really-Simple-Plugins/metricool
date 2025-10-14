import FlexContainer from "./FlexContainer.tsx";
import { Badge, Button } from "../components";
import { capitalizeFirstCharacter } from "../functions/utils.tsx";

export type TaskProps = {
    status: "warning" | "open" | "premium",
    text: string,
    id: number,
    action: {
        text: string,
        link: string
    },
    completed: boolean,
    dismissed: boolean,
}

const Task = ({ task: { status, text, action, completed, dismissed } }: { task: TaskProps }) => {
    return (
        <FlexContainer direction={"row"} className={"justify-between"}>
            <FlexContainer direction={"row"} className={"items-center"}>
                <Badge variant={status}>{capitalizeFirstCharacter(status)}</Badge>
                <div className={"font-semibold"}>{text}</div>
            </FlexContainer>
            <FlexContainer direction={"row"} className={"items-center"}>
                <div className={"underline cursor-pointer"}><span className={"text-nowrap"}>{action.text}</span></div>
                {!(completed || dismissed) ? (
                    <Button variant={"icon"} size={"icon"} icon={"close"} iconPosition={"right"}/>
                ) : (
                    <div className={"w-4 h-4"}></div>
                )}
            </FlexContainer>
        </FlexContainer>
    );
};

export default Task;