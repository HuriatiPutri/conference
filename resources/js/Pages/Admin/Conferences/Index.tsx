import { router, usePage } from '@inertiajs/react';
import dayjs from 'dayjs';
import { Column } from 'primereact/column';
import { DataTable } from 'primereact/datatable';
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
  const { data } = conferences;

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
      className: 'text-wrap w-25',
      width: '10%',
      rowspan: 2,
      renderCell: (row: Conference) => (
        <>
          {row.name}
          {
            row.deleted_at && 'detelted'
            // <Trash2 size={16} className="ml-2 text-gray-400" />
          }
        </>
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
            handleClick={() => (window.location.href = `/conferences/${row.id}/edit`)}
            icon="pi pi-fw pi-pencil"
          />
          <ActionButtonExt
            color="green"
            handleClick={() => (window.location.href = `/conferences/${row.id}/show`)}
            icon="pi pi-fw pi-eye"
          />
          <ActionButtonExt
            color="yellow"
            handleClick={() => (window.location.href = `/conferences/${row.id}/setting`)}
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
          onClick={() => (window.location.href = '/conferences/create')}
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
      </div>
    );
  };

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
            resizableColumns
            globalFilterFields={['name', 'initial', 'date', 'city', 'country', 'year']}
            header={header}
            filters={filters}
            headerColumnGroup={headerGroup}
            showGridlines
            alwaysShowPaginator
            tableStyle={{ minWidth: '100rem', fontSize: '14px' }}
            paginator
            rows={10}
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
