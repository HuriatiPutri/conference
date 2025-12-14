import React from "react";
import { Text } from "@mantine/core";
import { ParallelSession } from "../../../types";

export const TableData = [
  {
    field: 'serial_number',
    label: 'No.',
    width: '10px',
    style: { minWidth: '5rem' },
    renderCell: (_: ParallelSession, { rowIndex }: { rowIndex: number }) =>
      rowIndex + 1
  },
  {
    label: 'Conference',
    name: 'audience.conference.name',
    sortable: true,
    width: '10%',
    renderCell: (row: ParallelSession) => (
      <Text size="sm" fw={500}>
        {row.audience.conference.name} ({row.audience.conference.initial})
      </Text>
    ),
  },
  {
    label: 'Presenter Name',
    name: 'name_of_presenter',
    renderCell: (row: ParallelSession) => (
      <Text size="sm">
        {row.name_of_presenter}
      </Text>
    ),
  },
  {
    label: 'Email',
    name: 'audience.email',
    renderCell: (row: ParallelSession) => (
      <Text size="sm" c="blue" style={{ cursor: 'pointer' }}>
        {row.audience.email}
      </Text>
    ),
  },
  {
    label: 'Paper Title',
    name: 'paper_title',
    renderCell: (row: ParallelSession) => (
      <Text size="sm" lineClamp={2} style={{ maxWidth: 300 }}>
        {row.paper_title}
      </Text>
    ),
  },
  {
    label: 'Room',
    name: 'room.room_name',
    renderCell: (row: ParallelSession) => (
      <Text size="sm">
        {row.room?.room_name || 'Not Assigned'}
      </Text>
    ),
  },
  {
    label: 'Submitted Date',
    name: 'created_at',
    sortable: true,
    renderCell: (row: ParallelSession) => (
      <Text size="sm">
        {new Date(row.created_at).toLocaleDateString('id-ID')}
      </Text>
    ),
  },
];