import React, { useEffect, useRef, useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import { Card, Container, Group, Stack, Text, Title, Badge } from '@mantine/core';
import { Column } from 'primereact/column';
import { DataTable, DataTableStateEvent } from 'primereact/datatable';
import { IconField } from 'primereact/iconfield';
import { InputIcon } from 'primereact/inputicon';
import { InputText } from 'primereact/inputtext';
import MainLayout from '../../../Layout/MainLayout';

type BenefitUsageRow = {
  id: number;
  benefit_type: string;
  consumed_value: number | string;
  reference_type: string;
  reference_id: number;
  created_at: string;
  user?: { name?: string | null; email?: string | null } | null;
  membership?: {
    email?: string | null;
    first_name?: string | null;
    last_name?: string | null;
    package?: { name?: string | null } | null;
  } | null;
  membershipBenefit?: { code?: string | null; name?: string | null } | null;
  membership_benefit?: { code?: string | null; name?: string | null } | null;
  packageBenefit?: { package?: { name?: string | null } | null } | null;
  reference?: { public_id?: string | null; first_name?: string | null; last_name?: string | null; paper_title?: string | null } | null;
};

type PageProps = {
  benefitUsages: {
    data: BenefitUsageRow[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  filters: {
    search?: string;
    benefit_type?: string;
    reference_type?: string;
  };
};

function BenefitUsagesIndex() {
  const { benefitUsages, filters } = usePage<PageProps>().props;
  const data = benefitUsages?.data || [];

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

  const handleSearch = (searchValue: string) => {
    const params = new URLSearchParams();

    if (searchValue.trim()) params.append('search', searchValue.trim());
    router.visit(`/benefit-usages?${params.toString()}`);
  };

  const onGlobalFilterChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const value = e.target.value;
    setGlobalFilterValue(value);

    if (searchTimeout) clearTimeout(searchTimeout);

    const newTimeout = globalThis.setTimeout(() => {
      handleSearch(value);
    }, 500);

    setSearchTimeout(newTimeout);
  };

  const onPage = (event: DataTableStateEvent) => {
    const params = new URLSearchParams();

    if (globalFilterValue.trim()) params.append('search', globalFilterValue.trim());
    params.append('page', ((event.first! / event.rows!) + 1).toString());
    params.append('per_page', event.rows!.toString());

    router.visit(`/benefit-usages?${params.toString()}`);
  };

  const renderHeader = () => (
    <Group justify="space-between" mb="md">
      <div>
        <Text size="sm" c="dimmed">Audit log of benefit usage across conference and JOIV registrations</Text>
      </div>
      <IconField iconPosition="left">
        <InputIcon className="pi pi-search" />
        <InputText
          ref={searchInputRef}
          value={globalFilterValue}
          onChange={onGlobalFilterChange}
          size={'small'}
          placeholder="Search by user, membership, benefit, or reference"
          style={{ width: 340 }}
        />
      </IconField>
    </Group>
  );

  const renderReference = (row: BenefitUsageRow) => {
    if (row.reference_type?.includes('Audience')) {
      return row.reference?.public_id ? `Conference: ${row.reference.public_id}` : `Conference Ref #${row.reference_id}`;
    }

    if (row.reference_type?.includes('JoivRegistration')) {
      return row.reference?.public_id ? `JOIV: ${row.reference.public_id}` : `JOIV Ref #${row.reference_id}`;
    }

    return row.reference_type?.split('\\').pop() || row.reference_type || '-';
  };

  const pagination = benefitUsages;

  return (
    <MainLayout title="Benefit Usage History">
      <Container size="xl">
        <Stack gap="lg">
          <Group justify="space-between">
            <div>
              <Title order={2}>Benefit Usage History</Title>
              <Text c="dimmed">Track benefit consumption for auditing and quota control</Text>
            </div>
          </Group>

          <Card withBorder>
            <DataTable
              value={data}
              header={renderHeader()}
              paginator
              lazy
              first={((pagination?.current_page ?? 1) - 1) * (pagination?.per_page ?? 15)}
              onPage={onPage}
              rows={pagination?.per_page ?? 15}
              totalRecords={pagination?.total ?? data.length}
              rowsPerPageOptions={[15, 25, 50]}
              stripedRows
              showGridlines
              emptyMessage="No benefit usage found."
              paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
              currentPageReportTemplate="Showing {first} to {last} of {totalRecords} entries"
              tableStyle={{ minWidth: '120rem', fontSize: '14px' }}
            >
              <Column
                field="created_at"
                header="Date"
                body={(row: BenefitUsageRow) => new Date(row.created_at).toLocaleString('id-ID')}
                sortable
              />
              <Column
                field="user"
                header="User"
                body={(row: BenefitUsageRow) => row.user?.name || row.user?.email || row.membership?.email || '-'}
              />
              <Column
                field="membership"
                header="Membership Package"
                body={(row: BenefitUsageRow) => row.membership?.package?.name || row.packageBenefit?.package?.name || '-'}
              />
              <Column
                field="benefit"
                header="Benefit"
                body={(row: BenefitUsageRow) => row.membershipBenefit?.name || row.membership_benefit?.name || '-'}
              />
              <Column
                field="benefit_type"
                header="Type"
                body={(row: BenefitUsageRow) => (
                  <Badge color={row.benefit_type === 'discount' ? 'green' : 'blue'} variant="light">
                    {row.benefit_type}
                  </Badge>
                )}
              />
              <Column
                field="consumed_value"
                header="Consumed"
                body={(row: BenefitUsageRow) => Number(row.consumed_value).toLocaleString('id-ID')}
              />
              <Column field="reference" header="Reference" body={renderReference} />
              <Column field="reference_id" header="Ref ID" />
            </DataTable>
          </Card>
        </Stack>
      </Container>
    </MainLayout>
  );
}

export default BenefitUsagesIndex;