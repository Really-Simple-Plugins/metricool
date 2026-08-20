import { Link } from "@tanstack/react-router";
import { TaskStatusBadge } from "@/components/shared/dashboard/TaskStatusBadge";
import { Button } from "@/components/shared/forms/Button";
import { FlexContainer } from "@/components/shared/general/FlexContainer";
import { Icon } from "@/components/shared/user-feedback/Icon";
import { cn } from "@/support/functions/utils";

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
        route?: string,
        link?: string,
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
    const taskIsDismissable = type === "optional" && ["open", "urgent"].includes(status);

    return (
        <FlexContainer direction={"row"} className={"justify-between"}>
            <FlexContainer direction={"row"} className={"items-center"}>
                <TaskStatusBadge
                    status={status}
                    isPremium={premium}
                    isSpecialFeature={special_feature}
                    className={badgeClass}
                >
                    {label}
                </TaskStatusBadge>
                <div
                    className={cn(
                        "font-semibold",
                        {
                            "text-gray-400 line-through": (status === "dismissed"),
                        }
                    )}
                >
                    {text}
                </div>
            </FlexContainer>
            <FlexContainer direction={"row"} className={"items-center"}>
                {action && (
                    action.link ? (
                        <Button
                            variant={"link"}
                            size={"link"}
                            link={action.link}
                            target={action.target ?? "_self"}
                        >
                            <span className={"text-nowrap"}>
                                {action.text}
                            </span>
                        </Button>
                    ) : (
                        <Link
                            to={action.route}
                            className={"text-black hover:text-wordpress-link-hover font-normal underline leading-(--text-md)"}
                        >
                            <span className={"text-nowrap"}>
                                {action.text}
                            </span>
                        </Link>
                    )
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