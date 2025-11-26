import { __, sprintf } from "@wordpress/i18n";
import { Button, FlexContainer, Input, Label, Switch } from "../components";
import { useGlobalContext } from "../context/GlobalContext.tsx";
import { Controller, useForm } from "react-hook-form";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import { clsx } from "clsx";
import DOMPurify from "dompurify";

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

export const OnboardingLayout = () => {
    const { metricool } = useGlobalContext();

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

    const onSubmit = (values: { email: string; password: string; terms: boolean; marketing: boolean; }) => {
        console.log(values);
    };

    DOMPurify.addHook("afterSanitizeAttributes", (node) => {
        if (node.hasAttribute("href") && node.getAttribute("href") !== "https://metricool.com/legal-terms/") {
            node.remove();
        }
    });

    return (
        <FlexContainer direction={"column"} className={"w-full h-full px-20 py-12 !gap-0"}>
            <FlexContainer direction={"row"} className={"justify-between items-center pb-4"}>
                <FlexContainer direction={"row"} className={"text-base font-bold font-nunito items-center"}>
                    <img src={`${metricool.assets_url}img/mc-logo.svg`} alt={"Metricool logo"}/>
                    <img src={`${metricool.assets_url}img/logo.svg`} className={"h-[30px]"} alt={"Metricool logo"}/>
                    {__("The digital Swiss Army Knife for social media marketers", "metricool")}
                </FlexContainer>
                <FlexContainer direction={"row"} className={"text-md font-semibold font-nunito items-center"}>
                    {__("Already a Metricooler?", "metricool")}
                    <Button variant={"primary-gradient-ghost"} className={"p-0 after:!bg-white after:!border-none !border-none"} onClick={() => console.log("hi")}>
                        {__("Sign in here", "metricool")}
                    </Button>
                </FlexContainer>
            </FlexContainer>
            <div className={"w-full h-[2px] bg-[image:var(--gradient-brand-secondary)]"}></div>
            <FlexContainer direction={"row"} className={"w-full !gap-0"}>
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
                            disabled={!(dirtyFields.email && dirtyFields.password && dirtyFields.terms)}
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
                                                        { ADD_ATTR: ["target"] })
                                            }}>
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
                <img src={`${metricool.assets_url}img/mc-onboarding-image.webp`} className={"max-w-[55%]"} alt={"Metricool logo"}/>
            </FlexContainer>
        </FlexContainer>
    );
};