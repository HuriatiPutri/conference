import React from 'react';
import { LoaVolume } from '@/Types/LoaVolume';
import { Flex, Stack, Text } from '@mantine/core';
import { router } from '@inertiajs/react';
import { ActionButtonExt } from '../Conferences/ExtendComponent';

type DataProps = {
  handleDelete: (loaVolume: LoaVolume) => void;
}
export const TableData = ({ handleDelete }: DataProps) => [
    {
      field: 'serial_number',
      label: 'No.',
      width: '10px',
      renderCell: (_: LoaVolume, { rowIndex }: { rowIndex: number }) =>
        rowIndex + 1
    },
    {
      label: 'Volume',
      field: 'volume',
      sortable: true,
      width: '10%',
      className: 'text-wrap w-40',
    },
    {
      label: 'Articles Assigned',
      field: 'audiences_count',
      renderCell: (row: LoaVolume) => {
        const totalCount = (row.audiences_count || 0) + (row.joiv_registrations_count || 0);
        return (
          <Stack gap={4}>
            <Text size="sm" fw={600}>
              {totalCount}
            </Text>
            <Text size="xs" c="dimmed">
              ({row.audiences_count || 0} conf + {row.joiv_registrations_count || 0} JOIV)
            </Text>
          </Stack>
        );
      },
    },
    {
      label: 'Created By',
      field: 'creator.name',
      renderCell: (row: LoaVolume) => (
        <Text size="sm">
          {row.creator?.name || 'System'}
        </Text>
      ),
    },
    {
      label: 'Created At',
      field: 'created_at',
      renderCell: (row: LoaVolume) => (
        <Text size="sm" c="blue" style={{ cursor: 'pointer' }}>
          {new Date(row.created_at).toLocaleDateString('id-ID')}
        </Text>
      ),
    },
    {
      label: 'Actions',
      renderCell: (row: LoaVolume) => {
        const totalCount = (row.audiences_count || 0) + (row.joiv_registrations_count || 0);
        return (
          <Flex gap={'xs'} justify="center" align="center">
            <ActionButtonExt
              color="blue"
              handleClick={() => router.get(route('loa.loa-volumes.edit', row.id))}
              icon="pi pi-fw pi-pencil"
              title="Edit"
            />
            <ActionButtonExt
              color="green"
              handleClick={() => router.get(route('loa.loa-volumes.show', row.id))}
              icon="pi pi-fw pi-eye"
              title="View Details"
            />
            {totalCount > 0 && (
              <ActionButtonExt
                color="teal"
                handleClick={() => window.location.href = route('loa.loa-volumes.export', row.id)}
                icon="pi pi-fw pi-file-excel"
                title="Export to Excel"
              />
            )}
            {totalCount === 0 && (
              <ActionButtonExt
                color="red"
                handleClick={() => handleDelete(row)}
                icon="pi pi-fw pi-trash"
                title="Delete"
              />
            )}
          </Flex>
        );
      },
      style: { width: '12rem' }
    }
  ];