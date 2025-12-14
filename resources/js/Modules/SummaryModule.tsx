import React from "react"
import { Card, Grid, Group, Text } from "@mantine/core"
import { IconCircleCheckFilled, IconCircleDashed, IconRestore, IconXboxXFilled } from '@tabler/icons-react';

type SummaryProps = {
  data: {
    paid: number;
    pending: number;
    cancelled: number;
    refunded: number;
  }
}

export default function SummaryModule({ data }: SummaryProps) {
  return (
    <Grid>
      <Grid.Col span={{ base: 12, md: 3 }}>
        <Card padding="lg" radius="md" withBorder>
          <Group justify="space-between">
            <div>
              <Text c="dimmed" size="sm" fw={500}>Total Paid Participants</Text>
              <Text fw={700} size="xl">{data.paid}</Text>
            </div>
            <IconCircleCheckFilled size={24} color="green" />
          </Group>
        </Card>
      </Grid.Col>
      <Grid.Col span={{ base: 12, md: 3 }}>
        <Card padding="lg" radius="md" withBorder>
          <Group justify="space-between">
            <div>
              <Text c="dimmed" size="sm" fw={500}>Total Pending Participants</Text>
              <Text fw={700} size="xl">{data.pending}</Text>
            </div>
            <IconCircleDashed size={24} color="orange" />
          </Group>
        </Card>
      </Grid.Col>
      <Grid.Col span={{ base: 12, md: 3 }}>
        <Card padding="lg" radius="md" withBorder>
          <Group justify="space-between">
            <div>
              <Text c="dimmed" size="sm" fw={500}>Total Cancelled Participants</Text>
              <Text fw={700} size="xl">{data.cancelled}</Text>
            </div>
            <IconXboxXFilled size={24} color="red" />
          </Group>
        </Card>
      </Grid.Col>
      <Grid.Col span={{ base: 12, md: 3 }}>
        <Card padding="lg" radius="md" withBorder>
          <Group justify="space-between">
            <div>
              <Text c="dimmed" size="sm" fw={500}>Total Refunded Participants</Text>
              <Text fw={700} size="xl">{data.refunded}</Text>
            </div>
            <IconRestore size={24} color="gray" />
          </Group>
        </Card>
      </Grid.Col>
    </Grid>
  )
}