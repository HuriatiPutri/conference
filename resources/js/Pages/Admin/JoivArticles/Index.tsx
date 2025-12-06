import { router, usePage } from '@inertiajs/react';
import { Button, Card, Container, Grid, Group, Select, Stack, Text, Title, Badge, ActionIcon, Flex } from '@mantine/core';
import { IconCircleCheckFilled, IconCircleDashed, IconRestore, IconXboxXFilled } from '@tabler/icons-react';
import { Column } from 'primereact/column';
import { DataTable, DataTableStateEvent } from 'primereact/datatable';
import { IconField } from 'primereact/iconfield';
import { InputIcon } from 'primereact/inputicon';
import { InputText } from 'primereact/inputtext';
import React, { useEffect, useRef, useState } from 'react';
import { route } from 'ziggy-js';
import MainLayout from '../../../Layout/MainLayout';
import { JoivRegistrationFee, PaginatedData } from '../../../types';
import { formatCurrency } from '../../../utils';
import { ActionButtonExt } from '../Conferences/ExtendComponent';
import { PAYMENT_METHOD } from '../../../Constants';
import { JoivPaymentStatusModal } from '../../../Components/Modals/JoivPaymentStatusModal';
import CurrentFee from './CurrentFee';

interface JoivRegistration {
  id: number;
  public_id: string;
  first_name: string;
  last_name: string;
  email_address: string;
  phone_number: string;
  institution: string;
  country: string;
  paper_id: string | null;
  paper_title: string;
  full_paper_path: string | null;
  payment_status: string;
  payment_method: string | null;
  paid_fee: number;
  created_at: string;
}

