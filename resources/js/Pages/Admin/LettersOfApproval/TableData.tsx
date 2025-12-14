import React from "react";
import { Badge, Group, Text, Button, Stack } from "@mantine/core";
import { PRESENTATION_TYPE } from "../../../Constants";
import { route } from "ziggy-js";
import { Audiences, AudienceWithLoA } from "../../../types";
import { ActionButtonExt } from "../Conferences/ExtendComponent";

const getLoAStatusBadge = (status: string) => {
  const statusMap = {
    pending: { color: 'yellow', label: 'Pending' },
    approved: { color: 'green', label: 'Approved' },
    rejected: { color: 'red', label: 'Rejected' }
  };

  const config = statusMap[status as keyof typeof statusMap] || statusMap.pending;
  return <Badge color={config.color} variant="filled" style={{ textWrap: 'nowrap' }}>{config.label}</Badge>;
};

export const TableData = () => [
  {
    field: 'serial_number',
    header: 'No.',
    style: { minWidth: '5rem' },
    body: (_: Audiences, { rowIndex }: { rowIndex: number }) =>
      rowIndex + 1
  },
  {
    field: 'conference.name',
    header: 'Conference',
    style: { minWidth: '200px' }
  },
  {
    field: 'participant_name',
    header: 'Participant',
    body: (row: AudienceWithLoA) => `${row.first_name} ${row.last_name}`
  },
  {
    field: 'institution',
    header: 'Institution',
    style: { minWidth: '200px' }
  },
  {
    field: 'paper_title',
    header: 'Paper Title',
    style: { minWidth: '300px' },
    body: (row: AudienceWithLoA) => (
      <Text lineClamp={2} size="sm">
        {row.paper_title}
      </Text>
    )
  },
  {
    field: 'loa_volume',
    header: 'LoA Volume',
    style: { minWidth: '200px' },
    body: (row: AudienceWithLoA) => (
      <Stack>
        {row.loa_volume ? (
          <>
            <Badge color="blue" variant="outline">
              {row.loa_volume.volume}
            </Badge>
            {row.loa_authors && row.payment_status === 'paid' && (
              <Button
                color="green"
                size="xs"
                variant="light"
                leftSection={<i className="pi pi-download" />}
                onClick={() => window.open(route('letters-of-approval.download', row.id), '_blank')}
              >
                Download LoA
              </Button>
            )}
          </>
        ) : (
          <Text size="sm" c="dimmed" fs="italic">Not assigned</Text>
        )}
      </Stack>
    ),
  },
  {
    field: 'presentation_type',
    header: 'Type',
    body: (row: AudienceWithLoA) => PRESENTATION_TYPE[row.presentation_type as keyof typeof PRESENTATION_TYPE],
  },
  {
    field: 'loa_status',
    header: 'LoA Status',
    style: { minWidth: '150px' },
    body: (row: AudienceWithLoA) => getLoAStatusBadge(row.loa_status || 'pending')
  },
  {
    field: 'actions',
    header: 'Action',
    body: (row: AudienceWithLoA) => (
      <Group gap="xs">
        <ActionButtonExt
            color="violet"
            handleClick={() => window.location.href = route('letters-of-approval.assign-volume', row.id)}
            icon="pi pi-fw pi-book"
            title="Assign Volume"
          />
      </Group>
    )
  }
];