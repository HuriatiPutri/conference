import { formatCurrency } from '../../../utils';
import { usePage } from '@inertiajs/react';
import { Button, Card, Container, Grid, Group, Text } from '@mantine/core';
import { IconDownload, IconFilter } from '@tabler/icons-react';
import { Column } from 'primereact/column';
import { DataTable } from 'primereact/datatable';
import React, { useState } from 'react';
import MainLayout from '../../../Layout/MainLayout';

interface VoucherClaim {
  id: number;
  voucher_id: number;
  email: string;
  transaction_type: string;
  created_at: string;
  voucher?: {
    code: string;
    discount_type: string;
    discount_value: number;
  };
}

interface Filters {
  start_date?: string;
  end_date?: string;
  transaction_type?: string;
  voucher_code?: string;
  email?: string;
}

interface Summary {
  total_claims: number;
  total_discount_amount: number;
  claims_this_month: number;
}

interface ReportProps {
  claims: {
    data: VoucherClaim[];
    current_page: number;
    total: number;
    per_page: number;
  };
  filters: Filters;
  summary: Summary;
}

export default function Report() {
  const { claims, filters, summary } = usePage().props as unknown as ReportProps;
  const [filterValues, setFilterValues] = useState(filters);
  const [loading, setLoading] = useState(false);

  const transactionTypeMap: Record<string, string> = {
    conference_registration: 'Conference Registration',
    joiv_article: 'JOIV Article',
    membership_registration: 'Membership Registration',
  };

  const handleFilter = (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    // The form will be submitted via standard form submission
  };

  const handleReset = () => {
    setFilterValues({});
    globalThis.location.href = route('vouchers.report');
  };

  const transactionTypeBodyTemplate = (rowData: VoucherClaim) => {
    return transactionTypeMap[rowData.transaction_type] || rowData.transaction_type;
  };

  const discountBodyTemplate = (rowData: VoucherClaim) => {
    if (!rowData.voucher) return '-';
    if (rowData.voucher.discount_type === 'percent') {
      return `${rowData.voucher.discount_value}%`;
    }
    return formatCurrency(rowData.voucher.discount_value, 'USD');
  };

  const formatDiscount = (claim: VoucherClaim): string => {
    if (!claim.voucher) return '-';
    if (claim.voucher.discount_type === 'percent') {
      return `${claim.voucher.discount_value}%`;
    }
    return formatCurrency(claim.voucher.discount_value, 'USD');
  };

  const dateBodyTemplate = (rowData: VoucherClaim) => {
    return new Date(rowData.created_at).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  };

  const handleExportCSV = () => {
    const headers = ['Code', 'Email', 'Type', 'Discount', 'Claimed At'];
    const rows = claims.data.map((claim) => [
      claim.voucher?.code || '-',
      claim.email,
      transactionTypeMap[claim.transaction_type] || claim.transaction_type,
      formatDiscount(claim),
      new Date(claim.created_at).toLocaleDateString(),
    ]);

    const csvContent = [headers, ...rows].map((row) => row.map((cell) => `"${cell}"`).join(',')).join('\n');

    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = globalThis.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `voucher_claims_${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
    globalThis.URL.revokeObjectURL(url);
  };

  return (
    <MainLayout title="Voucher Claims Report">
      <Container size="xl" py="xl">
        <Text size="xl" fw={600} mb="lg">
          Voucher Claims Report
        </Text>

        {/* Summary Cards */}
        <Grid gutter="md" mb="xl">
          <Grid.Col span={{ base: 12, sm: 6, md: 4 }}>
            <Card shadow="sm" padding="lg" radius="md" withBorder>
              <Group justify="space-between" mb="xs">
                <Text size="sm" c="dimmed">
                  Total Claims
                </Text>
              </Group>
              <Text size="xl" fw={700}>
                {summary.total_claims}
              </Text>
            </Card>
          </Grid.Col>
          <Grid.Col span={{ base: 12, sm: 6, md: 4 }}>
            <Card shadow="sm" padding="lg" radius="md" withBorder>
              <Group justify="space-between" mb="xs">
                <Text size="sm" c="dimmed">
                  Claims This Month
                </Text>
              </Group>
              <Text size="xl" fw={700}>
                {summary.claims_this_month}
              </Text>
            </Card>
          </Grid.Col>
          <Grid.Col span={{ base: 12, sm: 6, md: 4 }}>
            <Card shadow="sm" padding="lg" radius="md" withBorder>
              <Group justify="space-between" mb="xs">
                <Text size="sm" c="dimmed">
                  Total Discount (Fixed Only)
                </Text>
              </Group>
              <Text size="xl" fw={700}>
                {formatCurrency(summary.total_discount_amount, 'USD')}
              </Text>
            </Card>
          </Grid.Col>
        </Grid>

        {/* Filter Card */}
        <Card shadow="sm" padding="lg" radius="md" withBorder mb="lg">
          <form onSubmit={handleFilter}>
            <Grid gutter="md" mb="md">
              <Grid.Col span={{ base: 12, sm: 6, md: 3 }}>
                <label htmlFor="start_date" style={{ display: 'block', marginBottom: 4, fontSize: 14 }}>
                  Start Date
                </label>
                <input
                  type="date"
                  id="start_date"
                  name="start_date"
                  defaultValue={filterValues.start_date || ''}
                  onChange={(e) => setFilterValues({ ...filterValues, start_date: e.target.value })}
                  style={{
                    width: '100%',
                    padding: '8px',
                    borderRadius: '4px',
                    border: '1px solid #ddd',
                  }}
                />
              </Grid.Col>
              <Grid.Col span={{ base: 12, sm: 6, md: 3 }}>
                <label htmlFor="end_date" style={{ display: 'block', marginBottom: 4, fontSize: 14 }}>
                  End Date
                </label>
                <input
                  type="date"
                  id="end_date"
                  name="end_date"
                  defaultValue={filterValues.end_date || ''}
                  onChange={(e) => setFilterValues({ ...filterValues, end_date: e.target.value })}
                  style={{
                    width: '100%',
                    padding: '8px',
                    borderRadius: '4px',
                    border: '1px solid #ddd',
                  }}
                />
              </Grid.Col>
              <Grid.Col span={{ base: 12, sm: 6, md: 3 }}>
                <label htmlFor="transaction_type" style={{ display: 'block', marginBottom: 4, fontSize: 14 }}>
                  Transaction Type
                </label>
                <select
                  id="transaction_type"
                  name="transaction_type"
                  defaultValue={filterValues.transaction_type || ''}
                  onChange={(e) => setFilterValues({ ...filterValues, transaction_type: e.target.value })}
                  style={{
                    width: '100%',
                    padding: '8px',
                    borderRadius: '4px',
                    border: '1px solid #ddd',
                  }}
                >
                  <option value="">All Types</option>
                  <option value="conference_registration">Conference Registration</option>
                  <option value="joiv_article">JOIV Article</option>
                  <option value="membership_registration">Membership Registration</option>
                </select>
              </Grid.Col>
              <Grid.Col span={{ base: 12, sm: 6, md: 3 }}>
                <label htmlFor="voucher_code" style={{ display: 'block', marginBottom: 4, fontSize: 14 }}>
                  Voucher Code
                </label>
                <input
                  type="text"
                  id="voucher_code"
                  name="voucher_code"
                  placeholder="Search code"
                  defaultValue={filterValues.voucher_code || ''}
                  onChange={(e) => setFilterValues({ ...filterValues, voucher_code: e.target.value })}
                  style={{
                    width: '100%',
                    padding: '8px',
                    borderRadius: '4px',
                    border: '1px solid #ddd',
                  }}
                />
              </Grid.Col>
              <Grid.Col span={{ base: 12 }}>
                <label htmlFor="email" style={{ display: 'block', marginBottom: 4, fontSize: 14 }}>
                  Email
                </label>
                <input
                  type="text"
                  id="email"
                  name="email"
                  placeholder="Search email"
                  defaultValue={filterValues.email || ''}
                  onChange={(e) => setFilterValues({ ...filterValues, email: e.target.value })}
                  style={{
                    width: '100%',
                    padding: '8px',
                    borderRadius: '4px',
                    border: '1px solid #ddd',
                  }}
                />
              </Grid.Col>
            </Grid>
            <Group justify="flex-end" gap="md">
              <Button variant="light" onClick={handleReset}>
                Reset
              </Button>
              <Button type="submit" loading={loading}>
                <IconFilter size={16} style={{ marginRight: 4 }} />
                Apply Filters
              </Button>
            </Group>
          </form>
        </Card>

        {/* Data Table */}
        <Card shadow="sm" padding="lg" radius="md" withBorder>
          <Group justify="space-between" mb="md">
            <Text fw={600}>Claims Data</Text>
            <Button size="sm" variant="light" onClick={handleExportCSV}>
              <IconDownload size={16} style={{ marginRight: 4 }} />
              Export CSV
            </Button>
          </Group>
          <DataTable
            value={claims.data}
            paginator
            rows={claims.per_page}
            totalRecords={claims.total}
            first={(claims.current_page - 1) * claims.per_page}
            onPage={(e) => {
              const page = Math.floor(e.first / claims.per_page) + 1;
              const params = new URLSearchParams(filterValues as any);
              params.append('page', page.toString());
              globalThis.location.href = `${route('vouchers.report')}?${params.toString()}`;
            }}
            tableStyle={{ minWidth: '50rem' }}
          >
            <Column field="voucher.code" header="Code" style={{ width: '10%' }} />
            <Column field="email" header="Email" style={{ width: '25%' }} />
            <Column body={transactionTypeBodyTemplate} header="Type" style={{ width: '20%' }} />
            <Column body={discountBodyTemplate} header="Discount" style={{ width: '15%' }} />
            <Column body={dateBodyTemplate} header="Claimed At" style={{ width: '20%' }} />
          </DataTable>
        </Card>
      </Container>
    </MainLayout>
  );
}
