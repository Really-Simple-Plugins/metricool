import React from "react";
import { Link } from "@tanstack/react-router";
import { __ } from '@wordpress/i18n';


export const DashboardLayout = ({ children }: { children: React.ReactNode }) => {
    return (
        <div>
            <div className="p-2 flex gap-2">
                <Link to="/" className="[&.active]:font-bold">
                    {__('Home', 'metricool')}
                </Link>
            </div>
            <hr/>
            {children}
        </div>
    );
};