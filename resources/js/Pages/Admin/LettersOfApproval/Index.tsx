import React, { useState } from 'react';
import { usePage } from '@inertiajs/react';
import { DataTable } from 'primereact/datatable';
import { Column } from 'primereact/column';
import {
  Container,
  Title,
  Button,
  Group,
  Text,
  Badge,
  Stack,
  Select,
  Grid,
  Card,
  Divider,
  ActionIcon,
  TextInput
} from '@mantine/core';
import { IconDownload, IconFileText } from '@tabler/icons-react';
import MainLayout from '../../../Layout/MainLayout';
import { Audiences, PaginatedData, PageProps } from '../../../types';
import { route } from 'ziggy-js';
import { PRESENTATION_TYPE } from '../../../Constants';

type AudienceWithLoA = Audiences & {
  loa_status?: string;
  loa_notes?: string;
  loa_approved_at?: string;
};

interface LettersOfApprovalPageProps extends PageProps {
  audiences: PaginatedData<AudienceWithLoA>;
  conferences: Array<{ id: number; name: string; initial: string }>;
  filters: {
    conference_id?: string;
    search?: string;
  };
  summary: {
    total_participants: number;
    total_papers: number;
    total_conferences: number;
  };
}

function LettersOfApprovalIndex() {
  const { audiences, conferences, filters, summary } = usePage<LettersOfApprovalPageProps>().props;
  const { data } = audiences;

  // Filter states
  const [conferenceFilter, setConferenceFilter] = useState(filters?.conference_id || '');
  const [searchTerm, setSearchTerm] = useState(filters?.search || '');

  // Global filter for DataTable
  const [globalFilterValue] = useState('');

  const handleFilterChange = () => {
    const params = new URLSearchParams();
    if (conferenceFilter) params.append('conference_id', conferenceFilter);
    if (searchTerm) params.append('search', searchTerm);

    window.location.href = `/letters-of-approval?${params.toString()}`;
  };

  const clearFilters = () => {
    window.location.href = '/letters-of-approval';
  };

  const getLoAStatusBadge = (status: string) => {
    const statusMap = {
      pending: { color: 'yellow', label: 'Pending' },
      approved: { color: 'green', label: 'Approved' },
      rejected: { color: 'red', label: 'Rejected' }
    };

    const config = statusMap[status as keyof typeof statusMap] || statusMap.pending;
    return <Badge color={config.color} variant="filled" style={{ textWrap: 'nowrap' }}>{config.label}</Badge>;
  };

  const columns = [
    {
      field: 'serial_number',
      header: 'No.',
      style: { minWidth: '5rem' },
      body: (_: Audiences, { rowIndex }: { rowIndex: number }) => rowIndex + 1
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
          <ActionIcon
            variant="light"
            color="green"
            component="a"
            href={route('letters-of-approval.download-form', row.id)}
            title="Download LoA"
          >
            <IconDownload size={16} />
          </ActionIcon>
        </Group>
      )
    }
  ];

  return (
    <Container fluid>
      <Stack gap="lg">
        {/* Header */}
        <Group justify="space-between">
          <div>
            <Title order={2}>Letters of Approval</Title>
            <Text c="dimmed">Manage and generate Letters of Approval for conference participants</Text>
          </div>
        </Group>

        {/* Summary Cards */}
        <Grid>
          <Grid.Col span={{ base: 12, md: 4 }}>
            <Card padding="lg" radius="md" withBorder>
              <Group justify="space-between">
                <div>
                  <Text c="dimmed" size="sm" fw={500}>Total Participants</Text>
                  <Text fw={700} size="xl">{summary.total_participants}</Text>
                </div>
                <IconFileText size={24} color="blue" />
              </Group>
            </Card>
          </Grid.Col>
          <Grid.Col span={{ base: 12, md: 4 }}>
            <Card padding="lg" radius="md" withBorder>
              <Group justify="space-between">
                <div>
                  <Text c="dimmed" size="sm" fw={500}>Papers Submitted</Text>
                  <Text fw={700} size="xl">{summary.total_papers}</Text>
                </div>
                <IconFileText size={24} color="green" />
              </Group>
            </Card>
          </Grid.Col>
          <Grid.Col span={{ base: 12, md: 4 }}>
            <Card padding="lg" radius="md" withBorder>
              <Group justify="space-between">
                <div>
                  <Text c="dimmed" size="sm" fw={500}>Active Conferences</Text>
                  <Text fw={700} size="xl">{summary.total_conferences}</Text>
                </div>
                <IconFileText size={24} color="orange" />
              </Group>
            </Card>
          </Grid.Col>
        </Grid>

        {/* Filters */}
        <Card padding="lg" radius="md" withBorder>
          <Title order={4} mb="md">Filter Participants</Title>
          <Grid>
            <Grid.Col span={{ base: 12, md: 4 }}>
              <Select
                // label="Conference"
                placeholder="-- All Conferences --"
                data={[
                  { value: '', label: '-- All Conferences --' },
                  ...conferences.map(conf => ({
                    value: conf.id.toString(),
                    label: `${conf.name} (${conf.initial})`
                  }))
                ]}
                value={conferenceFilter}
                onChange={(value) => setConferenceFilter(value || '')}
              />
            </Grid.Col>
            <Grid.Col span={{ base: 12, md: 4 }}>
              <TextInput
                placeholder="Search by name, email, institution..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                style={{ width: '100%' }}
              />
            </Grid.Col>
            <Grid.Col span={{ base: 12, md: 4 }}>
              <Group>
                <Button onClick={handleFilterChange} variant="filled">
                  Apply Filter
                </Button>
                <Button onClick={clearFilters} variant="outline">
                  Clear
                </Button>
              </Group>
            </Grid.Col>
          </Grid>
        </Card>

        <Divider />

        {/* Data Table */}
        <Card padding="lg" radius="md" withBorder>
          <DataTable
            value={data}
            paginator
            rows={10}
            // header={renderHeader()}
            globalFilter={globalFilterValue}
            // selection={selectedAudiences}
            // onSelectionChange={(e: { value: AudienceWithLoA[] }) => setSelectedAudiences(e.value)}
            dataKey="id"
            stripedRows
            showGridlines
            tableStyle={{ minWidth: '100rem' }}
          >
            {columns.map((col, index) => (
              <Column
                key={index}
                field={col.field}
                header={col.header}
                body={col.body}
                style={col.style}
              />
            ))}
          </DataTable>
        </Card>
      </Stack>
    </Container>
  );
}

LettersOfApprovalIndex.layout = (page: React.ReactNode) => (
  <MainLayout title="Letters of Approval">{page}</MainLayout>
);

export default LettersOfApprovalIndex;