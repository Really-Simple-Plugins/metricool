import { Toaster as PrimitiveToaster } from "@/components/shared/primitives/sonner.tsx";
import { toast } from "sonner";

/**
 *
 * @version 1.0.0
 */
const ToastContainer = ({ ...props }: React.ComponentProps<typeof PrimitiveToaster>) => {
    return (
        <PrimitiveToaster
            closeButton={true}
            toastOptions={{
                classNames: {
                    toast: "group toast !p-3 !w-fit !flex !min-w-70 !mb-0 !gap-2",
                    closeButton: "group toast !p-o !h-fit !w-fit !border-none !relative !transform-none !rounded-none [&>svg]:!m-0 order-3",
                    icon: "!h-fit !w-5 !m-0 order-1",
                    content: "order-2 grow",
                    title: "min-h-5",
                }
            }}
            {...props}
        />
    );
};

export { ToastContainer, toast as showToast };