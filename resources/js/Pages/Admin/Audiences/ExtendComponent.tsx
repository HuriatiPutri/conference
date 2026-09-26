import { Badge } from '@mantine/core';
import React from 'react';

interface BadgeStatusProps {
    status: string;
    mt?: string;
}

export function BadgeStatus({ status, mt }: BadgeStatusProps) {
    const color = {
        paid: {
            color: 'green',
            label: 'Paid'
        },
        pending_payment: {
            color: 'yellow',
            label: 'Pending Payment'
        },
        cancelled: {
            color: 'red',
            label: 'Cancelled'
        },
        refuned: {
            color: 'red',
            label: 'Refuned'
        },
        expired: {
            color: 'red',
            label: 'Expired'
        }
    }
    return <Badge color={color[status].color} mt={mt} variant="filled" fullWidth>{color[status].label}</Badge>;
}

export default { BadgeStatus };