import React from "react";
import { ActionIcon, Badge, Menu, Stack, Text } from "@mantine/core";
import { IconCheck, IconEye, IconDotsVertical } from '@tabler/icons-react';
import { Membership } from "../../../types";
import { formatCurrency } from "../../../utils";
import dayjs from 'dayjs';

type DataProps = {
  handleOpenVerification: (membership: Membership) => void;
  handleAcceptPayment: (membership: Membership) => void;
};

export const TableData = ({ handleOpenVerification, handleAcceptPayment }: DataProps) => [
  {
    field: 'serial_number',
    label: 'No.',
    style: { minWidth: '5rem' },
    sortable: false,
    renderCell: (_: Membership, { rowIndex }: { rowIndex: number }) => rowIndex + 1,
  },
  {
    label: 'Name',
    name: 'name',
    sortable: true,
    renderCell: (row: Membership) => (
      <Stack gap={0}>
        <Text fw={500}>{row.first_name} {row.last_name}</Text>
        <Text size="xs" c="dimmed">{row.email}</Text>
      </Stack>
    ),
  },
  {
    label: 'Institution',
    name: 'institution',
    sortable: true,
    renderCell: (row: Membership) => (
      <Stack w={250}>
        <Text size='sm' style={{ textWrap: 'wrap' }}>{row.institution}</Text>
      </Stack>
    ),
  },
  {
    label: 'Package',
    name: 'package.name',
    sortable: true,
    renderCell: (row: Membership) => (
      <Stack gap={0}>
        <Text size="sm">{row.package?.name}</Text>
        {row.invoices && row.invoices.length > 0 && (
          <Text size="xs" c="dimmed">
            {formatCurrency(row.invoices[row.invoices.length - 1].amount, (row.invoices[row.invoices.length - 1].currency || 'idr').toLowerCase() as 'idr' | 'usd')} via {row.invoices[row.invoices.length - 1].payment_method.replace('_', ' ')}
          </Text>
        )}
      </Stack>
    ),
  },
  {
    label: 'Payment Status',
    name: 'payment_status',
    sortable: false,
    renderCell: (row: Membership) => {
      const latestInvoice = row.invoices?.[row.invoices.length - 1];
      let paymentStatusColor = 'gray';
      if (latestInvoice?.status === 'completed') paymentStatusColor = 'green';
      if (latestInvoice?.status === 'pending') paymentStatusColor = 'orange';
      if (latestInvoice?.status === 'failed') paymentStatusColor = 'red';

      return (
        <Badge color={paymentStatusColor} variant="light">
          {latestInvoice?.status || 'No Payment'}
        </Badge>
      );
    },
  },
  {
    label: 'Account Status',
    name: 'status',
    sortable: true,
    renderCell: (row: Membership) => {
      const getStatusColor = () => {
        if (row.status === 'active') return 'blue';
        if (row.status === 'pending') return 'orange';
        return 'gray';
      };

      return (
        <Stack gap={0}>
          <Badge color={getStatusColor()} variant="filled">
            {row.status}
          </Badge>
          {row.status === 'active' && (
            <Text size="xs" c="dimmed" mt={4}>
              Valid until: {dayjs(row.end_date).format('DD MMM YYYY')}
            </Text>
          )}
        </Stack>
      );
    },
  },
  {
    label: 'Start Date',
    name: 'start_date',
    sortable: true,
    renderCell: (row: Membership) => (
      <Text size="sm">{dayjs(row.start_date).format('DD MMM YYYY')}</Text>
    ),
  },
  {
    label: 'End Date',
    name: 'end_date',
    sortable: true,
    renderCell: (row: Membership) => (
      <Text size="sm">{dayjs(row.end_date).format('DD MMM YYYY')}</Text>
    ),
  },
  {
    label: 'Actions',
    name: 'actions',
    sortable: false,
    renderCell: (row: Membership) => {
      const latestInvoice = row.invoices?.[row.invoices.length - 1];
      const isBankTransferPending = latestInvoice?.payment_method === 'transfer_bank' && latestInvoice?.status === 'pending';

      return (
        <Menu position="bottom-end" shadow="md">
          <Menu.Target>
            <ActionIcon variant="subtle">
              <IconDotsVertical size={16} />
            </ActionIcon>
          </Menu.Target>
          <Menu.Dropdown>
            {isBankTransferPending && (
              <Menu.Item
                leftSection={<IconCheck size={14} />}
                onClick={() => handleAcceptPayment(row)}
              >
                Accept Payment
              </Menu.Item>
            )}
            <Menu.Item
              leftSection={<IconEye size={14} />}
              onClick={() => handleOpenVerification(row)}
            >
              Open Verification
            </Menu.Item>
          </Menu.Dropdown>
        </Menu>
      );
    },
  },
];
