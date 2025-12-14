import { router, usePage } from '@inertiajs/react';
import { Column } from 'primereact/column';
import { DataTable, DataTableStateEvent } from 'primereact/datatable';
import React, { useMemo, useState } from 'react';
import MainLayout from '../../../Layout/MainLayout';
import { Conference, PaginatedData } from '../../../types';
import { ActionIcon, Container, Group, Stack, Text, Title } from '@mantine/core';
import { FilterMatchMode } from 'primereact/api';
import { ColumnGroup } from 'primereact/columngroup';
import { IconField } from 'primereact/iconfield';
import { InputIcon } from 'primereact/inputicon';
import { InputText } from 'primereact/inputtext';
import { Row } from 'primereact/row';
import { TableData } from './TableData';

interface ColumnType {
  label: string;
  name: string;
  colspan?: number;
  rowspan?: number;
  [key: string]: unknown;
}

function Home() {
  const { conferences } = usePage<{
    conferences: PaginatedData<Conference>;
  }>().props;

  const { data, meta } = conferences;

  const handleDelete = (id: number) => {
    if (!confirm('Delete this conference?')) return;
    router.delete(`/conferences/${id}`, {
      preserveScroll: true,
      onError: () => alert('Failed to delete conference.'),
    });
  };

  const [globalFilterValue, setGlobalFilterValue] = useState('');
  const [filters, setFilters] = useState({
    global: { value: null as string | null, matchMode: FilterMatchMode.CONTAINS },
    name: { value: null as string | null, matchMode: FilterMatchMode.STARTS_WITH },
    initial: { value: null as string | null, matchMode: FilterMatchMode.STARTS_WITH },
    date: { value: null as string | null, matchMode: FilterMatchMode.IN },
    city: { value: null as string | null, matchMode: FilterMatchMode.EQUALS },
  });

  const renderHeader = () => {
    return (
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', padding: '1rem' }}>
        <ActionIcon
          onClick={() => (router.visit('/conferences/create'))}
          color="blue"
          variant="filled"
          size="lg"
        >
          <i className="pi pi-fw pi-plus"></i>
        </ActionIcon>
        <IconField iconPosition="left">
          <InputIcon className="pi pi-search" />
          <InputText
            value={globalFilterValue}
            style={{ width: '300px' }}
            onChange={onGlobalFilterChange}
            size={'small'}
            placeholder="Keyword Search"
          />
        </IconField>
      </div >
    );
  };

  const handlePageChange = (event: DataTableStateEvent) => {
    if (event.page !== undefined) {
      const params = new URLSearchParams(window.location.search);
      params.set('page', (event.page + 1).toString());
      if (event.rows && event.rows !== meta.per_page) {
        params.set('per_page', event.rows.toString());
      }
      if (globalFilterValue) {
        params.set('global', globalFilterValue);
      }

      router.visit(`/conferences?${params.toString()}`);
    }
  }

  const onGlobalFilterChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const value = e.target.value;
    const _filters = { ...filters };

    _filters['global'].value = value;

    setFilters(_filters);
    setGlobalFilterValue(value);
  };

  const header = renderHeader();

  const mergedHeaderColumns = useMemo(() => {
    const map = new Map<string, ColumnType>();
    TableData({ handleDelete }).forEach(col => {
      const span = col.colspan || col.colspan || 1;
      if (!map.has(col.label)) {
        map.set(col.label, { ...col, colspan: span });
      } else {
        const existing = map.get(col.label);
        if (existing) {
          existing.colspan = span;
        }
      }
    });
    return Array.from(map.values());
  }, [TableData]);

  const headerGroup = (
    <ColumnGroup>
      <Row>
        {mergedHeaderColumns.map(col => (
          <Column
            key={col.name}
            header={col.label}
            colSpan={col.colspan || 1}
            rowSpan={col.rowspan || 1}
            align={'center'}
          />
        ))}
      </Row>
      <Row>
        <Column header="Domestic" field="online_fee" align={'center'} />
        <Column header="International" field="online_fee_usd" align={'center'} />
        <Column header="Domestic" field="onsite_fee" align={'center'} />
        <Column header="International" field="onsite_fee_usd" align={'center'} />
        <Column header="Domestic" field="participant_fee" align={'center'} />
        <Column header="International" field="participant_fee_usd" align={'center'} />
      </Row>
    </ColumnGroup>
  );
  return (
    <Container fluid>
      <Stack gap="lg">
        {/* Header */}
        <Group justify="space-between">
          <div>
            <Title order={2}>Conference Management</Title>
            <Text c="dimmed">Manage conferences, settings, and configurations</Text>
          </div>
        </Group>
        <div style={{ padding: '24px', backgroundColor: 'white', borderRadius: '8px', border: '1px solid #e9ecef' }}>
          <DataTable
            value={data}
            size="small"
            stripedRows
            lazy
            onPage={handlePageChange}
            first={(meta.current_page - 1) * meta.per_page}
            resizableColumns
            globalFilterFields={['name', 'initial', 'date', 'city', 'country', 'year']}
            header={header}
            filters={filters}
            headerColumnGroup={headerGroup}
            showGridlines
            paginator
            alwaysShowPaginator
            tableStyle={{ minWidth: '100rem', fontSize: '14px' }}
            rows={meta.per_page}
            totalRecords={meta.total}
            rowsPerPageOptions={[15, 25, 50, 100]}
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
            currentPageReportTemplate="Showing {first} to {last} of {totalRecords} entries"
          >
            {TableData({ handleDelete }).map(col => (
              <Column
                key={col.name}
                field={col.name}
                header={col.label}
                body={col.renderCell}
                sortable={col.sortable}
                frozen={col.frozen}
                style={{
                  alignItems: 'top',
                  textAlign: 'left',
                  width: col.width ? col.width : 'auto',
                }}
              />
            ))}
          </DataTable>
        </div>
      </Stack>
    </Container>
  );
}

Home.layout = (page: React.ReactNode) => <MainLayout title="Conference Management">{page}</MainLayout>;

export default Home;
