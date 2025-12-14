import { router, usePage } from '@inertiajs/react';
import { ActionIcon, Card, Container, Flex, Stack, Text, Title } from '@mantine/core';
import { Column } from 'primereact/column';
import { DataTable, DataTableStateEvent } from 'primereact/datatable';
import { IconField } from 'primereact/iconfield';
import { InputIcon } from 'primereact/inputicon';
import { InputText } from 'primereact/inputtext';
import React, { useEffect, useRef, useState } from 'react';
import { route } from 'ziggy-js';
import { JoivPaymentStatusModal } from '../../../Components/Modals/JoivPaymentStatusModal';
import MainLayout from '../../../Layout/MainLayout';
import SummaryModule from '../../../Modules/SummaryModule';
import { JoivRegistrationFee, PaginatedData } from '../../../types';
import CurrentFee from './CurrentFee';
import { FilterData } from './FilterData';
import { TableData } from './TableData';

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
  loa_authors: string | null;
  loa_volume_id: number | null;
  loa_approved_at: string | null;
  loa_volume?: {
    id: number;
    volume: string;
  };
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

  return (
    <MainLayout title='JOIV Article Management'>
      <Container size="xl">
        <Stack gap="lg">
          <Title order={2}>JOIV Article Management</Title>
          <CurrentFee currentFee={currentFee} isShowEdit={true} />

          <FilterData
            countries={countries}
            countryFilter={countryFilter}
            setCountryFilter={setCountryFilter}
            paymentStatusFilter={paymentStatusFilter}
            setPaymentStatusFilter={setPaymentStatusFilter}
            handleFilterChange={handleFilterChange}
            clearFilters={clearFilters}
          />
          <SummaryModule data={summary} />
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
              {TableData({ handleUpdateStatus, handleView }).map((col) => (
                <Column
                  key={col.label}
                  field={col.name}
                  header={col.label}
                  body={col.renderCell}
                  sortable={col.sortable}
                />
              ))}
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
