import { Link, useForm, usePage } from '@inertiajs/react';
import {
  Badge,
  Box,
  Button,
  Card,
  Container,
  Grid,
  Group,
  Modal,
  Paper,
  Stack,
  Text,
  ThemeIcon,
  Title
} from '@mantine/core';
import {
  IconAlertCircle,
  IconCalendarBolt,
  IconCrown,
  IconIdBadge2,
  IconPencilPlus
} from '@tabler/icons-react';
import dayjs from 'dayjs';
import React, { useState } from 'react';
import { route } from 'ziggy-js';
import MainLayout from '../../../Layout/MainLayout';
import { EmptyEvent } from '../../../Components/Elements/EmptyEvent';
import { BadgeStatus } from '../Audiences/ExtendComponent';

interface DashboardProps {
  statRoleUser: {
    memberships: {
      id: number;
      package: {
        name: string;
      };
      public_id: string,
      end_date: string;
    };
    total_conferences: number;
    total_audiences: number;
    recent_conferences: any[];
    recent_audiences: any[];
    upcoming_conference: any[];
  };
  user: any;
  packages: any[];
}

export default function Dashboard() {
  const { statRoleUser, user, packages } = usePage<DashboardProps>().props;
  const [openModal, setOpenModal] = useState(false);

  const { data, setData, post, processing, errors } = useForm({
    package_id: '' as string | number,
  });
  return (
    <Container size="xl">
      <Stack gap="lg">
        <Group justify="space-between">
          <div>
            <Title order={2}>Dashboard</Title>
            <Text c="dimmed"> Welcome, {user?.name || 'Admin'}! Here&apos;s your system overview.</Text>
          </div>
        </Group>
        <Grid mb={"md"}>
          <Grid.Col span={{ base: 12, md: 6, lg: 6 }}>
            {/* Memberships Info Cards */}
            {statRoleUser.memberships ? (() => {
              const isExpired = dayjs().isAfter(dayjs(statRoleUser.memberships.end_date), 'day');
              return (
                <Card
                  radius="xl"
                  p="xl"
                  mt="sm"
                  h="100%"
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

                  <Stack style={{ position: 'relative', zIndex: 1, height: '100%' }} gap="sm">
                    <Stack gap={2}>
                      <Text size="sm" c={'white'} fw={600} tt="uppercase" style={{ letterSpacing: '2px', opacity: 0.8 }} mb={4}>
                        Current Plan
                      </Text>
                      <Group mb="md" gap="sm">
                        <Title order={2} style={{ color: 'white' }}>{statRoleUser.memberships.package.name}</Title>
                        <Badge color={isExpired ? 'red.9' : 'white'} variant={isExpired ? 'filled' : 'white'} c={isExpired ? 'white' : 'blue.8'} size="lg" radius="lg">
                          {isExpired ? 'INACTIVE' : 'ACTIVE'}
                        </Badge>
                      </Group>
                      <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
                        <ThemeIcon variant="white" color={isExpired ? 'red.8' : 'blue.8'} size="lg" radius="xl">
                          <IconIdBadge2 size={18} />
                        </ThemeIcon>
                        <Text size='lg' c={'white'} fw={600} lts={3}>{statRoleUser.memberships.public_id}</Text>
                      </div>
                      <Text size="sm" c={'white'} mt="xs" style={{ opacity: 0.9 }}>
                        {isExpired ? 'Membership expired on' : 'Membership valid until'}
                        <Text span c={'white'} fw={700} ml={5}>{dayjs(statRoleUser.memberships.end_date).format('DD MMMM YYYY')}</Text>
                      </Text>
                    </Stack>
                    {isExpired && (
                      <Button
                        onClick={() => setOpenModal(true)}
                        color="white"
                        variant="white"
                        c="red.7"
                        radius="xl"
                        size="md"
                        mt="auto"
                        fullWidth
                        style={{ transition: 'all 0.2s', boxShadow: '0 4px 10px rgba(0,0,0,0.1)' }}
                      >
                        Renew Membership
                      </Button>
                    )}
                  </Stack>
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
          </Grid.Col>
          <Grid.Col span={{ base: 12, md: 6, lg: 6 }}>
            <Card
              radius="xl"
              px="xl"
              pt="xl"
              pb="md"
              mt="sm"
              h="100%"
              style={{
                background: 'linear-gradient(135deg, var(--mantine-color-indigo-7) 0%, var(--mantine-color-cyan-5) 100%)',
                color: 'white',
                boxShadow: '0 10px 25px -5px rgba(76, 110, 245, 0.4)',
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
                  opacity: 0.15,
                  transform: 'rotate(15deg)'
                }}
              >
                <IconPencilPlus size={150} />
              </Box>

              <Stack style={{ position: 'relative', zIndex: 1, height: '100%' }} gap="sm">
                <Group justify="space-between" align="flex-start" mb="md">
                  <div style={{ flex: 1 }}>
                    <Text size="sm" c={'white'} fw={600} tt="uppercase" style={{ letterSpacing: '2px', opacity: 0.8 }} mb={4}>
                      Contribution
                    </Text>
                    <Title order={3} style={{ color: 'white' }}>Call for Papers</Title>
                  </div>
                </Group>

                <Text size="sm" c={'white'} style={{ opacity: 0.95, lineHeight: 1.6 }}>
                  Be a part of the next big breakthrough. We invite researchers and practitioners to register and submit their latest work to JOIV.
                </Text>

                <Button
                  component={Link}
                  href="/joiv/registration"
                  radius="xl"
                  size="md"
                  variant="white"
                  c="indigo.7"
                  fw={600}
                  mt="auto"
                  mb={18}
                  style={{ transition: 'all 0.2s' }}
                >
                  Submit Manuscript
                </Button>
              </Stack>
            </Card>
          </Grid.Col>
        </Grid>
        {/* Upcoming Conferences */}
        <Title order={4}>Upcoming Conferences</Title>
        <Grid mb={"md"}>
          {statRoleUser.upcoming_conference.length > 0 ? statRoleUser.upcoming_conference.map((conference: any) => {
            const isRegistered = statRoleUser.recent_conferences.some((audience: any) => audience.conference_id === conference.id);

            const now = dayjs();
            const startDate = dayjs(conference.registration_start_date).startOf('day');
            const endDate = dayjs(conference.registration_end_date).endOf('day');

            const registerUpcoming = now.isBefore(startDate);
            const registerClose = now.isAfter(endDate);

            let btnText = '';
            let btnColor: string = 'blue';

            if (isRegistered) {
              btnText = 'Registered';
              btnColor = 'green';
            } else if (registerUpcoming) {
              btnText = 'Registration Opening Soon';
              btnColor = 'gray';
            } else if (registerClose) {
              btnText = 'Registration Closed';
              btnColor = 'red';
            } else {
              btnText = 'Register';
              btnColor = 'blue';
            }

            const isDisabled = isRegistered || registerUpcoming || registerClose;

            return (!isRegistered &&
              <Grid.Col key={conference.id} span={{ base: 12, lg: 4 }}>
                <Card
                  radius="xl"
                  p="xl"
                  withBorder
                  mt="sm"
                  h="100%"
                  style={{
                    background: 'linear-gradient(135deg, var(--mantine-color-gray-0) 0%, var(--mantine-color-gray-1) 100%)',
                    borderStyle: 'dashed',
                    display: 'flex',
                    flexDirection: 'column'
                  }}
                >
                  <div style={{ display: 'flex', flexDirection: 'column', flex: 1 }}>
                    <div style={{ flex: 1 }}>
                      <Group gap="sm">
                        <ThemeIcon color="gray" variant="light" size="lg" radius="xl">
                          <IconCalendarBolt size={18} />
                        </ThemeIcon>
                        <Title order={5} c="dark.7">{conference.name}</Title>
                      </Group>
                    </div>
                    <Stack gap={0}>
                      <Text size="sm">Registration Date:</Text>
                      <Text size="md" c={'black'} fw={600}>{dayjs(conference.registration_start_date).format('DD MMMM YYYY')} - {dayjs(conference.registration_end_date).format('DD MMMM YYYY')}</Text>
                      <Text size="sm">Conference Date:</Text>
                      <Text size="md" c={'black'} fw={600}>{dayjs(conference.date).format('DD MMMM YYYY')}</Text>
                      <Text size="sm">Conference City:</Text>
                      <Text size="md" c={'black'} fw={600}>{conference.city}</Text>
                    </Stack>
                    <Group justify="space-between" align="flex-end" mt="md">
                      <Button
                        component={Link}
                        disabled={isDisabled}
                        href={`/registration/${conference.public_id}`}
                        radius="xl"
                        size="md"
                        fullWidth={true}
                        color={btnColor}
                        variant={!isDisabled ? 'gradient' : 'filled'}
                        gradient={!isDisabled ? { from: 'indigo', to: 'cyan' } : undefined}
                      >
                        {btnText}
                      </Button>
                    </Group>
                  </div>
                </Card>
              </Grid.Col>
            )
          }) : <Grid.Col span={12}>
            <EmptyEvent
              title="Upcoming Conferences"
              description="We’re planning something great! Stay tuned for updates" />
          </Grid.Col>}
        </Grid>
        {/* Recent Data */}
        <Title order={4}>Attending Events</Title>
        <Grid mb="md">
          {statRoleUser.recent_conferences.length > 0 ? statRoleUser.recent_conferences.map((audience: any) => (
            <Grid.Col key={audience.id} span={{ base: 12, lg: 4 }}>
              <Card
                radius="xl"
                p="xl"
                withBorder
                mt="sm"
                h="100%"
                style={{
                  background: 'var(--mantine-color-white)',
                  display: 'flex',
                  flexDirection: 'column'
                }}
              >
                <div style={{ display: 'flex', flexDirection: 'column', flex: 1 }}>
                  <div style={{ flex: 1 }}>
                    <Group gap="sm" mb="md">
                      <ThemeIcon color="green" variant="light" size="lg" radius="xl">
                        <IconCalendarBolt size={18} />
                      </ThemeIcon>
                      <Title order={5} c="dark.7">{audience.conference?.name}</Title>
                    </Group>
                  </div>
                  <Stack gap={0}>
                    <Text size="sm">Conference Date:</Text>
                    <Text size="md" c={'black'} fw={600}>{dayjs(audience.conference?.date).format('DD MMMM YYYY')}</Text>
                    <Text size="sm">Conference City:</Text>
                    <Text size="md" c={'black'} fw={600}>{audience.conference?.city}</Text>
                  </Stack>
                  <BadgeStatus mt="md" status={audience.payment_status} />
                </div>
              </Card>
            </Grid.Col>
          )) : (
            <Grid.Col span={12}>
              <EmptyEvent
                title="Attending Events"
                description="No events yet? Let’s change that! Explore our conference list to find your next event." />
            </Grid.Col>
          )}
        </Grid>
      </Stack>
      <Modal
        onClose={() => setOpenModal(false)}
        opened={openModal}
        title={"Renew Membership"}
        size="lg"
      >
        <Text mb="md">Please select a package to renew your membership:</Text>
        {/* list package */}
        <Stack gap="sm" mb="xl">
          {packages.map((pkg: any) => (
            <Paper
              key={pkg.id}
              withBorder
              p="md"
              style={{
                cursor: 'pointer',
                borderColor: data.package_id === pkg.id ? 'var(--mantine-color-blue-5)' : undefined,
                backgroundColor: data.package_id === pkg.id ? 'var(--mantine-color-blue-0)' : undefined
              }}
              onClick={() => setData('package_id', pkg.id)}
            >
              <Group justify="space-between">
                <Group>
                  <input
                    type="radio"
                    style={{ cursor: 'pointer', accentColor: 'var(--mantine-color-blue-6)' }}
                    value={pkg.id.toString()}
                    checked={data.package_id === pkg.id}
                    onChange={() => setData('package_id', pkg.id)}
                  />
                  <div>
                    <Text fw={500}>{pkg.name}</Text>
                    <Text size="sm" c="dimmed">Duration: {pkg.duration} days</Text>
                  </div>
                </Group>
                <Text fw={700} c="blue">
                  ${pkg.price}
                </Text>
              </Group>
            </Paper>
          ))}
          {errors.package_id && (
            <Text c="red" size="sm">{errors.package_id}</Text>
          )}
        </Stack>
        <Group justify="flex-end">
          <Button
            onClick={() => setOpenModal(false)}
            color="gray"
            variant="subtle"
          >
            Cancel
          </Button>
          <Button
            onClick={() => {
              post(route('memberships.renew', statRoleUser.memberships?.id), {
                onSuccess: () => setOpenModal(false)
              });
            }}
            loading={processing}
            disabled={!data.package_id}
            color="blue"
            variant="filled"
          >
            Renew Now
          </Button>
        </Group>
      </Modal>
    </Container>
  );
}

Dashboard.layout = (page: React.ReactNode) => (
  <MainLayout title="Dashboard">{page}</MainLayout>
);