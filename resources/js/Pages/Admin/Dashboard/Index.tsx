import React from 'react';
import { usePage } from '@inertiajs/react';
import {
  Grid,
  Paper,
  Text,
  Title,
  Group,
  Stack,
  Table,
  Badge,
  Container,
  ThemeIcon
} from '@mantine/core';
import {
  IconUsers,
  IconCalendar,
  IconEye,
  IconChartBar
} from '@tabler/icons-react';
import MainLayout from '../../../Layout/MainLayout';

interface DashboardProps {
  stats: {
    total_conferences: number;
    total_audiences: number;
    recent_conferences: any[];
    recent_audiences: any[];
  };
  user: any;
}

export default function Dashboard() {
  const { stats, user } = usePage<DashboardProps>().props;

  const StatCard = ({ title, value, icon, color }: any) => (
    <Paper withBorder p="md" radius="md">
      <Group justify="apart">
        <div>
          <Text c="dimmed" tt="uppercase" fw={700} fz="xs">
            {title}
          </Text>
          <Text fw={700} fz="xl">
            {value}
          </Text>
        </div>
        <ThemeIcon color={color} variant="light" size={38} radius="md">
          {icon}
        </ThemeIcon>
      </Group>
    </Paper>
  );

  return (
    <Container size="xl">
      <Stack gap="lg">
        <Group justify="space-between">
          <div>
            <Title order={2}>Dashboard</Title>
            <Text c="dimmed"> Welcome, {user?.name || 'Admin'}! Here&apos;s your system overview.</Text>
          </div>
        </Group>
        {/* Stats Cards */}
        <Grid>
          <Grid.Col span={{ base: 12, sm: 6, lg: 3 }}>
            <StatCard
              title="Total Conferences"
              value={stats.total_conferences}
              icon={<IconCalendar size="1.4rem" />}
              color="blue"
            />
          </Grid.Col>
          <Grid.Col span={{ base: 12, sm: 6, lg: 3 }}>
            <StatCard
              title="Total Participants"
              value={stats.total_audiences}
              icon={<IconUsers size="1.4rem" />}
              color="green"
            />
          </Grid.Col>
          <Grid.Col span={{ base: 12, sm: 6, lg: 3 }}>
            <StatCard
              title="Average Participants"
              value={stats.total_conferences > 0 ? Math.round(stats.total_audiences / stats.total_conferences) : 0}
              icon={<IconChartBar size="1.4rem" />}
              color="orange"
            />
          </Grid.Col>
          <Grid.Col span={{ base: 12, sm: 6, lg: 3 }}>
            <StatCard
              title="Active This Month"
              value={stats.recent_conferences.length}
              icon={<IconEye size="1.4rem" />}
              color="red"
            />
          </Grid.Col>
        </Grid>

        {/* Recent Data */}
        <Grid>
          <Grid.Col span={{ base: 12, lg: 6 }}>
            <Paper withBorder p="md" radius="md">
              <Title order={4} mb="md">Recent Conferences</Title>
              <Table>
                <Table.Thead>
                  <Table.Tr>
                    <Table.Th>Name</Table.Th>
                    <Table.Th>City</Table.Th>
                    <Table.Th>Date</Table.Th>
                  </Table.Tr>
                </Table.Thead>
                <Table.Tbody>
                  {stats.recent_conferences.map((conf: any) => (
                    <Table.Tr key={conf.id}>
                      <Table.Td>
                        <Stack gap={0}>
                          <Text fw={500} fz="sm">{conf.name}</Text>
                          <Badge size="xs" variant="light">{conf.initial}</Badge>
                        </Stack>
                      </Table.Td>
                      <Table.Td>
                        <Text fz="sm">{conf.city}</Text>
                      </Table.Td>
                      <Table.Td>
                        <Text fz="sm">{new Date(conf.date).toLocaleDateString('id-ID')}</Text>
                      </Table.Td>
                    </Table.Tr>
                  ))}
                </Table.Tbody>
              </Table>
            </Paper>
          </Grid.Col>

          <Grid.Col span={{ base: 12, lg: 6 }}>
            <Paper withBorder p="md" radius="md">
              <Title order={4} mb="md">Recent Participants</Title>
              <Table>
                <Table.Thead>
                  <Table.Tr>
                    <Table.Th>Name</Table.Th>
                    <Table.Th>Email</Table.Th>
                    <Table.Th>Conference</Table.Th>
                  </Table.Tr>
                </Table.Thead>
                <Table.Tbody>
                  {stats.recent_audiences.map((audience: any) => (
                    <Table.Tr key={audience.id}>
                      <Table.Td>
                        <Text fw={500} fz="sm">
                          {audience.first_name} {audience.last_name}
                        </Text>
                      </Table.Td>
                      <Table.Td>
                        <Text fz="sm" c="dimmed">{audience.email}</Text>
                      </Table.Td>
                      <Table.Td>
                        <Text fz="sm">{audience.conference?.name}</Text>
                      </Table.Td>
                    </Table.Tr>
                  ))}
                </Table.Tbody>
              </Table>
            </Paper>
          </Grid.Col>
        </Grid>
      </Stack>
    </Container>
  );
}

Dashboard.layout = (page: React.ReactNode) => (
  <MainLayout title="Dashboard">{page}</MainLayout>
);