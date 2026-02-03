import { Link } from "@tanstack/react-router";
import { Icon } from "@/components/shared";

const HeaderTab = ({ link, children, external = false }: {
    link: string,
    children: React.ReactNode,
    external?: boolean
}) => {
    return (
        <Link to={link} {...(external && { target: "_blank" })} className="text-md text-black items-center flex gap-1 py-[23px] focus:outline-hidden relative ease-in-out duration-200 border-b-3 border-b-transparent [&.active]:border-b-primary hover:border-b-3 hover:border-b-primary-light hover:text-black">
            {children}
            {external && (<Icon icon={"inline-external-link"}/>)}
        </Link>
    );
};

export default HeaderTab;