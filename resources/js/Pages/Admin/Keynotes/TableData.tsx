import React from "react";
import { KeyNote } from "../../../types";
import { Text } from "@mantine/core";

export const TableData = () => [
  {
    field: 'serial_number',
    label: 'No.',
    width: '10px',
    renderCell: (_: KeyNote, { rowIndex }: { rowIndex: number }) =>
      rowIndex + 1
  },
  {
    label: 'Conference',
    name: 'audience.conference.name',
    sortable: true,
    width: '10%',
    className: 'text-wrap w-40',
    renderCell: (row: KeyNote) => (
      <Text size="sm" fw={500} style={{ textWrap: 'wrap' }}>
        {row.audience.conference.name} ({row.audience.conference.initial})
      </Text>
    ),
  },
  {
    label: 'Presenter Name',
    name: 'name_of_participant',
    renderCell: (row: KeyNote) => (
      <Text size="sm">
        {row.name_of_participant}
      </Text>
    ),
  },
  {
    label: 'Email',
    name: 'audience.email',
    renderCell: (row: KeyNote) => (
      <Text size="sm" c="blue" style={{ cursor: 'pointer' }}>
        {row.audience.email}
      </Text>
    ),
  },
  {
    label: 'Feedback',
    name: 'feedback',
    renderCell: (row: KeyNote) => (
      <Text size="sm" lineClamp={2} style={{ maxWidth: 300 }}>
        {row.feedback}
      </Text>
    ),
  },
  {
    label: 'Submitted Date',
    name: 'created_at',
    sortable: true,
    renderCell: (row: KeyNote) => (
      <Text size="sm">
        {new Date(row.created_at).toLocaleDateString('id-ID')}
      </Text>
    ),
  },
];