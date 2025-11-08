import { router, usePage } from '@inertiajs/react';
import dayjs from 'dayjs';
import { Column } from 'primereact/column';
import { DataTable, DataTableStateEvent } from 'primereact/datatable';
import React, { useMemo, useState } from 'react';
import MainLayout from '../../../Layout/MainLayout';
import { Conference, PaginatedData } from '../../../types';
import { formatCurrency } from '../../../utils';
// import { Button } from 'primereact/button';
import { ActionIcon, Container, Group, Stack, Text, Title } from '@mantine/core';
import { FilterMatchMode } from 'primereact/api';
import { ColumnGroup } from 'primereact/columngroup';
import { IconField } from 'primereact/iconfield';
import { InputIcon } from 'primereact/inputicon';
import { InputText } from 'primereact/inputtext';
import { Row } from 'primereact/row';
import { ActionButtonExt, CopyButtonExt } from './ExtendComponent';

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

  const baseUrl = window.location.origin;
  const { data, meta } = conferences;

  const handleDelete = (id: number) => {
    if (!confirm('Delete this conference?')) return;
    router.delete(`/conferences/${id}`, {
      preserveScroll: true,
      onError: () => alert('Failed to delete conference.'),
    });
  };

  const columns = [
    {
      label: 'No.',
      name: 'id',
      sortable: true,
      rowspan: 2,
      renderCell: (_: Conference, { rowIndex }: { rowIndex: number }) => rowIndex + 1
    },
    {
      label: 'Name',
      name: 'name',
      sortable: true,
      className: 'text-wrap w-40',
      width: '10%',
      rowspan: 2,
      renderCell: (row: Conference) => (
        <Text size='sm' style={{ textWrap: 'wrap' }}>
          {row.name}
          {
            row.deleted_at && 'detelted'
            // <Trash2 size={16} className="ml-2 text-gray-400" />
          }
        </Text>
      ),
    },
    { label: 'Initial', name: 'initial', rowspan: 2 },
    {
      label: 'Date',
      name: 'date',
      sortable: true,
      rowspan: 2,
      renderCell: (row: Conference) => dayjs(row.date).format('DD MMM YYYY'),
    },
    {
      label: 'Registation Date',
      name: 'registration_date',
      sortable: true,
      rowspan: 2,
      renderCell: (row: Conference) =>
        `${dayjs(row.registration_start_date).format('DD MMM YYYY')} - ${dayjs(row.registration_end_date).format('DD MMM YYYY')}`,
    },
    {
      label: 'Location',
      name: 'city',
      rowspan: 2,
      renderCell: (row: Conference) => `${row.city},${row.country}` || 'N/A',
    },
    { label: 'Year', name: 'year', rowspan: 2 },
    {
      label: 'Online Fee',
      name: 'online_fee',
      sortable: true,
      colspan: 2,
      renderCell: (row: Conference) => (
        <Text fw={500} ta={'right'}>
          {formatCurrency(row.online_fee)}
        </Text>
      ),
    },
    {
      label: 'Online Fee',
      name: 'online_fee_usd',
      sortable: true,
      colspan: 2,
      renderCell: (row: Conference) => (
        <Text fw={500} ta={'right'}>
          {formatCurrency(row.online_fee_usd, 'usd')}
        </Text>
      ),
    },
    {
      label: 'Onsite Fee',
      name: 'onsite_fee',
      sortable: true,
      colspan: 2,
      renderCell: (row: Conference) => (
        <Text fw={500} ta={'right'}>
          {formatCurrency(row.onsite_fee_usd, 'usd')}
        </Text>
      ),
    },
    {
      label: 'Onsite Fee',
      name: 'onsite_fee',
      sortable: true,
      colspan: 2,
      renderCell: (row: Conference) => (
        <Text fw={500} ta={'right'}>
          {formatCurrency(row.onsite_fee_usd, 'usd')}
        </Text>
      ),
    },
    {
      label: 'Participant Fee',
      name: 'participant_fee',
      sortable: true,
      colspan: 2,
      renderCell: (row: Conference) => (
        <Text fw={500} ta={'right'}>
          {formatCurrency(row.participant_fee)}
        </Text>
      ),
    },
    {
      label: 'Participant Fee',
      name: 'participant_fee',
      sortable: true,
      colspan: 2,
      renderCell: (row: Conference) => (
        <Text fw={500} ta={'right'}>
          {formatCurrency(row.participant_fee_usd, 'usd')}
        </Text>
      ),
    },
    {
      label: 'Registration Links',
      name: 'registration_link',
      rowspan: 2,
      renderCell: (row: Conference) => (
        <Stack gap={'xs'}>
          <CopyButtonExt value={`${baseUrl}/registration/${row.public_id}`} label={'Registration'} />
          <CopyButtonExt value={`${baseUrl}/keynote/${row.public_id}`} label={'Key Note'} />
          <CopyButtonExt value={`${baseUrl}/parallel-session/${row.public_id}`} label={'Parallel Session'} />
        </Stack>
      ),
    },
    {
      label: 'Action',
      name: 'action',
      rowspan: 2,
      frozen: true,
      renderCell: (row: Conference) => (
        <Stack gap={'xs'} justify="center" align="center">
          <ActionButtonExt
            color="blue"
            handleClick={() => (router.visit(`/conferences/${row.id}/edit`))}
            icon="pi pi-fw pi-pencil"
          />
          <ActionButtonExt
            color="green"
            handleClick={() => (router.visit(`/conferences/${row.id}/show`))}
            icon="pi pi-fw pi-eye"
          />
          <ActionButtonExt
            color="yellow"
            handleClick={() => (router.visit(`/conferences/${row.id}/setting`))}
            icon="pi pi-fw pi-cog"
          />
          <ActionButtonExt
            color="red"
            handleClick={() => handleDelete(row.id)}
            icon="pi pi-fw pi-trash"
          />
        </Stack>
      ),
    },
  ];

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
    columns.forEach(col => {
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
  }, [columns]);

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
            {columns.map(col => (
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
