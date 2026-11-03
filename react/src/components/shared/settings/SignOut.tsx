import { ButtonGroup as PrimitiveButtonGroup } from "@/components/shared/primitives/button-group";
import { Button } from "@/components/shared/forms/Button";
import { Input } from "@/components/shared/forms/Input";
import { Icon } from "@/components/shared/user-feedback/Icon";
import { FieldLabel as PrimitiveFieldLabel } from "@/components/shared/primitives/field";
import { __ } from "@wordpress/i18n";

type SignOutProps = {
    onSignOut: () => void,
    currentUserEmail: string,
    isPending?: boolean,
};

const SignOut = ({
    onSignOut,
    currentUserEmail,
    isPending = false,
}: SignOutProps) => {
    return (
        <>
            <PrimitiveFieldLabel htmlFor={"current-user-email"} className={"text-md font-semibold"}>
                {__("Currently logged in as", "metricool")}
            </PrimitiveFieldLabel>
            <PrimitiveButtonGroup className={"w-full"}>
                <Input id={"current-user-email"} readOnly={true} aria-readonly={true} value={currentUserEmail}/>
                <Button
                    variant={"primary"}
                    onClick={onSignOut}
                    size={"lg"}
                    disabled={isPending}
                >
                    {__("Log Out", "metricool")}
                    <Icon icon={isPending ? "loading" : "sign-out"}/>
                </Button>
            </PrimitiveButtonGroup>
        </>
    );
};

export { SignOut };