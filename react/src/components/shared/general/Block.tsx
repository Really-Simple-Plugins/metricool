import {
    Card as PrimitiveCard,
    CardDescription as PrimitiveCardDescription,
    CardHeader as PrimitiveCardHeader,
    CardTitle as PrimitiveCardTitle
} from "@/components/shared/primitives/card";
import { type VariantProps } from "class-variance-authority";
import { cn } from "@/support/functions/utils";
import { blockVariants } from "@/support/helpers/Variants";

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
        <PrimitiveCardHeader
            className={cn(
                className,
                "p-0 pb-0.5 gap-1! w-full",
                {
                    // purposefully using border-b-1, not border-b, as the latter applies additional unwanted styling from the primitive
                    "border-b-1 border-b-neutral-300": separator
                }
            )}
        >
            <div className={cn("flex flex-row justify-between items-center w-full")}>
                <BlockHeaderTitle>{title}</BlockHeaderTitle>
                {action && action}
            </div>
            <BlockDescription>{description}</BlockDescription>
        </PrimitiveCardHeader>
    );
};

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
}: React.ComponentProps<"div"> & VariantProps<typeof blockVariants>) => {
    return (
        <PrimitiveCard
            className={cn(blockVariants({ variant, className }))}
            {...props}
        >
            {children}
        </PrimitiveCard>
    );
};

export { Block, BlockHeader };