import React from "react"
import { Badge } from "@mantine/core";

export const getStatusBadge = (status: string) => {
  const statusMap: Record<string, { color: string; label: string }> = {
    paid: { color: 'green', label: 'Paid' },
    pending_payment: { color: 'yellow', label: 'Pending' },
    cancelled: { color: 'red', label: 'Cancelled' },
    refunded: { color: 'gray', label: 'Refunded' },
  };

  const statusInfo = statusMap[status] || { color: 'gray', label: status };
  return <Badge color={statusInfo.color}>{statusInfo.label}</Badge>;
};