import { DataTable } from 'primereact/datatable';
import MainLayout from '../../../Layout/MainLayout';
import { Column } from 'primereact/column';
import { usePage } from '@inertiajs/react';
import React, { useState } from 'react';
import { InputText } from 'primereact/inputtext';
import { Audiences, PaginatedData } from '../../../types';
import { formatCurrency } from '../../../utils';
import { ActionIcon, Button, Checkbox, Flex, Stack, Text, Select, Box, Group, Badge, Grid, Divider } from '@mantine/core';
import { IconField } from 'primereact/iconfield';
import { InputIcon } from 'primereact/inputicon';
import { BadgeStatus } from './ExtendComponent';
import { PAYMENT_METHOD, PRESENTATION_TYPE } from '../../../Constants';
import { PaymentStatusModal } from '../../../Components/Modals/PaymentStatusModal';
import { route } from 'ziggy-js';

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

  const { data } = audiences;

  console.log('Audiences data:', data);
  const [paymentModalOpened, setPaymentModalOpened] = useState(false);
  const [selectedAudience] = useState<Audiences | null>(null);
  const [isExporting, setIsExporting] = useState(false);

  // Filter states
  const [conferenceFilter, setConferenceFilter] = useState(filters?.conference_id || '');
  const [paymentMethodFilter, setPaymentMethodFilter] = useState(filters?.payment_method || '');
  const [paymentStatusFilter, setPaymentStatusFilter] = useState(filters?.payment_status || '');

  const handleExportExcel = () => {
    setIsExporting(true);
    const params = new URLSearchParams();
    if (conferenceFilter) params.append('conference_id', conferenceFilter);
    if (paymentMethodFilter) params.append('payment_method', paymentMethodFilter);
    if (paymentStatusFilter) params.append('payment_status', paymentStatusFilter);

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

    window.location.href = `/audiences?${params.toString()}`;
  };

  const clearFilters = () => {
    window.location.href = '/audiences';
  };

  const columns = [
    {
      field: 'serial_number',
      label: 'No.',
      style: { minWidth: '5rem' },
      sortable: false,
      renderCell: (_: Audiences, { rowIndex }: { rowIndex: number }) => rowIndex + 1
    },
    {
      label: 'Conference',
      name: 'conference.name',
      className: 'text-wrap w-40',
      renderCell: (row: Audiences) => (
        <Text size='sm' style={{ textWrap: 'wrap' }}>{row.conference?.name}</Text>
      ),
      sortable: true,
    },
    {
      label: 'First Name',
      name: 'first_name',
    },
    {
      label: 'Last Name',
      name: 'last_name',
    },
    {
      label: 'Email',
      name: 'email',
      renderCell: (row: Audiences) => (
        <Text fz={'sm'} style={{ whiteSpace: 'nowrap' }}>
          {row.email}
        </Text>
      ),
    },
    {
      label: 'Participant Type',
      name: 'presentation_type',
      renderCell: (row: Audiences) => PRESENTATION_TYPE[row.presentation_type as keyof typeof PRESENTATION_TYPE],
    },
    {
      label: 'Payment Method',
      name: 'payment_method',
      renderCell: (row: Audiences) => {
        const isTransferWithProof = row.payment_method === 'transfer_bank' && row.payment_proof_path;

        return (
          <Stack>
            <span>{PAYMENT_METHOD[row.payment_method as keyof typeof PAYMENT_METHOD]}</span>
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
      },
    },
    {
      label: 'Amount Paid',
      name: 'paid_fee',
      renderCell: (row: Audiences) => (
        <Text fz={'sm'} style={{ whiteSpace: 'nowrap' }}>
          {row.country === 'ID'
            ? formatCurrency(row.paid_fee, 'idr')
            : formatCurrency(row.paid_fee, 'usd')}
        </Text>
      ),
    },
    {
      label: 'Payment Status',
      name: 'payment_status',
      renderCell: (row: Audiences) => (
        <Stack>
          <BadgeStatus status={row.payment_status} />
          {row.payment_status === 'paid' && (
            <Button
              component="a"
              size="xs"
              variant="light"
              leftSection={<i className="pi pi-download" />}
              href={route('audiences.receipt', row.id)}
              target="_blank"
            >
              Download Receipt
            </Button>
          )}
        </Stack>
      ),
    },
    {
      label: 'Paper',
      name: 'paper_title',
      renderCell: (row: Audiences) => (
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
      ),
    },
    {
      label: 'Keynote',
      name: 'key_notes',
      renderCell: (row: Audiences) => <Checkbox checked={row.key_notes.length > 0} />,
    },
    {
      label: 'Parallel Session',
      name: 'parallel_sessions',
      renderCell: (row: Audiences) => <Checkbox checked={row.parallel_sessions.length > 0} />,
    },
    {
      label: 'Certificate',
      name: 'certificate',
      renderCell: (row: Audiences) => {
        const hasTemplate = row.conference?.certificate_template_path && row.conference?.certificate_template_position;
        const hasSubmissions = row.key_notes.length > 0 && row.parallel_sessions.length > 0;
        const canDownload = hasTemplate && hasSubmissions;

        if (canDownload) {
          return (
            <Button
              color="green"
              size="xs"
              component="a"
              href={route('audiences.download', row.id)}
              target="_blank"
              variant="light"
              leftSection={<i className="pi pi-download" />}
            >
              Download {row.key_notes.length}
            </Button>
          );
        }

        let message = 'Certificate Not Available';
        if (!hasTemplate) message = 'Template Not Set';
        else if (!hasSubmissions) message = 'No Keynote/Parallel Session';

        return (
          <Text fz={'xs'} c="dimmed">
            {message}
          </Text>
        );
      },
    },
    // {
    //   label: 'Action',
    //   name: 'action',
    //   renderCell: (row: Audiences) => (
    //     <Stack gap={'xs'} justify="center" align="center">
    //       <ActionButtonExt
    //         color="green"
    //         handleClick={() => (window.location.href = `/audiences/${row.id}/show`)}
    //         icon="pi pi-fw pi-eye"
    //       />
    //       {row.payment_method === 'transfer_bank' && (
    //         <ActionButtonExt
    //           color="blue"
    //           handleClick={() => handlePaymentStatusClick(row)}
    //           icon="pi pi-fw pi-credit-card"
    //         />
    //       )}
    //     </Stack>
    //   ),
    // },
  ];

  const [globalFilterValue, setGlobalFilterValue] = useState('');

  const onGlobalFilterChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const value = e.target.value;
    setGlobalFilterValue(value);
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
            value={globalFilterValue}
            style={{ width: '300px' }}
            onChange={onGlobalFilterChange}
            size={'small'}
            placeholder="Keyword Search"
          />
        </IconField>
      </Flex>
    );
  };

  return (
    <div style={{ padding: '24px', backgroundColor: 'white', borderRadius: '8px', border: '1px solid #e9ecef' }}>
      {/* Filter Section */}
      <Box mb="lg">
        <Text size="lg" fw={600} mb="md">Filter Audience Data</Text>
        <Grid>
          <Grid.Col span={{ base: 12, md: 3 }}>
            <Select
              placeholder="-- All Conferences --"
              data={[
                { value: '', label: '-- All Conferences --' },
                ...conferences.map(conf => ({ value: conf.id.toString(), label: conf.name }))
              ]}
              value={conferenceFilter}
              onChange={(value) => setConferenceFilter(value || '')}
              style={{ minWidth: 200 }}
            />
          </Grid.Col>

          <Grid.Col span={{ base: 12, md: 3 }}>
            <Select
              placeholder="-- All Payment Methods --"
              data={[
                { value: '', label: '-- All Payment Methods --' },
                { value: 'paypal', label: 'PayPal' },
                { value: 'transfer_bank', label: 'Bank Transfer' }
              ]}
              value={paymentMethodFilter}
              onChange={(value) => setPaymentMethodFilter(value || '')}
              style={{ minWidth: 200 }}
            />
          </Grid.Col>

          <Grid.Col span={{ base: 12, md: 3 }}>
            <Select
              placeholder="-- All Payment Status --"
              data={[
                { value: '', label: '-- All Payment Status --' },
                { value: 'paid', label: 'Paid' },
                { value: 'pending_payment', label: 'Pending' },
                { value: 'cancelled', label: 'Cancelled' },
                { value: 'refunded', label: 'Refunded' }
              ]}
              value={paymentStatusFilter}
              onChange={(value) => setPaymentStatusFilter(value || '')}
              style={{ minWidth: 200 }}
            />
          </Grid.Col>

          <Grid.Col span={{ base: 12, md: 3 }}>
            <Button onClick={handleFilterChange} variant="filled" mr={'sm'}>
              Apply Filter
            </Button>
            <Button onClick={clearFilters} variant="outline" mr={'sm'}>
              Clear Filter
            </Button>
          </Grid.Col>
        </Grid>

        <Group gap="sm" mt="md">
          <Badge size="lg" color="green" variant="filled">
            Paid: {summary.paid}
          </Badge>
          <Badge size="lg" color="yellow" variant="filled">
            Pending: {summary.pending}
          </Badge>
          <Badge size="lg" color="red" variant="filled">
            Cancelled/Refunded: {summary.cancelled + summary.refunded}
          </Badge>
          <Badge size="lg" color="gray" variant="filled">
            Refunded: {summary.refunded}
          </Badge>
        </Group>
      </Box>
      <Divider mb="lg" />
      <DataTable
        value={data}
        paginator
        rows={10}
        header={renderHeader()}
        globalFilter={globalFilterValue}
        stripedRows
        showGridlines
        className="datatable-responsive"
        tableStyle={{ minWidth: '100rem', fontSize: '14px' }}
      >
        {columns.map(col => (
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
    </div>
  );
}

AudienceIndex.layout = (page: React.ReactNode) => <MainLayout title="Participants">{page}</MainLayout>;

export default AudienceIndex;
