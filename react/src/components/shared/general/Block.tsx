import {
    Card as PrimitiveCard,
    CardDescription as PrimitiveCardDescription,
    CardHeader as PrimitiveCardHeader,
    CardTitle as PrimitiveCardTitle
} from "@/components/shared/primitives/card.tsx";
import { cva, type VariantProps } from "class-variance-authority";
import { cn } from "@/functions/utils.ts";

/**
 * Custom extension of shadcn's {@link PrimitiveCardTitle} component.
 * Used exclusively in {@link BlockHeader}
 * Not to be exported or used on its own.
 *
 * @uses {@link PrimitiveCardTitle} from primitives
 *
 * @version 1.0.0
 */
const BlockHeaderTitle = ({ className, children }: React.ComponentProps<"div">) => {
    return (
        <PrimitiveCardTitle className={cn(className, "text-base font-bold leading-none")}>
            <h1 className={"block-header-title"}>
                {children}
            </h1>
        </PrimitiveCardTitle>
    );
};

/**
 * Custom extension of shadcn's {@link PrimitiveCardDescription} component.
 * Used exclusively in {@link BlockHeader}
 * Not to be exported or used on its own.
 *
 * @uses {@link PrimitiveCardDescription} from primitives
 *
 * @version: 1.0.0
 */
const BlockDescription = ({ className, children }: React.ComponentProps<"div">) => {
    return (
        <PrimitiveCardDescription className={cn(className, "font-sm leading-3 h-3")}>{children}</PrimitiveCardDescription>
    );
};

/**
 * @property {ReactNode} [action] - Optional element to be rendered in top
 * right corner of the Block. Often tabs, a link or an image.
 * @property {boolean} [separator] - Optional, used to conditionally render a
 * dark bottom border as a separator, often used with the "transparent" variant.
 */
type BlockHeaderProps = {
    title: string,
    description?: string,
    action?: React.ReactNode,
    separator?: boolean,
}
/**
 * Custom extension of shadcn's {@link PrimitiveCardHeader} component.
 * Should always and exclusively be used as a child of {@link Block}.
 *
 * @uses {@link PrimitiveCardHeader} from primitives
 * @uses {@link BlockHeaderTitle}
 * @uses {@link BlockDescription}
 *
 * @version 1.0.0
 */
const BlockHeader = ({
    className,
    title,
    description,
    action,
    separator
}: React.ComponentProps<"div"> & BlockHeaderProps) => {
    return (
        <PrimitiveCardHeader className={cn(className, "p-0 !gap-[0.375rem] w-full", separator && "border-b-1 border-b-neutral-300")}>
            <div className={cn("flex flex-row justify-between items-center w-full")}>
                <BlockHeaderTitle>{title}</BlockHeaderTitle>
                {action && action}
            </div>
            <BlockDescription>{description}</BlockDescription>
        </PrimitiveCardHeader>
    );
};

const BlockVariants = cva(
    "w-full p-4 gap-[10px] shadow-lg",
    {
        variants: {
            variant: {
                "default": "border-none",
                "transparent": "bg-transparent shadow-none border-none"
            },
        },
        defaultVariants: {
            variant: "default",
        },
    });

/**
 * Custom extension of shadcn's {@link PrimitiveCard} component.
 * The main building block of any element within our plugins.
 * Should always have a {@link BlockHeader} child.
 *
 * In addition to the named props, it accepts all other possible props
 * for a `div` element, passed to `Card` through a `...props` rest object
 * @uses {@link PrimitiveCard} from primitives
 *
 * @version 1.0.0
 */
const Block = ({
    className,
    variant,
    children,
    ...props
}: React.ComponentProps<"div"> & VariantProps<typeof BlockVariants>) => {
    return (
        <PrimitiveCard className={cn(BlockVariants({ variant }), className)} {...props}>
            {children}
        </PrimitiveCard>
    );
};

export { Block, BlockHeader };