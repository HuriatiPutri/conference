import { DataTable, DataTableStateEvent } from 'primereact/datatable';
import MainLayout from '../../../Layout/MainLayout';
import { Column } from 'primereact/column';
import { usePage } from '@inertiajs/react';
import React, { useState } from 'react';
import { InputText } from 'primereact/inputtext';
import { ActionIcon, Button, Flex, Text, Select, Box, Group } from '@mantine/core';
import { IconField } from 'primereact/iconfield';
import { InputIcon } from 'primereact/inputicon';

interface ParallelSession {
  id: number;
  name_of_presenter: string;
  paper_title: string;
  created_at: string;
  audience: {
    id: number;
    email: string;
    conference: {
      id: number;
      name: string;
      initial: string;
    };
  };
  room: {
    id: number;
    room_name: string;
  } | null;
}

interface Props {
  parallelSessions: {
    data: ParallelSession[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  filters: {
    conference_id?: string;
  };
  conferences: Array<{ id: number; name: string }>;
  [key: string]: unknown;
}

function ParallelSessionIndex() {
  const { parallelSessions, filters, conferences } = usePage<Props>().props;
  const { data } = parallelSessions;

  const [globalFilterValue, setGlobalFilterValue] = useState('');
  const [conferenceFilter, setConferenceFilter] = useState(filters?.conference_id || '');

  const handleFilterChange = () => {
    const params = new URLSearchParams();
    if (conferenceFilter) params.append('conference_id', conferenceFilter);

    window.location.href = `/parallel-sessions?${params.toString()}`;
  };

  const handlePageChange = (event: DataTableStateEvent) => {
    if (event.page !== undefined) {
      const params = new URLSearchParams(window.location.search);
      params.set('page', (event.page + 1).toString());
      if (event.rows && event.rows !== parallelSessions.per_page) {
        params.set('per_page', event.rows.toString());
      }
      if (conferenceFilter) params.set('conference_id', conferenceFilter);

      window.location.href = `/parallel-sessions?${params.toString()}`;
    }
  };

  const clearFilters = () => {
    window.location.href = '/parallel-sessions';
  };

  const onGlobalFilterChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const value = e.target.value;
    setGlobalFilterValue(value);
  };

  const renderHeader = () => {
    return (
      <Flex justify={'space-between'} direction={{ base: 'column', sm: 'row' }} gap={'md'}>
        <Flex gap={'xs'}>
          <ActionIcon color={'green'} variant="outline" radius={'lg'} size={'lg'}>
            <i className="pi pi-file-excel" />
          </ActionIcon>
          <ActionIcon color={'red'} variant="outline" radius={'lg'} size={'lg'}>
            <i className="pi pi-file-pdf" />
          </ActionIcon>
        </Flex>
        <IconField iconPosition="left">
          <InputIcon className="pi pi-search" />
          <InputText
            value={globalFilterValue}
            onChange={onGlobalFilterChange}
            size={'small'}
            placeholder="Keyword Search"
          />
        </IconField>
      </Flex>
    );
  };

  const columns = [
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

  return (
    <div style={{ padding: '1rem', backgroundColor: 'white', borderRadius: '8px', boxShadow: '0 2px 8px rgba(0,0,0,0.1)' }}>
      {/* Filter Section */}
      <Box mb="lg">
        <Text size="lg" fw={600} mb="md">Filter Parallel Session Data</Text>

        {/* Pagination Info */}
        <Text size="sm" c="dimmed" mb="md">
          Showing {((parallelSessions.current_page - 1) * parallelSessions.per_page) + 1} to {Math.min(parallelSessions.current_page * parallelSessions.per_page, parallelSessions.total)} of {parallelSessions.total} entries
        </Text>

        <Group gap="md" mb="md">
          <Select
            placeholder="-- All Conferences --"
            data={[
              { value: '', label: '-- All Conferences --' },
              ...conferences.map(conf => ({ value: conf.id.toString(), label: conf.name }))
            ]}
            value={conferenceFilter}
            onChange={(value) => setConferenceFilter(value || '')}
            style={{ minWidth: 300 }}
          />

          <Button onClick={handleFilterChange} variant="filled">
            Filter
          </Button>

          <Button onClick={clearFilters} variant="outline">
            Reset
          </Button>
        </Group>
      </Box>

      <DataTable
        value={data}
        lazy
        paginator
        first={(parallelSessions.current_page - 1) * parallelSessions.per_page}
        rows={parallelSessions.per_page}
        totalRecords={parallelSessions.total}
        onPage={handlePageChange}
        rowsPerPageOptions={[15, 25, 50, 100]}
        header={renderHeader()}
        globalFilter={globalFilterValue}
        stripedRows
        showGridlines
        className="datatable-responsive"
        tableStyle={{ minWidth: '100rem', fontSize: '14px' }}
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        currentPageReportTemplate="Showing {first} to {last} of {totalRecords} entries"
      >
        {columns.map(col => (
          <Column
            key={col.name}
            field={col.name}
            body={col.renderCell}
            header={col.label}
            sortable={col.sortable}
            style={{
              alignItems: 'top',
              textAlign: 'left',
              width: col.width ? col.width : 'auto',
            }}
          />
        ))}
      </DataTable>
    </div>
  );
}

ParallelSessionIndex.layout = (page: React.ReactNode) => <MainLayout title="Parallel Session Management">{page}</MainLayout>;

export default ParallelSessionIndex;