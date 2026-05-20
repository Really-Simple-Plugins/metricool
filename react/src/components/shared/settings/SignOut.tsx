import { ButtonGroup as PrimitiveButtonGroup } from "@/components/shared/primitives/button-group";
import { Button } from "@/components/shared/forms/Button";
import { Input } from "@/components/shared/forms/Input";
import { Icon } from "@/components/shared/user-feedback/Icon";
import { FieldLabel as PrimitiveFieldLabel } from "@/components/shared/primitives/field";
import { __ } from "@wordpress/i18n";

type SignOutProps = {
    onSignOut: () => void,
    currentUserEmail: string,
};

const SignOut = ({
    onSignOut,
    currentUserEmail,
}: SignOutProps) => {
    return (
        <>
            <PrimitiveFieldLabel htmlFor={"current-user-email"} className={"text-md font-semibold"}>
                {__("Currently logged in as", "{{TEXT_DOMAIN}}")}
            </PrimitiveFieldLabel>
            <PrimitiveButtonGroup className={"w-full"}>
                <Input id={"current-user-email"} disabled value={currentUserEmail}/>
                <Button
                    variant={"primary"}
                    onClick={() => onSignOut()}
                    size={"lg"}
                >
                    {__("Log Out", "{{TEXT_DOMAIN}}")}
                    <Icon icon={"sign-out"}/>
                </Button>
            </PrimitiveButtonGroup>
        </>
    );
};

export { SignOut };