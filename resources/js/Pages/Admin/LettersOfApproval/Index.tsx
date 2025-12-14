import { router, usePage } from '@inertiajs/react';
import {
  Card,
  Container,
  Divider,
  Group,
  Stack,
  Text,
  Title
} from '@mantine/core';
import { Column } from 'primereact/column';
import { DataTable, DataTableStateEvent } from 'primereact/datatable';
import React, { useState } from 'react';
import MainLayout from '../../../Layout/MainLayout';
import SummaryLoaModule from '../../../Modules/SummaryLoaModule';
import { AudienceWithLoA, PageProps } from '../../../types';
import FilterData from './FilterData';
import { TableData } from './TableData';

interface LettersOfApprovalPageProps extends PageProps {
  audiences: {
    data: AudienceWithLoA[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  conferences: Array<{ id: number; name: string; initial: string }>;
  loaVolumes: Array<{ id: number; volume: string }>;
  filters: {
    conference_id?: string;
    loa_volume_id?: string;
    search?: string;
  };
  summary: {
    total_participants: number;
    total_papers: number;
    total_conferences: number;
  };
}

function LettersOfApprovalIndex() {
  const { audiences, conferences, loaVolumes, filters, summary } = usePage<LettersOfApprovalPageProps>().props;
  const { data } = audiences;

  // Filter states
  const [conferenceFilter, setConferenceFilter] = useState(filters?.conference_id || '');
  const [loaVolumeFilter, setLoaVolumeFilter] = useState(filters?.loa_volume_id || '');
  const [searchTerm, setSearchTerm] = useState(filters?.search || '');

  // Global filter for DataTable
  const [globalFilterValue] = useState('');

  const handleFilterChange = () => {
    const params = new URLSearchParams();
    if (conferenceFilter) params.append('conference_id', conferenceFilter);
    if (loaVolumeFilter) params.append('loa_volume_id', loaVolumeFilter);
    if (searchTerm) params.append('search', searchTerm);

    router.visit(`/letters-of-approval?${params.toString()}`);
  };

  const clearFilters = () => {
    router.visit('/letters-of-approval');
  };

  const handlePageChange = (event: DataTableStateEvent) => {
    if (event.page !== undefined) {
      const params = new URLSearchParams(window.location.search);
      params.set('page', (event.page + 1).toString());
      if (event.rows && event.rows !== audiences.per_page) {
        params.set('per_page', event.rows.toString());
      }
      if (conferenceFilter) params.set('conference_id', conferenceFilter);
      if (loaVolumeFilter) params.set('loa_volume_id', loaVolumeFilter);
      if (searchTerm) params.set('search', searchTerm);

      router.visit(`/letters-of-approval?${params.toString()}`);
    }
  };

  return (
    <Container fluid>
      <Stack gap="lg">
        {/* Header */}
        <Group justify="space-between">
          <div>
            <Title order={2}>Letters of Approval</Title>
            <Text c="dimmed">Manage and generate Letters of Approval for conference participants</Text>
          </div>
        </Group>

        {/* Summary Cards */}
        <SummaryLoaModule data={summary} />

        {/* Filters */}
        <FilterData
          conferences={conferences}
          loaVolumes={loaVolumes}
          conferenceFilter={conferenceFilter}
          setConferenceFilter={setConferenceFilter}
          loaVolumeFilter={loaVolumeFilter}
          setLoaVolumeFilter={setLoaVolumeFilter}
          searchTerm={searchTerm}
          setSearchTerm={setSearchTerm}
          handleFilterChange={handleFilterChange}
          clearFilters={clearFilters}
        />

        <Divider />

        {/* Data Table */}
        <Card padding="lg" radius="md" withBorder>
          {/* Pagination Info */}
          <Text size="sm" c="dimmed" mb="md">
            Showing {((audiences.current_page - 1) * audiences.per_page) + 1} to {Math.min(audiences.current_page * audiences.per_page, audiences.total)} of {audiences.total} entries
          </Text>
          <DataTable
            value={data}
            lazy
            paginator
            first={(audiences.current_page - 1) * audiences.per_page}
            rows={audiences.per_page}
            totalRecords={audiences.total}
            onPage={handlePageChange}
            rowsPerPageOptions={[15, 25, 50, 100]}
            globalFilter={globalFilterValue}
            dataKey="id"
            stripedRows
            showGridlines
            tableStyle={{ minWidth: '100rem' }}
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
            currentPageReportTemplate="Showing {first} to {last} of {totalRecords} entries"
          >
            {TableData().map((col, index) => (
              <Column
                key={index}
                field={col.field}
                header={col.header}
                body={col.body}
                style={col.style}
              />
            ))}
          </DataTable>
        </Card>
      </Stack>
    </Container>
  );
}

LettersOfApprovalIndex.layout = (page: React.ReactNode) => (
  <MainLayout title="Letters of Approval">{page}</MainLayout>
);

export default LettersOfApprovalIndex;