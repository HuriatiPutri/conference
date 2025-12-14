import React from 'react';
import { Button, Card, Group, Select, Title } from '@mantine/core';

interface FilterDataProps {
  conferences: Array<{ id: number; name: string }>;
  conferenceFilter: string;
  setConferenceFilter: (value: string) => void;
  handleFilterChange: () => void;
  clearFilters: () => void;
}
export default function FilterData({
  conferences,
  conferenceFilter,
  setConferenceFilter,
  handleFilterChange,
  clearFilters,
}: FilterDataProps) {
  return (
    <Card padding="lg" radius="md" withBorder>
      <Title order={4} mb="md">Filter Keynote Data</Title>
      <Group gap="md">
        <Select
          placeholder="-- All Conferences --"
          data={[
            { value: '', label: '-- All Conferences --' },
            ...conferences.map(conf => ({ value: conf.id.toString(), label: conf.name }))
          ]}
          value={conferenceFilter}
          onChange={(value) => setConferenceFilter(value || '')}
          style={{ minWidth: 300 }}
        />

        <Button onClick={handleFilterChange} variant="filled">
          Apply Filter
        </Button>

        <Button onClick={clearFilters} variant="outline">
          Reset
        </Button>
      </Group>
    </Card>
  )
}