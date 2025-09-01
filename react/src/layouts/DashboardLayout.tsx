import React from 'react';
import { __ } from '@wordpress/i18n';

export const DashboardLayout = ({ children }: { children?: React.ReactNode }) => {
    return (
        <div>
            <div className='p-2 flex gap-2'>
                {__('Dashboard', 'metricool')}
            </div>
            <hr/>
            {children}
        </div>
    );
};