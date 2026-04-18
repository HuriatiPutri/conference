import React from 'react';
import { usePage, Link } from '@inertiajs/react';
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
  ThemeIcon,
  Alert,
  Button,
  Card,
  Box
} from '@mantine/core';
import {
  IconUsers,
  IconCalendar,
  IconEye,
  IconChartBar,
  IconCrown,
  IconAlertCircle
} from '@tabler/icons-react';
import MainLayout from '../../../Layout/MainLayout';
import dayjs from 'dayjs';
import { route } from 'ziggy-js';

interface DashboardProps {
  statRoleUser: {
    memberships: {
      package: {
        name: string;
      };
      end_date: string;
    };
    total_conferences: number;
    total_audiences: number;
    recent_conferences: any[];
    recent_audiences: any[];
  };
  user: any;
}

export default function Dashboard() {
  const { statRoleUser, user } = usePage<DashboardProps>().props;
  console.log('statRoleUser.memberships', statRoleUser.recent_conferences)

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
        {/* Memberships Info Cards */}
        {statRoleUser.memberships ? (() => {
          const isExpired = dayjs().isAfter(dayjs(statRoleUser.memberships.end_date), 'day');
          return (
            <Card
              radius="xl"
              p="xl"
              mt="sm"
              style={{
                background: isExpired
                  ? 'linear-gradient(135deg, var(--mantine-color-red-6) 0%, var(--mantine-color-orange-5) 100%)'
                  : 'linear-gradient(135deg, var(--mantine-color-indigo-7) 0%, var(--mantine-color-cyan-5) 100%)',
                color: 'white',
                boxShadow: isExpired ? '0 10px 25px -5px rgba(250, 82, 82, 0.4)' : '0 10px 25px -5px rgba(76, 110, 245, 0.4)',
                border: 'none',
                position: 'relative',
                overflow: 'hidden'
              }}
            >
              <Box
                style={{
                  position: 'absolute',
                  top: '-20px',
                  right: '-10px',
                  opacity: 0.1,
                  transform: 'rotate(15deg)'
                }}
              >
                {isExpired ? <IconAlertCircle size={150} /> : <IconCrown size={150} />}
              </Box>

              <Group justify="space-between" align="center" style={{ position: 'relative', zIndex: 1 }}>
                <div>
                  <Text size="sm" c={'white'} fw={600} tt="uppercase" style={{ letterSpacing: '2px', opacity: 0.8 }} mb={4}>
                    Current Plan
                  </Text>
                  <Group mb="md" gap="sm">
                    <Title order={2} style={{ color: 'white' }}>{statRoleUser.memberships.package.name}</Title>
                    <Badge color={isExpired ? 'red.9' : 'white'} variant={isExpired ? 'filled' : 'white'} c={isExpired ? 'white' : 'blue.8'} size="lg" radius="sm">
                      {isExpired ? 'INACTIVE' : 'ACTIVE'}
                    </Badge>
                  </Group>
                  <Text size="sm" c={'white'} mt="xs" style={{ opacity: 0.9 }}>
                    {isExpired ? 'Membership expired on' : 'Membership valid until'}
                    <Text span c={'white'} fw={700} ml={5}>{dayjs(statRoleUser.memberships.end_date).format('DD MMMM YYYY')}</Text>
                  </Text>
                </div>

                {isExpired && (
                  <Button
                    component={Link}
                    href="/register-membership"
                    color="white"
                    variant="white"
                    c="red.7"
                    radius="xl"
                    size="md"
                    style={{ transition: 'all 0.2s', boxShadow: '0 4px 10px rgba(0,0,0,0.1)' }}
                  >
                    Renew Membership
                  </Button>
                )}
              </Group>
            </Card>
          );
        })() : (
          <Card
            radius="xl"
            p="xl"
            withBorder
            mt="sm"
            style={{
              background: 'linear-gradient(135deg, var(--mantine-color-gray-0) 0%, var(--mantine-color-gray-1) 100%)',
              borderStyle: 'dashed'
            }}
          >
            <Group justify="space-between" align="center">
              <div>
                <Group gap="sm" mb="xs">
                  <ThemeIcon color="gray" variant="light" size="lg" radius="xl">
                    <IconCrown size={18} />
                  </ThemeIcon>
                  <Title order={3} c="dark.7">No Active Membership</Title>
                </Group>
                <Text c="dimmed" size="md">Join our platform today to get access to exclusive benefits and conferences!</Text>
              </div>
              <Button component={Link} href="/register-membership" radius="xl" size="md" variant="gradient" gradient={{ from: 'indigo', to: 'cyan' }}>
                Subscribe Now
              </Button>
            </Group>
          </Card>
        )}

        {/* Recent Data */}
        <Grid>
          <Grid.Col span={{ base: 12, lg: 12 }}>
            <Paper p="md" radius="md">
              <Title order={4} mb="md">Recent Conferences</Title>
              <Table>
                <Table.Thead>
                  <Table.Tr>
                    <Table.Th>Conference</Table.Th>
                    <Table.Th>City</Table.Th>
                    <Table.Th>Date</Table.Th>
                    <Table.Th>Action</Table.Th>
                  </Table.Tr>
                </Table.Thead>
                <Table.Tbody>
                  {statRoleUser.recent_conferences.map((audience: any) => (
                    <Table.Tr key={audience.id}>
                      <Table.Td>
                        <Text fz="sm">{audience.conference?.name}</Text>
                      </Table.Td>
                      <Table.Td>
                        <Text fz="sm">{audience.conference?.city}</Text>
                      </Table.Td>
                      <Table.Td>
                        <Text fz="sm">{new Date(audience.conference?.date).toLocaleDateString('id-ID')}</Text>
                      </Table.Td>
                      <Table.Td>
                        <Button
                          component="a"
                          size="xs"
                          variant="light"
                          leftSection={<i className="pi pi-download" />}
                          href={route('audiences.receipt', audience.id)}
                          target="_blank"
                        >
                          Download Receipt
                        </Button>
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