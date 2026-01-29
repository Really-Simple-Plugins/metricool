import { Icon } from "@/components/shared/user-feedback/Icon.tsx"
import { useTheme } from "next-themes"
import { Toaster as Sonner, type ToasterProps } from "sonner"

const Toaster = ({ ...props }: ToasterProps) => {
  const { theme = "system" } = useTheme()

  return (
    <Sonner
      theme={theme as ToasterProps["theme"]}
      className="toaster group"
      icons={{
        success: <Icon icon={"success"} className="group toast !size-5 leading-5 text-rsp-success-dark" />,
        info: <Icon icon={"info"} className="group toast !size-5 leading-5 text-rsp-info" />,
        warning: <Icon icon={"warning"} className="group toast !size-5 leading-5 text-rsp-warning" />,
        error: <Icon icon={"error"} className="group toast !size-5 leading-5 text-rsp-error" />,
        loading: <Icon icon={"loading"} className="group toast !size-5 leading-5" />,
        close: <Icon icon={"close"} className="group toast !size-3 leading-5 opacity-45" />,
      }}
      style={
        {
          "--normal-bg": "var(--popover)",
          "--normal-text": "var(--popover-foreground)",
          "--normal-border": "var(--border)",
          "--border-radius": "var(--radius)",
        } as React.CSSProperties
      }
      {...props}
    />
  )
}

export { Toaster }
