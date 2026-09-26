import React, { useEffect, useRef, useState } from 'react';
import { Head, router, usePage } from '@inertiajs/react';
import {
  Container,
  Card,
  Button,
  Title,
  Text,
  Stack,
  Group,
  Modal,
  ActionIcon,
  Flex,
  Divider,
} from '@mantine/core';
import { Column } from 'primereact/column';
import { DataTable, DataTableStateEvent } from 'primereact/datatable';
import { IconField } from 'primereact/iconfield';
import { InputIcon } from 'primereact/inputicon';
import { InputText } from 'primereact/inputtext';
import { IconCheck, IconX } from '@tabler/icons-react';
import MainLayout from '../../../Layout/MainLayout';
import { PaginatedData, Membership, PageProps } from '../../../types';
import { FilterData } from './FilterData';
import { TableData } from './TableData';

interface MembershipPageProps extends PageProps {
  memberships: PaginatedData<Membership>;
  filters: {
    status?: string;
    search?: string;
  };
}

export default function MembershipIndex() {
  const { memberships, filters } = usePage<MembershipPageProps>().props;
  const data = memberships?.data ?? [];
  const pagination = memberships?.meta ?? memberships;

  const [selectedMembership, setSelectedMembership] = useState<Membership | null>(null);
  const [verifyModalOpened, setVerifyModalOpened] = useState(false);
  const [globalFilterValue, setGlobalFilterValue] = useState(() => {
    const urlParams = new URLSearchParams(globalThis.location.search);
    return urlParams.get('search') || '';
  });
  const [statusFilter, setStatusFilter] = useState(filters?.status || '');
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

    const newTimeout = globalThis.setTimeout(() => {
      handleSearch(value);
    }, 500);

    setSearchTimeout(newTimeout);
  };

  const handleSearch = (searchValue: string) => {
    const params = new URLSearchParams();
    if (statusFilter) params.append('status', statusFilter);
    if (searchValue.trim()) params.append('search', searchValue.trim());

    router.visit(`/memberships?${params.toString()}`);
  };

  const handleFilterChange = () => {
    const params = new URLSearchParams();
    if (statusFilter) params.append('status', statusFilter);
    if (globalFilterValue.trim()) params.append('search', globalFilterValue.trim());

    router.visit(`/memberships?${params.toString()}`);
  };

  const clearFilters = () => {
    setGlobalFilterValue('');
    setStatusFilter('');
    router.visit('/memberships');
  };

  const handleOpenVerification = (membership: Membership) => {
    setSelectedMembership(membership);
    setVerifyModalOpened(true);
  };

  const handleAcceptPayment = (membership: Membership) => {
    const latestInvoice = membership.invoices?.[membership.invoices.length - 1];
    if (latestInvoice?.payment_method !== 'transfer_bank' || latestInvoice?.status !== 'pending') {
      return;
    }

    if (confirm('Approve this bank transfer payment?')) {
      router.patch(
        `/memberships/${membership.id}/payment-status/${latestInvoice.id}`,
        { status: 'completed' }
      );
    }
  };

  const handleUpdatePaymentStatus = (status: 'completed' | 'failed') => {
    if (!selectedMembership?.invoices?.length) {
      return;
    }

    const latestInvoice = selectedMembership.invoices[selectedMembership.invoices.length - 1];
    if (confirm(`Are you sure you want to mark this payment as ${status}?`)) {
      router.patch(
        `/memberships/${selectedMembership.id}/payment-status/${latestInvoice.id}`,
        { status },
        { onSuccess: () => setVerifyModalOpened(false) }
      );
    }
  };

  const renderHeader = () => {
    return (
      <Flex justify={'flex-end'} direction={{ base: 'column', sm: 'row' }} gap={'md'}>
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

  const handlePagination = (event: DataTableStateEvent) => {
    if (event.page !== undefined && pagination) {
      const params = new URLSearchParams(globalThis.location.search);
      params.set('page', (event.page + 1).toString());
      if (event.rows && event.rows !== pagination.per_page) {
        params.set('per_page', event.rows.toString());
      }
      if (statusFilter) params.set('status', statusFilter);
      if (globalFilterValue.trim()) params.set('search', globalFilterValue.trim());

      router.visit(`/memberships?${params.toString()}`);
    }
  };

  return (
    <Container fluid>
      <Head title="Membership Management" />
      <Stack gap="lg">
        {/* Header */}
        <Group justify="space-between">
          <div>
            <Title order={2}>Membership Management</Title>
            <Text c="dimmed">Manage community memberships and verify payments</Text>
          </div>
        </Group>

        {/* Filter */}
        <FilterData
          statusFilter={statusFilter}
          setStatusFilter={setStatusFilter}
          handleFilterChange={handleFilterChange}
          clearFilters={clearFilters}
        />

        <Divider />

        {/* Data Table */}
        <Card mb="lg" padding="lg" radius="md" withBorder>
          <Text size="sm" c="dimmed" mb="md">
            {/* Showing {meta.from} to {meta.to} of {meta.total} entries */}
          </Text>
          <DataTable
            value={data}
            header={renderHeader()}
            globalFilter={globalFilterValue}
            stripedRows
            showGridlines
            paginator
            lazy
            first={((pagination?.current_page ?? 1) - 1) * (pagination?.per_page ?? 15)}
            onPage={handlePagination}
            className="datatable-responsive"
            tableStyle={{ minWidth: '100rem', fontSize: '14px' }}
            rows={pagination?.per_page ?? 15}
            totalRecords={pagination?.total ?? data.length}
            rowsPerPageOptions={[15, 25, 50, 100]}
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
            currentPageReportTemplate="Showing {first} to {last} of {totalRecords} entries"
          >
            {TableData({ handleOpenVerification, handleAcceptPayment }).map((col) => (
              <Column
                key={col.name}
                field={col.name}
                body={col.renderCell}
                header={col.label}
                sortable={col.sortable}
                style={{
                  alignItems: 'top',
                  textAlign: 'left',
                  textWrap: 'nowrap',
                  width: '250px',
                }}
              />
            ))}
          </DataTable>
        </Card>
      </Stack>

      {/* Verification Modal */}
      <Modal
        opened={verifyModalOpened}
        onClose={() => setVerifyModalOpened(false)}
        title={<Title order={4}>Verify Payment Proof</Title>}
        size="lg"
        centered
      >
        {selectedMembership && (
          <Stack>
            {(() => {
              const latestInvoice = selectedMembership.invoices?.[selectedMembership.invoices.length - 1];
              const proofPath = latestInvoice?.payment_proof_path ? `/storage/${latestInvoice.payment_proof_path}` : null;
              const isImageProof = proofPath ? /\.(png|jpe?g|gif|webp|bmp|svg)$/i.test(proofPath) : false;

              if (!proofPath) {
                return <Text c="dimmed" ta="center">No payment proof uploaded</Text>;
              }

              return isImageProof ? (
                <img
                  src={proofPath}
                  alt="Payment Proof"
                  style={{ maxWidth: '100%', maxHeight: '400px', objectFit: 'contain' }}
                />
              ) : (
                <Button
                  component="a"
                  href={proofPath}
                  target="_blank"
                  rel="noreferrer"
                >
                  Open Payment Proof File
                </Button>
              );
            })()}
            {selectedMembership.invoices?.[selectedMembership.invoices.length - 1]?.payment_method === 'transfer_bank' &&
              selectedMembership.invoices?.[selectedMembership.invoices.length - 1]?.status === 'pending' && (
                <Group justify="flex-end" mt="md">
                  <ActionIcon
                    color="red"
                    variant="outline"
                    onClick={() => handleUpdatePaymentStatus('failed')}
                  >
                    <IconX size={16} />
                  </ActionIcon>
                  <ActionIcon
                    color="green"
                    onClick={() => handleUpdatePaymentStatus('completed')}
                  >
                    <IconCheck size={16} />
                  </ActionIcon>
                </Group>
              )}
          </Stack>
        )}
      </Modal>
    </Container>
  );
}

MembershipIndex.layout = (page: React.ReactNode) => <MainLayout title="Memberships">{page}</MainLayout>;
