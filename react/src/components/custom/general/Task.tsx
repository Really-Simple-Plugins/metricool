import { Badge, type BadgeVariantsProps, Button, FlexContainer, Icon } from "@/components/shared";
import { clsx } from "clsx";

export type TaskProps = {
    id: string,
    text: string,
    label: string,
    status: "open" | "urgent" | "completed" | "dismissed" | "hidden",
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
        },
    },
};

const Task = ({
    task: {
        status,
        text,
        action,
        type,
        label,
        premium,
        special_feature
    },
    badgeClass,
    onDismiss
}: {
    task: TaskProps,
    badgeClass?: string,
    onDismiss?: () => void
}) => {
    const taskIsDismissable = type === "optional" && ["open", "urgent", "premium"].includes(status);
    const getBadgeVariant = (): BadgeVariantsProps["variant"] => {
        if (status === "completed") {
            return "completed";
        }
        if (status === "dismissed") {
            return "dismissed";
        }
        if (premium) {
            return "premium";
        }
        if (special_feature) {
            return "special-feature";
        }
        if (status === "hidden") {
            return "default";
        }
        return status;
    };

    return (
        <FlexContainer direction={"row"} className={"justify-between"}>
            <FlexContainer direction={"row"} className={"items-center"}>
                <Badge
                    variant={getBadgeVariant()}
                    className={badgeClass}
                >
                    {label}
                </Badge>
                <div className={clsx(
                    "font-semibold",
                    status === "dismissed" && "text-gray-400 line-through",
                )}>
                    {text}
                </div>
            </FlexContainer>
            <FlexContainer direction={"row"} className={"items-center"}>
                {action && (
                    <Button variant={"link"} link={action.link ? action.link : ""}>
                        <span className={"text-nowrap"}>
                            {action.text}
                        </span>
                    </Button>
                )}
                <div className={"w-4 h-4"}>
                    {taskIsDismissable && (
                        <Button
                            variant={"icon"}
                            size={"icon"}
                            onClick={onDismiss}
                        >
                            <Icon icon={"close"}/>
                        </Button>
                    )}
                </div>
            </FlexContainer>
        </FlexContainer>
    );
};

export { Task };