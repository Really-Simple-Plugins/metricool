import { Button, FlexContainer, Icon, Input, Label } from "../components";
import { __ } from "@wordpress/i18n";
import { Controller, useForm } from "react-hook-form";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import { clsx } from "clsx";

const formSchema = z.object({
    email: z.email({
        error: () => __("Please enter a valid email address", "metricool"),
    }),
    password: z.string().min(8, {
        error: () => __("Password must be at least 8 characters", "metricool"),
    }),
}).required();

type SignInFormProps = {
    onSubmit: (values: z.infer<typeof formSchema>) => void,
};
const SignInForm = ({ onSubmit }: SignInFormProps) => {
    const {
        handleSubmit,
        formState: { errors: formValidationErrors, dirtyFields },
        control,
    } = useForm<z.infer<typeof formSchema>>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            email: "",
            password: "",
        },
    });

    return (
        <FlexContainer direction={"row"}>
            <form onSubmit={handleSubmit((values) => onSubmit(values))} className={"flex flex-col items-center justify-center gap-2"}>
                <FlexContainer direction={"column"} className={"!gap-2"}>
                    <FlexContainer direction={"column"} className={"!gap-2"}>
                        <Label htmlFor={"email"} className={"text-black"}>{__("Email", "metricool")}</Label>
                        <Controller
                            control={control}
                            render={({ field }) =>
                                <Input {...field} id={"email"} placeholder={__("Enter your email", "metricool")} className={"bg-white font-semibold text-black min-w-76 max-w-80"}/>}
                            name={"email"}
                        />
                        <span className={clsx("text-rsp-error-dark text-sm h-0 opacity-0 transition-all ease-in-out duration-600", formValidationErrors.password?.message && "h-3 opacity-100")}>
                            <Icon icon={"error"} iconClass={"text-rsp-error-dark"} inverse={true}></Icon>
                            {" "}
                            {formValidationErrors.email?.message}
                        </span>
                    </FlexContainer>
                    <FlexContainer direction={"column"} className={"!gap-2"}>
                        <Label htmlFor={"password"} className={"text-black"}>{__("Password", "metricool")}</Label>
                        <Controller
                            control={control}
                            render={({ field }) =>
                                <Input type={"password"} {...field} id={"password"} placeholder={__("Write your password here", "metricool")} className={"bg-white font-semibold text-black min-w-76 max-w-80"}/>}
                            name={"password"}
                        />
                        <span className={clsx("text-rsp-error-dark text-sm h-0 opacity-0 transition-all ease-in-out duration-600", formValidationErrors.password?.message && "h-3 opacity-100")}>
                            <Icon icon={"error"} iconClass={"text-rsp-error-dark"} inverse={true}></Icon>
                            {" "}
                            {formValidationErrors.password?.message}
                        </span>
                    </FlexContainer>
                </FlexContainer>
                <Button
                    variant={"black"}
                    icon={"arrow-right"}
                    iconPosition={"right"}
                    type={"submit"}
                    disabled={!(dirtyFields.email && dirtyFields.password)}
                >
                    {__("Sign in", "metricool")}
                </Button>
            </form>
        </FlexContainer>
    );
};

export default SignInForm;