import { router, usePage } from '@inertiajs/react';
import { ActionIcon, Card, Container, Grid, Group, Stack, Text, Title } from '@mantine/core';
import { IconPlus } from '@tabler/icons-react';
import { Column } from 'primereact/column';
import { DataTable, DataTableStateEvent } from 'primereact/datatable';
import { IconField } from 'primereact/iconfield';
import { InputIcon } from 'primereact/inputicon';
import { InputText } from 'primereact/inputtext';
import React, { useEffect, useRef, useState } from 'react';
import { route } from 'ziggy-js';
import MainLayout from '../../../Layout/MainLayout';
import { TableData } from './TableData';

interface LoaVolume {
  id: number;
  volume: string;
  created_at: string;
  updated_at: string;
  audiences_count: number;
  joiv_registrations_count: number;
  creator?: {
    id: number;
    name: string;
  };
  updater?: {
    id: number;
    name: string;
  };
}

interface Props {
  loaVolumes: {
    data: LoaVolume[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  summary: {
    total: number;
    this_month: number;
  };
  search: string;
  [key: string]: unknown;
}

function LoaVolumeIndex() {
  const { loaVolumes, summary, search } = usePage<Props>().props;

  const { data } = loaVolumes;

  // Initialize search value from URL params or props
  const [globalFilterValue, setGlobalFilterValue] = useState(() => {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('search') || search || '';
  });

  const [searchTimeout, setSearchTimeout] = useState<number | null>(null);
  const searchInputRef = useRef<HTMLInputElement>(null);

  // Auto-focus effect
  useEffect(() => {
    if (globalFilterValue && searchInputRef.current) {
      searchInputRef.current.focus();
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

  const handleFilterChange = (searchValue?: string) => {
    const params = new URLSearchParams();

    const currentSearch = searchValue !== undefined ? searchValue : globalFilterValue;
    if (currentSearch && currentSearch.trim()) {
      params.append('search', currentSearch.trim());
    }

    router.get(`${window.location.pathname}?${params.toString()}`, {}, {
      preserveState: true,
      preserveScroll: true,
    });
  };

  const onGlobalFilterChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const value = e.target.value;
    setGlobalFilterValue(value);

    // Clear previous timeout
    if (searchTimeout) {
      clearTimeout(searchTimeout);
    }

    // Set new timeout for debounced search
    const newTimeout = setTimeout(() => {
      handleFilterChange(value);
    }, 500);

    setSearchTimeout(newTimeout);
  };

  const onPage = (event: DataTableStateEvent) => {
    const params = new URLSearchParams();
    if (globalFilterValue.trim()) params.append('search', globalFilterValue.trim());
    params.append('page', ((event.first! / event.rows!) + 1).toString());
    params.append('per_page', event.rows!.toString());

    router.get(`${window.location.pathname}?${params.toString()}`, {}, {
      preserveState: true,
      preserveScroll: true,
    });
  };

  const handleDelete = (loaVolume: LoaVolume) => {
    if (confirm('Are you sure you want to delete this LoA Volume?')) {
      router.delete(route('loa.loa-volumes.destroy', loaVolume.id), {
        preserveScroll: true,
      });
    }
  };

  const renderHeader = () => {
    return (
      <Group justify="space-between" mb="md">
        <ActionIcon
          onClick={() => router.get(route('loa.loa-volumes.create'))}
        >
          <IconPlus size="1rem" />
        </ActionIcon>
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
      </Group>
    );
  };

  return (
    <MainLayout title='Volume'>
      <Container size="xl">
        <Stack gap="lg">
          <Group justify="space-between">
            <div>
              <Title order={2}>LoA Volume Management</Title>
              <Text c="dimmed">Manage LoA Volumes</Text>
            </div>
          </Group>

          {/* Summary Cards */}
          <Grid>
            <Grid.Col span={{ base: 12, md: 6 }}>
              <Card withBorder>
                <Group justify="space-between">
                  <div>
                    <Text c="dimmed" size="sm" fw={500}>
                      Total LoA Volumes
                    </Text>
                    <Text fw={700} size="xl">
                      {summary.total}
                    </Text>
                  </div>
                </Group>
              </Card>
            </Grid.Col>
            <Grid.Col span={{ base: 12, md: 6 }}>
              <Card withBorder>
                <Group justify="space-between">
                  <div>
                    <Text c="dimmed" size="sm" fw={500}>
                      Added This Month
                    </Text>
                    <Text fw={700} size="xl">
                      {summary.this_month}
                    </Text>
                  </div>
                </Group>
              </Card>
            </Grid.Col>
          </Grid>

          {/* Data Table */}
          <Card withBorder>
            <DataTable
              value={data}
              header={renderHeader()}
              paginator
              rows={loaVolumes?.per_page || 15}
              totalRecords={loaVolumes?.total || 0}
              lazy
              first={(loaVolumes?.current_page - 1) * (loaVolumes?.per_page || 15)}
              onPage={onPage}
              loading={false}
              stripedRows
              showGridlines
              emptyMessage="No LoA volumes found."
              paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
              currentPageReportTemplate="{first} to {last} of {totalRecords}"
              rowsPerPageOptions={[15, 25, 50]}
            >
              {TableData({ handleDelete }).map((col, index) => (
                <Column
                  key={index}
                  field={col.field}
                  header={col.label}
                  body={col.renderCell}
                  sortable={col.sortable}
                  style={col.style}
                  className={col.className}
                />
              ))}
            </DataTable>
          </Card>
        </Stack>
      </Container>
    </MainLayout>
  );
}

export default LoaVolumeIndex;