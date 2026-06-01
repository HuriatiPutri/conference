import { router, usePage } from '@inertiajs/react';
import { ActionIcon, Button, Card, Container, Group, Stack, Text, Title } from '@mantine/core';
import { IconPlus, IconReport } from '@tabler/icons-react';
import { Column } from 'primereact/column';
import { DataTable, DataTableStateEvent } from 'primereact/datatable';
import { IconField } from 'primereact/iconfield';
import { InputIcon } from 'primereact/inputicon';
import { InputText } from 'primereact/inputtext';
import React, { useState } from 'react';
import { route } from 'ziggy-js';
import MainLayout from '../../../Layout/MainLayout';
import { TableData } from './TableData';

interface Props {
  vouchers: {
    data: any[];
    current_page: number;
    per_page: number;
    total: number;
  };
  filters: {
    search?: string;
  };
}

function VoucherIndex() {
  const { vouchers, filters } = usePage<Props>().props;
  const [globalFilterValue, setGlobalFilterValue] = useState(filters.search || '');

  const onGlobalFilterChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const value = e.target.value;
    setGlobalFilterValue(value);

    const params = new URLSearchParams();
    if (value.trim()) params.append('search', value.trim());

    router.get(`${globalThis.location.pathname}?${params.toString()}`, {}, {
      preserveState: true,
      preserveScroll: true,
    });
  };

  const onPage = (event: DataTableStateEvent) => {
    const params = new URLSearchParams();
    const first = event.first ?? 0;
    const rows = event.rows ?? (vouchers?.per_page || 15);
    if (globalFilterValue.trim()) params.append('search', globalFilterValue.trim());
    params.append('page', ((first / rows) + 1).toString());
    params.append('per_page', rows.toString());

    router.get(`${globalThis.location.pathname}?${params.toString()}`, {}, {
      preserveState: true,
      preserveScroll: true,
    });
  };

  const handleDelete = (voucher: any) => {
    if (confirm('Are you sure want to delete this voucher?')) {
      router.delete(route('vouchers.destroy', voucher.id));
    }
  };

  const renderHeader = () => (
    <Group justify="space-between" mb="md">
      <ActionIcon onClick={() => router.get(route('vouchers.create'))}>
        <IconPlus size="1rem" />
      </ActionIcon>
      <IconField iconPosition="left">
        <InputIcon className="pi pi-search" />
        <InputText
          value={globalFilterValue}
          onChange={onGlobalFilterChange}
          size={'small'}
          placeholder="Search voucher code"
        />
      </IconField>
    </Group>
  );

  return (
    <MainLayout title="Voucher Settings">
      <Container size="xl">
        <Stack gap="lg">
          <Group justify="space-between">
            <div>
              <Title order={2}>Voucher Settings</Title>
              <Text c="dimmed">Manage voucher code, period, quota, and applicable transactions</Text>
            </div>
            <Button variant="light" onClick={() => router.get(route('vouchers.report'))} leftSection={<IconReport size={16} />}>
              View Report
            </Button>
          </Group>

          <Card withBorder>
            <DataTable
              value={vouchers.data}
              header={renderHeader()}
              paginator
              rows={vouchers?.per_page || 15}
              totalRecords={vouchers?.total || 0}
              lazy
              first={(vouchers?.current_page - 1) * (vouchers?.per_page || 15)}
              onPage={onPage}
              stripedRows
              showGridlines
              emptyMessage="No vouchers found."
              paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
              currentPageReportTemplate="{first} to {last} of {totalRecords}"
              rowsPerPageOptions={[15, 25, 50]}
            >
              {TableData({ handleDelete }).map((col, index) => (
                <Column
                  key={col.field}
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

export default VoucherIndex;
