import { router, usePage } from '@inertiajs/react';
import { ActionIcon, Card, Container, Group, Stack, Text, Title } from '@mantine/core';
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

interface Package {
  id: number;
  name: string;
  price_idr: number;
  price_usd?: number;
  duration: number;
  status: string;
}

interface Props {
  packages: {
    data: Package[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  filters: any;
}

function PackageIndex() {
  const { packages, filters } = usePage<Props>().props;

  const { data } = packages;

  const [globalFilterValue, setGlobalFilterValue] = useState(filters.search || '');
  const [searchTimeout, setSearchTimeout] = useState<number | null>(null);
  const searchInputRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    if (globalFilterValue && searchInputRef.current) {
      searchInputRef.current.focus();
      const length = globalFilterValue.length;
      searchInputRef.current.setSelectionRange(length, length);
    }
  }, [globalFilterValue]);

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

    if (searchTimeout) clearTimeout(searchTimeout);

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

  const handleDelete = (pkg: Package) => {
    if (confirm('Are you sure want to delete this package?')) {
      router.delete(route('packages.destroy', pkg.id));
    }
  };

  const renderHeader = () => (
    <Group justify="space-between" mb="md">
      <ActionIcon onClick={() => router.get(route('packages.create'))}>
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
        />
      </IconField>
    </Group>
  );

  return (
    <MainLayout title="Packages">
      <Container size="xl">
        <Stack gap="lg">
          <Group justify="space-between">
            <div>
              <Title order={2}>Package Management</Title>
              <Text c="dimmed">Manage subscription packages</Text>
            </div>
          </Group>

          <Card withBorder>
            <DataTable
              value={data}
              header={renderHeader()}
              paginator
              rows={packages?.per_page || 15}
              totalRecords={packages?.total || 0}
              lazy
              first={(packages?.current_page - 1) * (packages?.per_page || 15)}
              onPage={onPage}
              stripedRows
              showGridlines
              emptyMessage="No packages found."
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
                />
              ))}
            </DataTable>
          </Card>
        </Stack>
      </Container>
    </MainLayout>
  );
}

export default PackageIndex;
