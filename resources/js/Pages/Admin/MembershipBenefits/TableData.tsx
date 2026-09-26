import React from 'react';
import { Badge, Flex, Text } from '@mantine/core';
import { router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { ActionButtonExt } from '../Conferences/ExtendComponent';

export interface MembershipBenefit {
  id: number;
  code: string;
  name: string;
  benefit_type: string;
  description?: string | null;
  created_at?: string;
  updated_at?: string;
}

type DataProps = {
  handleDelete: (benefit: MembershipBenefit) => void;
};

export const TableData = ({ handleDelete }: DataProps) => [
  {
    field: 'serial_number',
    label: 'No.',
    width: '10px',
    renderCell: (_: MembershipBenefit, { rowIndex }: { rowIndex: number }) => rowIndex + 1,
  },
  {
    field: 'code',
    label: 'Code',
    sortable: true,
    renderCell: (row: MembershipBenefit) => (
      <Badge variant="outline" color="blue">
        {row.code}
      </Badge>
    ),
  },
  {
    field: 'name',
    label: 'Name',
    sortable: true,
    renderCell: (row: MembershipBenefit) => (
      <Text fw={500} size="sm">
        {row.name}
      </Text>
    ),
  },
  {
    field: 'benefit_type',
    label: 'Type',
    renderCell: (row: MembershipBenefit) => {
      const getBadgeColor = (type: string) => {
        switch (type) {
          case 'discount':
            return 'green';
          case 'souvenir':
          case 'item':
            return 'grape';
          case 'opportunity':
            return 'indigo';
          case 'voucher':
            return 'orange';
          default:
            return 'gray';
        }
      };

      return (
        <Badge color={getBadgeColor(row.benefit_type)} variant="light">
          {row.benefit_type}
        </Badge>
      );
    },
  },
  {
    field: 'description',
    label: 'Description',
    sortable: false,
    renderCell: (row: MembershipBenefit) => (
      <Text size="sm" c="dimmed">
        {row.description || '-'}
      </Text>
    ),
  },
  {
    field: 'actions',
    label: 'Actions',
    sortable: false,
    renderCell: (row: MembershipBenefit) => (
      <Flex gap="xs" justify="center" align="center">
        <ActionButtonExt
          color="blue"
          handleClick={() => router.get(route('membership-benefits.edit', row.id))}
          icon="pi pi-fw pi-pencil"
          title="Edit"
        />
        <ActionButtonExt
          color="red"
          handleClick={() => handleDelete(row)}
          icon="pi pi-fw pi-trash"
          title="Delete"
        />
      </Flex>
    ),
  },
];

export default TableData;
