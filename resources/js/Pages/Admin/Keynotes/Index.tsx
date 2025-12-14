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

interface KeyNote {
  id: number;
  name_of_participant: string;
  feedback: string;
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
}

interface Props {
  keynotes: {
    data: KeyNote[];
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

function KeynoteIndex() {
  const { keynotes, filters, conferences } = usePage<Props>().props;
  const { data } = keynotes;

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

    router.visit(`/keynotes?${params.toString()}`);
  };

  const handlePageChange = (event: DataTableStateEvent) => {
    if (event.page !== undefined) {
      const params = new URLSearchParams(window.location.search);
      params.set('page', (event.page + 1).toString());
      if (event.rows && event.rows !== keynotes.per_page) {
        params.set('per_page', event.rows.toString());
      }
      if (conferenceFilter) params.set('conference_id', conferenceFilter);
      if (globalFilterValue.trim()) params.set('search', globalFilterValue.trim());

      router.visit(`/keynotes?${params.toString()}`);
    }
  };

  const clearFilters = () => {
    setGlobalFilterValue('');
    router.visit('/keynotes');
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

    router.visit(`/keynotes?${params.toString()}`);
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
            <Title order={2}>Keynote Management</Title>
            <Text c="dimmed">Manage keynotes, settings, and configurations</Text>
          </div>
        </Group>
        <FilterData
          conferences={conferences}
          conferenceFilter={conferenceFilter}
          setConferenceFilter={setConferenceFilter}
          handleFilterChange={handleFilterChange}
          clearFilters={clearFilters}
        />
        <Card padding="lg" radius="md" withBorder>
          {/* Pagination Info */}
          <Text size="sm" c="dimmed" mb="md">
            Showing {((keynotes.current_page - 1) * keynotes.per_page) + 1} to {Math.min(keynotes.current_page * keynotes.per_page, keynotes.total)} of {keynotes.total} entries
          </Text>
          <DataTable
            value={data}
            lazy
            paginator
            first={(keynotes.current_page - 1) * keynotes.per_page}
            rows={keynotes.per_page}
            totalRecords={keynotes.total}
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
            {TableData().map(col => (
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

KeynoteIndex.layout = (page: React.ReactNode) => <MainLayout title="Keynote Management">{page}</MainLayout>;

export default KeynoteIndex;