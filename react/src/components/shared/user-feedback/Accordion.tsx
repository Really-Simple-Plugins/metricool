import {
    Accordion as PrimitiveAccordion,
    AccordionContent as PrimitiveAccordionContent,
    AccordionItem as PrimitiveAccordionItem,
    AccordionTrigger as PrimitiveAccordionTrigger,
} from "@/components/shared/primitives/accordion.tsx";
import { cn } from "@/functions/utils.ts";

type AccordionProps = {
    title: string,
};

/**
 * Custom extension of shadcn's single {@link PrimitiveAccordion} component.
 *
 * @uses {@link PrimitiveAccordion} from primitives
 * @see {@link Alert} - Alert implements this component
 *
 * @version 1.0.0
 */
const SingleAccordion = ({ children, className, title }: React.ComponentProps<"div"> & AccordionProps) => {
    return (
        <PrimitiveAccordion type={"single"} collapsible defaultValue={"single-item"} className={"w-full"}>
            <PrimitiveAccordionItem value={"single-item"} className={cn("[&>h3]:my-0 flex flex-col gap-2", className)}>
                <PrimitiveAccordionTrigger className={"font-bold text-md py-0 items-center cursor-pointer hover:no-underline"}>
                    {title}
                </PrimitiveAccordionTrigger>
                <PrimitiveAccordionContent className={"pb-2 text-md"}>
                    {children}
                </PrimitiveAccordionContent>
            </PrimitiveAccordionItem>
        </PrimitiveAccordion>
    );
};

export { SingleAccordion };