import React from 'react';
import { Button, Card, Grid, Group, Select, TextInput, Title } from '@mantine/core';

type FilterDataProps = {
  conferences: Array<{ id: number; name: string; initial: string }>;
  loaVolumes: Array<{ id: number; volume: string }>;
  conferenceFilter: string;
  setConferenceFilter: (value: string) => void;
  loaVolumeFilter: string;
  setLoaVolumeFilter: (value: string) => void;
  searchTerm: string;
  setSearchTerm: (value: string) => void;
  handleFilterChange: () => void;
  clearFilters: () => void;
};

export default function FilterData({
  conferences,
  loaVolumes,
  conferenceFilter,
  setConferenceFilter,
  loaVolumeFilter,
  setLoaVolumeFilter,
  searchTerm,
  setSearchTerm,
  handleFilterChange,
  clearFilters,
}: Readonly<FilterDataProps>) {
  return (
    <Card padding="lg" radius="md" withBorder>
      <Title order={4} mb="md">Filter Participants</Title>
      <Grid>
        <Grid.Col span={{ base: 12, md: 3 }}>
          <Select
            placeholder="-- All Conferences --"
            data={[
              { value: '', label: '-- All Conferences --' },
              ...conferences.map(conf => ({
                value: conf.id.toString(),
                label: `${conf.name} (${conf.initial})`
              }))
            ]}
            value={conferenceFilter}
            onChange={(value) => setConferenceFilter(value || '')}
          />
        </Grid.Col>
        <Grid.Col span={{ base: 12, md: 3 }}>
          <Select
            placeholder="-- All LoA Volumes --"
            data={[
              { value: '', label: '-- All LoA Volumes --' },
              ...loaVolumes.map(volume => ({
                value: volume.id.toString(),
                label: volume.volume
              }))
            ]}
            value={loaVolumeFilter}
            onChange={(value) => setLoaVolumeFilter(value || '')}
          />
        </Grid.Col>
        <Grid.Col span={{ base: 12, md: 3 }}>
          <TextInput
            placeholder="Search by name, email, institution..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            style={{ width: '100%' }}
          />
        </Grid.Col>
        <Grid.Col span={{ base: 12, md: 3 }}>
          <Group>
            <Button onClick={handleFilterChange} variant="filled" size="sm">
              Apply Filter
            </Button>
            <Button onClick={clearFilters} variant="outline" size="sm">
              Clear
            </Button>
          </Group>
        </Grid.Col>
      </Grid>
    </Card>
  )
}