function JoivArticleIndex() {
  const { registrations, filters, summary, countries, currentFee } = usePage<{
    registrations: PaginatedData<JoivRegistration>;
    currentFee: JoivRegistrationFee | null;
    filters: {
      country?: string;
      institution?: string;
      payment_status?: string;
      search?: string;
    };
    summary: {
      paid: number;
      pending: number;
      cancelled: number;
      refunded: number;
    };
    countries: string[];
  }>().props;

  const { data } = registrations;

  console.log('registrations data:', registrations);

  const [isExporting, setIsExporting] = useState(false);
  const [countryFilter, setCountryFilter] = useState(filters?.country || '');
  const [paymentStatusFilter, setPaymentStatusFilter] = useState(filters?.payment_status || '');
  const [modalOpened, setModalOpened] = useState(false);
  const [selectedRegistration, setSelectedRegistration] = useState<JoivRegistration | null>(null);

  const [globalFilterValue, setGlobalFilterValue] = useState(() => {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('search') || '';
  });

  const [searchTimeout, setSearchTimeout] = useState<number | null>(null);
  const searchInputRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    if (globalFilterValue && searchInputRef.current) {
      searchInputRef.current.focus();
      const length = globalFilterValue.length;
      searchInputRef.current.setSelectionRange(length, length);
    }
  }, [globalFilterValue]);

  useEffect(() => {
    if (globalFilterValue && searchInputRef.current) {
      setTimeout(() => {
        searchInputRef.current?.focus();
        const length = globalFilterValue.length;
        searchInputRef.current?.setSelectionRange(length, length);
      }, 100);
    }
  }, []);

  const onGlobalFilterChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const value = e.target.value;
    setGlobalFilterValue(value);

    if (searchTimeout) {
      clearTimeout(searchTimeout);
    }

    const newTimeout = window.setTimeout(() => {
      handleFilterChange(value);
    }, 500);

    setSearchTimeout(newTimeout);
  };

  const handleFilterChange = (searchValue?: string) => {
    const params = new URLSearchParams();
    if (countryFilter) params.append('country', countryFilter);
    if (paymentStatusFilter) params.append('payment_status', paymentStatusFilter);

    const search = searchValue !== undefined ? searchValue : globalFilterValue;
    if (search.trim()) params.append('search', search.trim());

    router.visit(`/joiv-articles?${params.toString()}`);
  };

  const clearFilters = () => {
    setGlobalFilterValue('');
    setCountryFilter('');
    setPaymentStatusFilter('');
    router.visit('/joiv-articles');
  };

  const handleExportExcel = () => {
    setIsExporting(true);
    const params = new URLSearchParams();
    if (countryFilter) params.append('country', countryFilter);
    if (paymentStatusFilter) params.append('payment_status', paymentStatusFilter);
    if (globalFilterValue.trim()) params.append('search', globalFilterValue.trim());

    const link = document.createElement('a');
    link.href = `/joiv-articles/export/excel?${params.toString()}`;
    link.download = '';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    setTimeout(() => setIsExporting(false), 1000);
  };

  const handleView = (registration: JoivRegistration) => {
    router.get(route('joiv-articles.show', registration.id));
  };

  const handleUpdateStatus = (registration: JoivRegistration) => {
    setSelectedRegistration(registration);
    setModalOpened(true);
  };

  const getStatusBadge = (status: string) => {
    const statusMap: Record<string, { color: string; label: string }> = {
      paid: { color: 'green', label: 'Paid' },
      pending_payment: { color: 'yellow', label: 'Pending' },
      cancelled: { color: 'red', label: 'Cancelled' },
      refunded: { color: 'gray', label: 'Refunded' },
    };

    const statusInfo = statusMap[status] || { color: 'gray', label: status };
    return <Badge color={statusInfo.color}>{statusInfo.label}</Badge>;
  };

  const renderHeader = () => {
    return (
      <Flex justify={'space-between'} direction={{ base: 'column', sm: 'row' }} gap={'md'}>
        <Flex gap={'xs'}>
          <ActionIcon
            color={'green'}
            variant="outline"
            radius={'lg'}
            size={'lg'}
            onClick={handleExportExcel}
            title="Export to Excel"
            loading={isExporting}
            disabled={isExporting}
          >
            <i className="pi pi-file-excel" />
          </ActionIcon>
        </Flex>
        <IconField iconPosition="left">
          <InputIcon className="pi pi-search" />
          <InputText
            ref={searchInputRef}
            value={globalFilterValue}
            style={{ width: '300px' }}
            onChange={onGlobalFilterChange}
            size={'small'}
            placeholder="Keyword Search"
            autoFocus={!!globalFilterValue}
          />
        </IconField>
      </Flex>
    );
  };

  const header = renderHeader();

  const handlePageChange = (event: DataTableStateEvent) => {
    if (event.page !== undefined) {
      const params = new URLSearchParams(window.location.search);
      params.set('page', (event.page + 1).toString());
      if (event.rows && event.rows !== registrations.per_page) {
        params.set('per_page', event.rows.toString());
      }
      if (globalFilterValue) {
        params.set('global', globalFilterValue);
      }

      router.visit(`/joiv-articles?${params.toString()}`);
    }
  }

  const renderSummaryCards = (summary: {
    paid: number;
    pending: number;
    cancelled: number;
    refunded: number;
  }) => {
    return (
      <Grid>
        <Grid.Col span={{ base: 12, sm: 6, md: 3 }}>
          <Card padding="lg" radius="md" withBorder>
            <Group justify="space-between">
              <div>
                <Text c="dimmed" size="sm" fw={500}>Total Paid Participants</Text>
                <Text fw={700} size="xl">{summary.paid}</Text>
              </div>
              <IconCircleCheckFilled size={24} color="green" />
            </Group>
          </Card>
        </Grid.Col>
        <Grid.Col span={{ base: 12, sm: 6, md: 3 }}>
          <Card padding="lg" radius="md" withBorder>
            <Group justify="space-between">
              <div>
                <Text c="dimmed" size="sm" fw={500}>Total Pending Participants</Text>
                <Text fw={700} size="xl">{summary.pending}</Text>
              </div>
              <IconCircleDashed size={24} color="orange" />
            </Group>
          </Card>
        </Grid.Col>
        <Grid.Col span={{ base: 12, sm: 6, md: 3 }}>
          <Card padding="lg" radius="md" withBorder>
            <Group justify="space-between">
              <div>
                <Text c="dimmed" size="sm" fw={500}>Total Cancelled Participants</Text>
                <Text fw={700} size="xl">{summary.cancelled}</Text>
              </div>
              <IconXboxXFilled size={24} color="red" />
            </Group>
          </Card>
        </Grid.Col>
        <Grid.Col span={{ base: 12, sm: 6, md: 3 }}>
          <Card padding="lg" radius="md" withBorder>
            <Group justify="space-between">
              <div>
                <Text c="dimmed" size="sm" fw={500}>Total Refunded Participants</Text>
                <Text fw={700} size="xl">{summary.refunded}</Text>
              </div>
              <IconRestore size={24} color="gray" />
            </Group>
          </Card>
        </Grid.Col>
      </Grid>
    )
  };

  const renderFilterCards = () => {
    return (
      <Card padding="lg" radius="md" withBorder>
        <Title order={4} mb="md">Filter Audience Data</Title>
        <Grid>
          <Grid.Col span={{ base: 12, md: 4 }}>
            <Select
              placeholder="Filter by Country"
              data={[
                { value: '', label: '-- All Countries --' },
                ...countries.map(c => ({ value: c, label: c }))
              ]}
              value={countryFilter}
              onChange={(value) => setCountryFilter(value || '')}
              clearable
            />
          </Grid.Col>
          <Grid.Col span={{ base: 12, md: 4 }}>
            <Select
              placeholder="Filter by Status"
              data={[
                { value: '', label: '-- All Payment Status --' },
                { value: 'paid', label: 'Paid' },
                { value: 'pending_payment', label: 'Pending' },
                { value: 'cancelled', label: 'Cancelled' },
                { value: 'refunded', label: 'Refunded' },
              ]}
              value={paymentStatusFilter}
              onChange={(value) => setPaymentStatusFilter(value || '')}
              clearable
            />
          </Grid.Col>
          <Grid.Col span={{ base: 12, md: 3 }}>
            <Button onClick={() => handleFilterChange()} mr={'sm'}>Apply Filters</Button>
            <Button variant="outline" onClick={clearFilters}>Clear Filter</Button>
          </Grid.Col>
        </Grid>
      </Card>
    )
  }

  return (
    <MainLayout title='JOIV Article Management'>
      <Container size="xl">
        <Stack gap="lg">
          <Title order={2}>JOIV Article Management</Title>
          <CurrentFee currentFee={currentFee} isShowEdit={true} />

          {renderFilterCards()}

          {renderSummaryCards(summary)}

          <Card withBorder padding="lg" radius="md">
            <Text size="sm" c="dimmed" mb="md">
              Showing {registrations.from} to {registrations.to} of {registrations.total} entries
            </Text>
            <DataTable
              value={data}
              paginator
              stripedRows
              onPage={handlePageChange}
              rows={registrations.per_page}
              totalRecords={registrations.total}
              lazy
              resizableColumns
              showGridlines
              alwaysShowPaginator
              first={(registrations.current_page - 1) * registrations.per_page}
              header={header}
              emptyMessage="No registrations found."
              rowsPerPageOptions={[15, 25, 50, 100]}
              paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
              currentPageReportTemplate="Showing {first} to {last} of {totalRecords} entries"
            >
              <Column
                header="No"
                body={(_, { rowIndex }) => (registrations.current_page - 1) * registrations.per_page + rowIndex + 1}
                style={{ width: '5rem' }}
              />
              <Column field="first_name" header="First Name" sortable />
              <Column field="last_name" header="Last Name" sortable />
              <Column field="email_address" header="Email" sortable />
              <Column field="institution" header="Institution" sortable />
              <Column field="country" header="Country" style={{ width: '8rem' }} />
              <Column
                field="paper_id"
                header="Paper ID"
                body={(row) => (
                  <Stack w={250}>
                    <Text size='sm' style={{ textWrap: 'wrap' }}>{row.paper_title}</Text>
                    {row.full_paper_path && (
                      <Button
                        color="blue"
                        size="xs"
                        variant="light"
                        leftSection={<i className="pi pi-download" />}
                        onClick={() => window.open(`/storage/${row.full_paper_path}`, '_blank')}
                      >
                        Download Paper
                      </Button>
                    )}
                  </Stack>
                )}
              />
              <Column
                field='payment_method'
                header="Payment Method"
                body={(row) => {
                  const isTransferWithProof = row.payment_method === 'transfer_bank' && row.payment_proof_path;

                  return (
                    <Stack>
                      <Text size='sm'>{PAYMENT_METHOD[row.payment_method as keyof typeof PAYMENT_METHOD]}</Text>
                      {isTransferWithProof && (
                        <Button
                          color="blue"
                          size="xs"
                          variant="light"
                          leftSection={<i className="pi pi-download" />}
                          onClick={() => window.open(`/storage/${row.payment_proof_path}`, '_blank')}
                        >
                          Download Proof
                        </Button>
                      )}
                    </Stack>
                  );
                }}
              />
              <Column
                field="payment_status"
                header="Payment Status"
                body={(row) => getStatusBadge(row.payment_status)}
              />
              <Column
                field="paid_fee"
                header="Paid Fee"
                body={(row) => formatCurrency(row.paid_fee, row.country === 'ID' ? 'idr' : 'usd')}
              />
              <Column
                header="Actions"
                body={(row: JoivRegistration) => (
                  <Flex gap="xs">
                    <ActionButtonExt
                      color="green"
                      handleClick={() => handleView(row)}
                      icon="pi pi-fw pi-eye"
                    />
                    {row.payment_method === 'transfer_bank' && (
                      <ActionButtonExt
                        color="blue"
                        handleClick={() => handleUpdateStatus(row)}
                        icon="pi pi-fw pi-credit-card"
                      />
                    )}
                  </Flex>
                )}
                style={{ width: '12rem' }}
              />
            </DataTable>
          </Card>
        </Stack>
      </Container>

      <JoivPaymentStatusModal
        opened={modalOpened}
        onClose={() => {
          setModalOpened(false);
          setSelectedRegistration(null);
        }}
        registration={selectedRegistration}
      />
    </MainLayout>
  );
}

export default JoivArticleIndex;
