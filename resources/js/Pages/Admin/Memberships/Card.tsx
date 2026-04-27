import { Link, usePage } from '@inertiajs/react';
import { Badge, Box, Button, Card, Container, Group, Stack, Text, ThemeIcon, Title } from '@mantine/core';
import { IconAlertCircle, IconArrowLeft, IconCrown, IconIdBadge2 } from '@tabler/icons-react';
import dayjs from 'dayjs';
import React from 'react';
import MainLayout from '../../../Layout/MainLayout';

interface MembershipPackage {
  name: string;
}

interface Membership {
  public_id: string;
  status: string;
  first_name: string;
  last_name: string;
  email: string;
  institution: string;
  country: string;
  start_date: string;
  end_date: string;
  package: MembershipPackage | null;
}

interface MembershipCardProps {
  membership: Membership | null;
  user: {
    name: string;
    email: string;
  };
}

export default function MembershipCardPage() {
  const { membership, user } = usePage().props as unknown as MembershipCardProps;

  const isExpired = membership
    ? dayjs().isAfter(dayjs(membership.end_date).endOf('day'))
    : false;

  const isActive = membership?.status === 'active' && !isExpired;

  const renderEmptyState = () => (
    <Card radius="xl" p="xl" withBorder>
      <Stack gap="sm" align="center" py="md">
        <ThemeIcon color="gray" variant="light" size="xl" radius="xl">
          <IconCrown size={24} />
        </ThemeIcon>
        <Title order={3}>No Membership Found</Title>
        <Text c="dimmed" ta="center">
          {user?.name || 'User'}, you do not have an active membership yet.
        </Text>
        <Button component={Link} href="/register-membership" radius="xl" variant="gradient" gradient={{ from: 'indigo', to: 'cyan' }}>
          Register Membership
        </Button>
      </Stack>
    </Card>
  );

  const renderMembershipCard = () => {
    if (!membership) {
      return renderEmptyState();
    }

    return (
      <Card
        radius="xl"
        p="xl"
        style={{
          background: isActive
            ? 'linear-gradient(135deg, var(--mantine-color-indigo-7) 0%, var(--mantine-color-cyan-5) 100%)'
            : 'linear-gradient(135deg, var(--mantine-color-red-6) 0%, var(--mantine-color-orange-5) 100%)',
          color: 'white',
          boxShadow: isActive
            ? '0 10px 25px -5px rgba(76, 110, 245, 0.4)'
            : '0 10px 25px -5px rgba(250, 82, 82, 0.4)',
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
          {isActive ? <IconCrown size={150} /> : <IconAlertCircle size={150} />}
        </Box>

        <Stack gap="md" style={{ position: 'relative', zIndex: 1 }}>
          <Group justify="space-between" align="flex-start">
            <div>
              <Text size="sm" fw={600} c={'white'} tt="uppercase" style={{ letterSpacing: '2px', opacity: 0.85 }}>
                SOTVI Membership
              </Text>
              <Title order={2} style={{ color: 'white' }}>
                {membership.package?.name || 'General Membership'}
              </Title>
            </div>
            <Badge
              color={isActive ? 'white' : 'red.9'}
              variant={isActive ? 'white' : 'filled'}
              c={isActive ? 'blue.8' : 'white'}
              size="lg"
              radius="lg"
            >
              {isActive ? 'ACTIVE' : 'INACTIVE'}
            </Badge>
          </Group>

          <Group gap="sm">
            <ThemeIcon variant="white" color={isActive ? 'blue.8' : 'red.8'} size="lg" radius="xl">
              <IconIdBadge2 size={18} />
            </ThemeIcon>
            <Text fw={700} size="xl" lts={3} c={'white'}>
              {membership.public_id}
            </Text>
          </Group>

          <Stack gap={2}>
            <Text size="sm" style={{ opacity: 0.9 }} c={'white'} tt="uppercase">
              Card Holder
            </Text>
            <Text fw={700} size="lg" c={'white'}>
              {membership.first_name} {membership.last_name}
            </Text>
            <Text size="sm" style={{ opacity: 0.92 }} c={'white'}>
              {membership.email}
            </Text>
            <Text size="sm" style={{ opacity: 0.92 }} c={'white'}>
              {membership.institution} • {membership.country}
            </Text>
          </Stack>

          <Group justify="space-between" mt="sm">
            <div>
              <Text size="xs" tt="uppercase" style={{ opacity: 0.75 }} c={'white'}>
                Start Date
              </Text>
              <Text fw={600} c={'white'}>
                {dayjs(membership.start_date).format('DD MMM YYYY')}
              </Text>
            </div>
            <div>
              <Text size="xs" tt="uppercase" style={{ opacity: 0.75 }} c={'white'}>
                Valid Until
              </Text>
              <Text fw={600} c={'white'}>
                {dayjs(membership.end_date).format('DD MMM YYYY')}
              </Text>
            </div>
          </Group>
        </Stack>
      </Card>
    );
  };

  return (
    <Container size="md">
      <Stack gap="lg">
        <Group justify="space-between" align="center">
          <div>
            <Title order={2}>Membership Card</Title>
            <Text c="dimmed">Digital card for your current membership.</Text>
          </div>
          <Button component={Link} href="/dashboard" variant="subtle" leftSection={<IconArrowLeft size={16} />}>
            Back to Dashboard
          </Button>
        </Group>

        {renderMembershipCard()}
      </Stack>
    </Container>
  );
}

MembershipCardPage.layout = (page: React.ReactNode) => <MainLayout title="Membership Card">{page}</MainLayout>;
