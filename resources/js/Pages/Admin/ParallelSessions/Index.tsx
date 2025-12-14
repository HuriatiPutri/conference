import { router, usePage } from '@inertiajs/react';
import { ActionIcon, Card, Container, Flex, Group, Stack, Text, Title } from '@mantine/core';
import { Column } from 'primereact/column';
import { DataTable, DataTableStateEvent } from 'primereact/datatable';
import { IconField } from 'primereact/iconfield';
import { InputIcon } from 'primereact/inputicon';
import { InputText } from 'primereact/inputtext';
import React, { useEffect, useRef, useState } from 'react';
import MainLayout from '../../../Layout/MainLayout';
import FilterData from './FilterData';
import { TableData } from './TableData';

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

  console.log('data:', data);
  // Initialize search value from URL params
  const [globalFilterValue, setGlobalFilterValue] = useState(() => {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('search') || '';
  });
  const [conferenceFilter, setConferenceFilter] = useState(filters?.conference_id || '');
  const [searchTimeout, setSearchTimeout] = useState<number | null>(null);
  const searchInputRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    if (globalFilterValue && searchInputRef.current) {
      // Keep focus on search input after search
      searchInputRef.current.focus();
      // Maintain cursor at the end of the text
      const length = globalFilterValue.length;
      searchInputRef.current.setSelectionRange(length, length);
    }
  }, [globalFilterValue]);

  // Auto-focus on component mount if there's a search value
  useEffect(() => {
    if (globalFilterValue && searchInputRef.current) {
      setTimeout(() => {
        searchInputRef.current?.focus();
        const length = globalFilterValue.length;
        searchInputRef.current?.setSelectionRange(length, length);
      }, 100);
    }
  }, []);

  const handleFilterChange = () => {
    const params = new URLSearchParams();
    if (conferenceFilter) params.append('conference_id', conferenceFilter);
    if (globalFilterValue.trim()) params.append('search', globalFilterValue.trim());

    router.visit(`/parallel-sessions?${params.toString()}`);
  };

  const handlePageChange = (event: DataTableStateEvent) => {
    if (event.page !== undefined) {
      const params = new URLSearchParams(window.location.search);
      params.set('page', (event.page + 1).toString());
      if (event.rows && event.rows !== parallelSessions.per_page) {
        params.set('per_page', event.rows.toString());
      }
      if (conferenceFilter) params.set('conference_id', conferenceFilter);
      if (globalFilterValue.trim()) params.set('search', globalFilterValue.trim());

      router.visit(`/parallel-sessions?${params.toString()}`);
    }
  };

  const clearFilters = () => {
    setGlobalFilterValue('');
    router.visit('/parallel-sessions');
  };

  const onGlobalFilterChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const value = e.target.value;
    setGlobalFilterValue(value);

    // Debounce search to avoid too many requests
    if (searchTimeout) {
      clearTimeout(searchTimeout);
    }

    const timeout = setTimeout(() => {
      handleSearch(value);
    }, 500);

    setSearchTimeout(timeout);
  };

  const handleSearch = (searchValue: string) => {
    const params = new URLSearchParams();
    if (conferenceFilter) params.append('conference_id', conferenceFilter);
    if (searchValue.trim()) params.append('search', searchValue.trim());

    router.visit(`/parallel-sessions?${params.toString()}`);
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
            ref={searchInputRef}
            value={globalFilterValue}
            onChange={onGlobalFilterChange}
            size={'small'}
            placeholder="Keyword Search"
            autoFocus={!!globalFilterValue}
          />
        </IconField>
      </Flex>
    );
  };



  return (
    <Container fluid>
      <Stack gap="lg">
        {/* Header */}
        <Group justify="space-between">
          <div>
            <Title order={2}>Parallel Session Management</Title>
            <Text c="dimmed">Manage parallel sessions, settings, and configurations</Text>
          </div>
        </Group>
        <FilterData
          conferences={conferences}
          conferenceFilter={conferenceFilter}
          setConferenceFilter={setConferenceFilter}
          handleFilterChange={handleFilterChange}
          clearFilters={clearFilters}
        />

        <Card mb="lg" padding="lg" radius="md" withBorder>
          {/* Pagination Info */}
          <Text size="sm" c="dimmed" mb="md">
            Showing {((parallelSessions.current_page - 1) * parallelSessions.per_page) + 1} to {Math.min(parallelSessions.current_page * parallelSessions.per_page, parallelSessions.total)} of {parallelSessions.total} entries
          </Text>
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
            {TableData.map(col => (
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
        </Card>
      </Stack>
    </Container>
  );
}

ParallelSessionIndex.layout = (page: React.ReactNode) => <MainLayout title="Parallel Session Management">{page}</MainLayout>;

export default ParallelSessionIndex;