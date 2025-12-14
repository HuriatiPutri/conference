import React from 'react';
import dayjs from 'dayjs';
import { Stack, Text } from '@mantine/core';
import { router } from '@inertiajs/react';
import { Conference } from '../../../types';
import { formatCurrency } from '../../../utils';
import { ActionButtonExt, CopyButtonExt } from './ExtendComponent';

type DataProps = {
  handleDelete: (id: number) => void;
}
const baseUrl = window.location.origin;
export const TableData = ({ handleDelete }: DataProps) => [
  {
    label: 'No.',
    name: 'id',
    sortable: true,
    rowspan: 2,
    renderCell: (_: Conference, { rowIndex }: { rowIndex: number }) => rowIndex + 1
  },
  {
    label: 'Name',
    name: 'name',
    sortable: true,
    className: 'text-wrap w-40',
    width: '10%',
    rowspan: 2,
    renderCell: (row: Conference) => (
      <Text size='sm' style={{ textWrap: 'wrap' }}>
        {row.name}
        {
          row.deleted_at && 'detelted'
          // <Trash2 size={16} className="ml-2 text-gray-400" />
        }
      </Text>
    ),
  },
  { label: 'Initial', name: 'initial', rowspan: 2 },
  {
    label: 'Date',
    name: 'date',
    sortable: true,
    rowspan: 2,
    renderCell: (row: Conference) => dayjs(row.date).format('DD MMM YYYY'),
  },
  {
    label: 'Registation Date',
    name: 'registration_date',
    sortable: true,
    rowspan: 2,
    renderCell: (row: Conference) =>
      `${dayjs(row.registration_start_date).format('DD MMM YYYY')} - ${dayjs(row.registration_end_date).format('DD MMM YYYY')}`,
  },
  {
    label: 'Location',
    name: 'city',
    rowspan: 2,
    renderCell: (row: Conference) => `${row.city},${row.country}` || 'N/A',
  },
  { label: 'Year', name: 'year', rowspan: 2 },
  {
    label: 'Online Fee',
    name: 'online_fee',
    sortable: true,
    colspan: 2,
    renderCell: (row: Conference) => (
      <Text fw={500} ta={'right'}>
        {formatCurrency(row.online_fee)}
      </Text>
    ),
  },
  {
    label: 'Online Fee',
    name: 'online_fee_usd',
    sortable: true,
    colspan: 2,
    renderCell: (row: Conference) => (
      <Text fw={500} ta={'right'}>
        {formatCurrency(row.online_fee_usd, 'usd')}
      </Text>
    ),
  },
  {
    label: 'Onsite Fee',
    name: 'onsite_fee',
    sortable: true,
    colspan: 2,
    renderCell: (row: Conference) => (
      <Text fw={500} ta={'right'}>
        {formatCurrency(row.onsite_fee_usd, 'usd')}
      </Text>
    ),
  },
  {
    label: 'Onsite Fee',
    name: 'onsite_fee',
    sortable: true,
    colspan: 2,
    renderCell: (row: Conference) => (
      <Text fw={500} ta={'right'}>
        {formatCurrency(row.onsite_fee_usd, 'usd')}
      </Text>
    ),
  },
  {
    label: 'Participant Fee',
    name: 'participant_fee',
    sortable: true,
    colspan: 2,
    renderCell: (row: Conference) => (
      <Text fw={500} ta={'right'}>
        {formatCurrency(row.participant_fee)}
      </Text>
    ),
  },
  {
    label: 'Participant Fee',
    name: 'participant_fee',
    sortable: true,
    colspan: 2,
    renderCell: (row: Conference) => (
      <Text fw={500} ta={'right'}>
        {formatCurrency(row.participant_fee_usd, 'usd')}
      </Text>
    ),
  },
  {
    label: 'Registration Links',
    name: 'registration_link',
    rowspan: 2,
    renderCell: (row: Conference) => (
      <Stack gap={'xs'}>
        <CopyButtonExt value={`${baseUrl}/registration/${row.public_id}`} label={'Registration'} />
        <CopyButtonExt value={`${baseUrl}/keynote/${row.public_id}`} label={'Key Note'} />
        <CopyButtonExt value={`${baseUrl}/parallel-session/${row.public_id}`} label={'Parallel Session'} />
      </Stack>
    ),
  },
  {
    label: 'Action',
    name: 'action',
    rowspan: 2,
    frozen: true,
    renderCell: (row: Conference) => (
      <Stack gap={'xs'} justify="center" align="center">
        <ActionButtonExt
          color="blue"
          handleClick={() => (router.visit(`/conferences/${row.id}/edit`))}
          icon="pi pi-fw pi-pencil"
          title="Edit"
        />
        <ActionButtonExt
          color="green"
          handleClick={() => (router.visit(`/conferences/${row.id}/show`))}
          icon="pi pi-fw pi-eye"
          title="View Details"
        />
        <ActionButtonExt
          color="yellow"
          handleClick={() => (router.visit(`/conferences/${row.id}/setting`))}
          icon="pi pi-fw pi-cog"
          title="Settings"
        />
        <ActionButtonExt
          color="red"
          handleClick={() => handleDelete(row.id)}
          icon="pi pi-fw pi-trash"
          title="Delete"
        />
      </Stack>
    ),
  },
];