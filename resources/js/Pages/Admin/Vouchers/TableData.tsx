import { ActionIcon, Badge } from '@mantine/core';
import { IconPencil, IconTrash } from '@tabler/icons-react';
import React from 'react';
import { router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import dayjs from 'dayjs';

const mapTypeLabel: Record<string, string> = {
  conference_registration: 'Conference',
  joiv_article: 'JOIV',
  membership_registration: 'Membership',
};

export const TableData = ({ handleDelete }: { handleDelete: any }) => {
  const cols: any[] = [
    {
      field: 'code',
      label: 'Code',
      renderCell: (row: any) => <b>{row.code}</b>,
      sortable: true,
    },
    {
      field: 'period',
      label: 'Period',
      renderCell: (row: any) => <span>{dayjs(row.start_date).format('DD MMM YYYY')} - {dayjs(row.end_date).format('DD MMM YYYY')}</span>,
      sortable: false,
    },
    {
      field: 'quota',
      label: 'Quota',
      renderCell: (row: any) => <span>{row.used_count} / {row.quota}</span>,
      sortable: true,
    },
    {
      field: 'applies_to',
      label: 'Applies To',
      renderCell: (row: any) => (
        <div style={{ display: 'flex', gap: 6, flexWrap: 'wrap' }}>
          {(row.applies_to || []).map((type: string) => (
            <Badge key={type} variant="light">{mapTypeLabel[type] || type}</Badge>
          ))}
        </div>
      ),
      sortable: false,
    },
    {
      field: 'status',
      label: 'Status',
      renderCell: (row: any) => (
        <Badge color={row.status === 'active' ? 'green' : 'gray'}>{row.status}</Badge>
      ),
      sortable: true,
    },
    {
      field: 'actions',
      label: 'Actions',
      renderCell: (row: any) => (
        <div style={{ display: 'flex', gap: 8 }}>
          <ActionIcon onClick={() => router.visit(route('vouchers.edit', row.id))}>
            <IconPencil size={16} />
          </ActionIcon>
          <ActionIcon color="red" onClick={() => handleDelete(row)}>
            <IconTrash size={16} />
          </ActionIcon>
        </div>
      ),
      sortable: false,
    },
  ];

  return cols;
};

export default TableData;
