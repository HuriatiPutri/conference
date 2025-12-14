import React from 'react';
import { Card, Grid, Group, Text } from '@mantine/core';
import { IconFileText } from '@tabler/icons-react';

type SummaryProps = {
  data: {
    total_participants: number;
    total_papers: number;
    total_conferences: number;
  }
}
export default function SummaryLoaModule({ data }: SummaryProps) {
  return (
    <Grid>
      <Grid.Col span={{ base: 12, md: 4 }}>
        <Card padding="lg" radius="md" withBorder>
          <Group justify="space-between">
            <div>
              <Text c="dimmed" size="sm" fw={500}>Total Participants</Text>
              <Text fw={700} size="xl">{data.total_participants}</Text>
            </div>
            <IconFileText size={24} color="blue" />
          </Group>
        </Card>
      </Grid.Col>
      <Grid.Col span={{ base: 12, md: 4 }}>
        <Card padding="lg" radius="md" withBorder>
          <Group justify="space-between">
            <div>
              <Text c="dimmed" size="sm" fw={500}>Papers Submitted</Text>
              <Text fw={700} size="xl">{data.total_papers}</Text>
            </div>
            <IconFileText size={24} color="green" />
          </Group>
        </Card>
      </Grid.Col>
      <Grid.Col span={{ base: 12, md: 4 }}>
        <Card padding="lg" radius="md" withBorder>
          <Group justify="space-between">
            <div>
              <Text c="dimmed" size="sm" fw={500}>Active Conferences</Text>
              <Text fw={700} size="xl">{data.total_conferences}</Text>
            </div>
            <IconFileText size={24} color="orange" />
          </Group>
        </Card>
      </Grid.Col>
    </Grid>
  )
}