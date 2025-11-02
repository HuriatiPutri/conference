import { Badge } from '@mantine/core';
import React, { useState } from 'react';

export function BadgeStatus({ status }: { status: string }) {
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
  return <Badge color={color[status].color} variant="filled" fullWidth>{color[status].label}</Badge>;
}

export default { BadgeStatus };