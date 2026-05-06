import { Button, Card, Grid, Select, Title } from '@mantine/core';
import React from 'react';

type FilterDataProps = {
  statusFilter: string;
  setStatusFilter: (value: string) => void;
  handleFilterChange: () => void;
  clearFilters: () => void;
};

export const FilterData = ({
  statusFilter,
  setStatusFilter,
  handleFilterChange,
  clearFilters,
}: FilterDataProps) => {
  return (
    <Card padding="lg" radius="md" withBorder>
      <Title order={4} mb="md">Filter Data</Title>
      <Grid>
        <Grid.Col span={{ base: 12, md: 4 }}>
          <Select
            placeholder="-- All Status --"
            data={[
              { value: '', label: '-- All Status --' },
              { value: 'pending', label: 'Pending' },
              { value: 'active', label: 'Active' },
              { value: 'inactive', label: 'Inactive' },
              { value: 'suspended', label: 'Suspended' },
            ]}
            value={statusFilter}
            onChange={(value) => setStatusFilter(value || '')}
            clearable
          />
        </Grid.Col>
        <Grid.Col span={{ base: 12, md: 8 }}>
          <Button onClick={() => handleFilterChange()} mr={'sm'}>
            Apply Filters
          </Button>
          <Button variant="outline" onClick={clearFilters}>
            Clear Filter
          </Button>
        </Grid.Col>
      </Grid>
    </Card>
  );
};
