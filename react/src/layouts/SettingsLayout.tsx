import React from 'react';
import { __ } from '@wordpress/i18n';

export const SettingsLayout = ({ children }: { children?: React.ReactNode }) => {
    return (
        <div>
            <div className='p-2 flex gap-2'>
                {__('Settings', 'metricool')}
            </div>
            <hr/>
            {children}
        </div>
    );
};