import { Link } from "@tanstack/react-router";
import { Icon } from "../components";

const HeaderTab = ({ link, children, external = false }: {
    link: string,
    children: React.ReactNode,
    external?: boolean
}) => {
    return (
        <Link to={link} {...(external && { target: "_blank" })} className="text-md text-black items-center flex gap-1 py-[23px] focus:outline-hidden relative ease-in-out duration-100 [&.active]:border-b-2 [&.active]:border-b-primary hover:border-b-2 hover:border-b-primary-light hover:text-black focus:shadow-none">
            {children}
            {external && (<Icon icon={"external-link"}/>)}
        </Link>
    );
};

export default HeaderTab;