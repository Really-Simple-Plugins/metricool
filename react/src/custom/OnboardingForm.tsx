import { Button, FlexContainer, Icon, Input, Label, Switch } from "../components";
import { __, sprintf } from "@wordpress/i18n";
import { Controller, useForm } from "react-hook-form";
import { clsx } from "clsx";
import DOMPurify from "dompurify";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";

const formSchema = z.object({
    email: z.email({
        error: () => __("Please enter a valid email address", "metricool"),
    }),
    password: z.string().min(8, {
        error: () => __("Password must be at least 8 characters", "metricool"),
    }),
    terms: z.boolean().refine((val) => val === true, {
        error: () => __("Please read and accept the Legal Terms", "metricool"),
    }),
    marketing: z.boolean(),
}).required();

type OnboardingFormProps = {
    onSubmit: (values: z.infer<typeof formSchema>) => void,
};

const OnboardingForm = ({ onSubmit }: OnboardingFormProps) => {
    const {
        handleSubmit,
        formState: { errors: formValidationErrors, dirtyFields },
        control,
    } = useForm<z.infer<typeof formSchema>>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            email: "",
            password: "",
            terms: false,
            marketing: false,
        },
    });

    DOMPurify.addHook("afterSanitizeAttributes", (node) => {
        if (node.hasAttribute("href") && node.getAttribute("href") !== "https://metricool.com/legal-terms/") {
            node.remove();
        }
    });

    return (
        <FlexContainer direction={"column"} className={"min-w-[45%] max-w-[45%]"}>
            <h1 className={"font-bold font-nunito text-[1.75rem] leading-8"}>{__("Join more than 2 million professionals, agencies and brands that use Metricool as their one-stop shop for social media and online ad management.", "metricool")}</h1>
            <form onSubmit={handleSubmit((values) => onSubmit(values))} className={"flex flex-col gap-4"}>
                <FlexContainer direction={"column"} className={"!gap-2"}>
                    <FlexContainer direction={"column"} className={"!gap-2"}>
                        <Label htmlFor={"email"} className={"text-black"}>{__("Email", "metricool")}</Label>
                        <Controller
                            control={control}
                            render={({ field }) =>
                                <Input {...field} id={"email"} placeholder={__("Enter your email", "metricool")} className={clsx("bg-white font-semibold text-black max-w-80)", formValidationErrors.email?.message && "data-[slot=input]:border-rsp-error-dark")}/>}
                            name={"email"}
                        />
                        <span className={clsx("text-rsp-error-dark text-sm h-0 opacity-0 transition-all ease-in-out duration-600", formValidationErrors.email?.message && "h-3 opacity-100")}>
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
                                <Input type={"password"} {...field} id={"password"} placeholder={__("Write your password here", "metricool")} className={clsx("bg-white font-semibold text-black max-w-80", formValidationErrors.password?.message && "data-[slot=input]:border-rsp-error-dark")}/>}
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
                    variant={"primary-gradient"}
                    icon={"arrow-right"}
                    iconPosition={"right"}
                    type={"submit"}
                    disabled={!(dirtyFields.email && dirtyFields.password)}
                >
                    {__("Create your free account", "metricool")}
                </Button>
                <FlexContainer direction={"column"} className={"!gap-2"}>
                    <FlexContainer direction={"column"} className={"!gap-2"}>
                        <FlexContainer direction={"row"} className={"w-full"}>
                            <Controller
                                control={control}
                                render={({ field }) =>
                                    <Switch checked={field.value} onCheckedChange={field.onChange}/>}
                                name={"terms"}
                            />
                            <Label htmlFor={"terms"}>
                                <span
                                    dangerouslySetInnerHTML={{
                                        __html:
                                            DOMPurify.sanitize(
                                                sprintf(
                                                    /*! translators: the two variables are opening and closing anchor tags */
                                                    __("I have read and accept the %sLegal Terms%s by Metricool.", "metricool"),
                                                    `<a href="https://metricool.com/legal-terms/" target="_blank">`,
                                                    `</a>`),
                                                { ADD_ATTR: ["target"] }
                                            )
                                    }}
                                >
                                </span>
                            </Label>
                        </FlexContainer>
                        <span className={clsx("text-rsp-error-dark text-sm h-0 opacity-0 transition-all ease-in-out duration-600", formValidationErrors.terms?.message && "h-3 opacity-100")}>
                            <Icon icon={"error"} iconClass={"text-rsp-error-dark"} inverse={true}></Icon>
                            {" "}
                            {formValidationErrors.terms?.message}
                        </span>
                    </FlexContainer>
                    <FlexContainer direction={"column"} className={"!gap-2"}>
                        <FlexContainer direction={"row"} className={"w-full"}>
                            <Controller
                                control={control}
                                render={({ field }) =>
                                    <Switch checked={field.value} onCheckedChange={field.onChange}/>}
                                name={"marketing"}
                            />
                            <Label htmlFor={"marketing"}><span dangerouslySetInnerHTML={{ __html: DOMPurify.sanitize(__("I wish to receive communications about news and/or promotions from Metricool Software.", "metricool")) }}></span></Label>
                        </FlexContainer>
                        <span className={"text-red-500 text-sm"}>{formValidationErrors.marketing?.message}</span>
                    </FlexContainer>
                </FlexContainer>
            </form>
        </FlexContainer>
    );
};

export default OnboardingForm;