import { router, usePage } from '@inertiajs/react';
import { ActionIcon, Card, Container, Divider, Flex, Group, Stack, Text, Title } from '@mantine/core';
import { Column } from 'primereact/column';
import { DataTable, DataTableStateEvent } from 'primereact/datatable';
import { IconField } from 'primereact/iconfield';
import { InputIcon } from 'primereact/inputicon';
import { InputText } from 'primereact/inputtext';
import React, { useEffect, useRef, useState } from 'react';
import { PaymentStatusModal } from '../../../Components/Modals/PaymentStatusModal';
import MainLayout from '../../../Layout/MainLayout';
import SummaryModule from '../../../Modules/SummaryModule';
import { Audiences, PaginatedData } from '../../../types';
import { FilterData } from './FilterData';
import { TableData } from './TableData';

function AudienceIndex() {
  const { audiences, filters, summary, conferences } = usePage<{
    audiences: PaginatedData<Audiences>;
    filters: {
      conference_id?: string;
      payment_method?: string;
      payment_status?: string;
    };
    summary: {
      paid: number;
      pending: number;
      cancelled: number;
      refunded: number;
    };
    conferences: Array<{ id: number; name: string }>;
  }>().props;

  const { data, meta } = audiences;

  const [paymentModalOpened, setPaymentModalOpened] = useState(false);
  const [selectedAudience, setSelectedAudience] = useState<Audiences | null>(null);
  const [isExporting, setIsExporting] = useState(false);

  // Filter states
  const [conferenceFilter, setConferenceFilter] = useState(filters?.conference_id || '');
  const [paymentMethodFilter, setPaymentMethodFilter] = useState(filters?.payment_method || '');
  const [paymentStatusFilter, setPaymentStatusFilter] = useState(filters?.payment_status || '');

  // Initialize search value from URL params
  const [globalFilterValue, setGlobalFilterValue] = useState(() => {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('search') || '';
  });

  const searchInputRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    if (globalFilterValue && searchInputRef.current) {
      // Keep focus on search input after search
      searchInputRef.current.focus();
      // Maintain cursor at the end of the text
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

  const handleExportExcel = () => {
    setIsExporting(true);
    const params = new URLSearchParams();
    if (conferenceFilter) params.append('conference_id', conferenceFilter);
    if (paymentMethodFilter) params.append('payment_method', paymentMethodFilter);
    if (paymentStatusFilter) params.append('payment_status', paymentStatusFilter);
    if (globalFilterValue.trim()) params.append('search', globalFilterValue.trim());

    // Create a temporary link to trigger download
    const link = document.createElement('a');
    link.href = `/audiences/export?${params.toString()}`;
    link.download = '';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    // Reset loading state after a short delay
    setTimeout(() => setIsExporting(false), 1000);
  };

  const handleFilterChange = () => {
    const params = new URLSearchParams();
    if (conferenceFilter) params.append('conference_id', conferenceFilter);
    if (paymentMethodFilter) params.append('payment_method', paymentMethodFilter);
    if (paymentStatusFilter) params.append('payment_status', paymentStatusFilter);
    if (globalFilterValue.trim()) params.append('search', globalFilterValue.trim());

    router.visit(`/audiences?${params.toString()}`);
  };

  const clearFilters = () => {
    setGlobalFilterValue('');
    router.visit('/audiences');
  };

  const handlePaymentStatusClick = (audience: Audiences) => {
    setPaymentModalOpened(true);
    setSelectedAudience(audience);
  };

  const _handleRedirectWa = (row: Audiences) => {
    window.open(`https://wa.me/${row.phone_number}`, '_blank');
  }

  const [searchTimeout, setSearchTimeout] = useState<number | null>(null);

  const onGlobalFilterChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const value = e.target.value;
    setGlobalFilterValue(value);

    // Debounce search to avoid too many requests
    if (searchTimeout) {
      clearTimeout(searchTimeout);
    }

    const timeout = setTimeout(() => {
      handleSearch(value);
    }, 500);

    setSearchTimeout(timeout);
  };

  const handleSearch = (searchValue: string) => {
    const params = new URLSearchParams();
    if (conferenceFilter) params.append('conference_id', conferenceFilter);
    if (paymentMethodFilter) params.append('payment_method', paymentMethodFilter);
    if (paymentStatusFilter) params.append('payment_status', paymentStatusFilter);
    if (searchValue.trim()) params.append('search', searchValue.trim());

    router.visit(`/audiences?${params.toString()}`);
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

  const handlePagination = (event: DataTableStateEvent) => {
    if (event.page !== undefined) {
      const params = new URLSearchParams(window.location.search);
      params.set('page', (event.page + 1).toString());
      if (event.rows && event.rows !== meta.per_page) {
        params.set('per_page', event.rows.toString());
      }
      if (conferenceFilter) params.set('conference_id', conferenceFilter);
      if (paymentMethodFilter) params.set('payment_method', paymentMethodFilter);
      if (paymentStatusFilter) params.set('payment_status', paymentStatusFilter);
      if (globalFilterValue.trim()) params.set('search', globalFilterValue.trim());

      router.visit(`/audiences?${params.toString()}`);
    }
  }

  return (
    <Container fluid>
      <Stack gap="lg">
        {/* Header */}
        <Group justify="space-between">
          <div>
            <Title order={2}>Audience Management</Title>
            <Text c="dimmed">Manage audiences, settings, and configurations</Text>
          </div>
        </Group>
        <FilterData
          conferences={conferences}
          conferenceFilter={conferenceFilter}
          setConferenceFilter={setConferenceFilter}
          paymentMethodFilter={paymentMethodFilter}
          setPaymentMethodFilter={setPaymentMethodFilter}
          paymentStatusFilter={paymentStatusFilter}
          setPaymentStatusFilter={setPaymentStatusFilter}
          handleFilterChange={handleFilterChange}
          clearFilters={clearFilters}
        />
        {/* Summary Payment Status */}
        <SummaryModule data={summary} />
        <Divider />
        <Card mb="lg" padding="lg" radius="md" withBorder>
          {/* Pagination Info */}
          <Text size="sm" c="dimmed" mb="md">
            Showing {meta.from} to {meta.to} of {meta.total} entries
          </Text>
          <DataTable
            value={data}
            header={renderHeader()}
            globalFilter={globalFilterValue}
            stripedRows
            showGridlines
            paginator
            lazy
            first={(meta.current_page - 1) * meta.per_page}
            onPage={handlePagination}
            className="datatable-responsive"
            tableStyle={{ minWidth: '100rem', fontSize: '14px' }}
            rows={meta.per_page}
            totalRecords={meta.total}
            rowsPerPageOptions={[15, 25, 50, 100]}
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
            currentPageReportTemplate="Showing {first} to {last} of {totalRecords} entries"
          >
            {TableData({ _handleRedirectWa, handlePaymentStatusClick }).map(col => (
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
          <PaymentStatusModal
            opened={paymentModalOpened}
            onClose={() => setPaymentModalOpened(false)}
            audience={selectedAudience}
          />
        </Card>
      </Stack>
    </Container>
  );
}

AudienceIndex.layout = (page: React.ReactNode) => <MainLayout title="Audience Management">{page}</MainLayout>;

export default AudienceIndex;
