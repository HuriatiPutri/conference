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
import { MembershipBenefit, TableData } from './TableData';

interface Props {
  membershipBenefits: {
    data: MembershipBenefit[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  filters: {
    search?: string;
  };
}

function MembershipBenefitsIndex() {
  const { membershipBenefits, filters } = usePage<Props>().props;
  const data = membershipBenefits?.data || [];

  const [globalFilterValue, setGlobalFilterValue] = useState(filters?.search || '');
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

  const handleDelete = (benefit: MembershipBenefit) => {
    if (confirm('Are you sure you want to delete this benefit?')) {
      router.delete(route('membership-benefits.destroy', benefit.id), {
        preserveScroll: true,
      });
    }
  };

  const renderHeader = () => (
    <Group justify="space-between" mb="md">
      <ActionIcon
        onClick={() => router.get(route('membership-benefits.create'))}
        title="Add Benefit"
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

  return (
    <MainLayout title="Membership Benefits">
      <Container size="xl">
        <Stack gap="lg">
          <Group justify="space-between">
            <div>
              <Title order={2}>Membership Benefits</Title>
              <Text c="dimmed">Manage available membership benefits</Text>
            </div>
          </Group>

          <Card withBorder>
            <DataTable
              value={data}
              header={renderHeader()}
              paginator
              rows={membershipBenefits?.per_page || 15}
              totalRecords={membershipBenefits?.total || 0}
              lazy
              first={((membershipBenefits?.current_page ?? 1) - 1) * (membershipBenefits?.per_page || 15)}
              onPage={onPage}
              stripedRows
              showGridlines
              emptyMessage="No membership benefits found."
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

export default MembershipBenefitsIndex;
