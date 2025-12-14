import { Card, Grid, Title, Select, Button } from '@mantine/core';
import React from 'react';

type FilterDataProps = {
  countries: string[];
  countryFilter: string;
  setCountryFilter: (value: string) => void;
  paymentStatusFilter: string;
  setPaymentStatusFilter: (value: string) => void;
  handleFilterChange: () => void;
  clearFilters: () => void;
};

export const FilterData = ({
  countries,
  countryFilter,
  setCountryFilter,
  paymentStatusFilter,
  setPaymentStatusFilter,
  handleFilterChange,
  clearFilters,
}: FilterDataProps) => {
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