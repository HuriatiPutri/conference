import { Head } from '@inertiajs/react';
import { Badge, Button, Card, Container, Group, Stack, Text, ThemeIcon, Title } from '@mantine/core';
import { IconArrowLeft, IconCircleCheck, IconClock } from '@tabler/icons-react';
import dayjs from 'dayjs';
import React from 'react';
import AuthLayout from '../../Layout/AuthLayout';

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

interface MembershipStatusProps {
  readonly membership: Membership;
  readonly package: MembershipPackage | null;
}

export default function MembershipStatus({ membership, package: packageData }: MembershipStatusProps) {
  const isExpired = dayjs().isAfter(dayjs(membership.end_date).endOf('day'));
  const isActive = membership.status === 'active' && !isExpired;

  return (
    <>
      <Head title="Membership Status" />

      <Container size="sm" py="xl">
        <Card radius="xl" p="xl" withBorder>
          <Stack gap="lg">
            <Group justify="space-between" align="flex-start">
              <div>
                <Text size="sm" c="dimmed" tt="uppercase" fw={700}>
                  Public Membership Status
                </Text>
                <Title order={2}>{membership.first_name} {membership.last_name}</Title>
                <Text c="dimmed">{membership.institution}</Text>
              </div>
              <Badge color={isActive ? 'green' : 'orange'} variant="light" size="lg">
                {isActive ? 'ACTIVE' : 'INACTIVE'}
              </Badge>
            </Group>

            <Group align="flex-start" gap="sm">
              <ThemeIcon size={42} radius="xl" color={isActive ? 'green' : 'orange'} variant="light">
                {isActive ? <IconCircleCheck size={22} /> : <IconClock size={22} />}
              </ThemeIcon>
              <div>
                <Text fw={700} size="lg">
                  {packageData?.name || membership.package?.name || 'Membership'}
                </Text>
                <Text size="sm" c="dimmed">
                  Membership ID: {membership.public_id}
                </Text>
              </div>
            </Group>

            <Card withBorder radius="lg" p="md" bg="gray.0">
              <Stack gap={8}>
                <Group justify="space-between">
                  <Text size="sm" c="dimmed">Status</Text>
                  <Text fw={700}>{membership.status}</Text>
                </Group>
                <Group justify="space-between">
                  <Text size="sm" c="dimmed">Valid From</Text>
                  <Text fw={500}>{dayjs(membership.start_date).format('DD MMM YYYY')}</Text>
                </Group>
                <Group justify="space-between">
                  <Text size="sm" c="dimmed">Valid Until</Text>
                  <Text fw={500}>{dayjs(membership.end_date).format('DD MMM YYYY')}</Text>
                </Group>
                <Group justify="space-between">
                  <Text size="sm" c="dimmed">Email</Text>
                  <Text fw={500}>{membership.email}</Text>
                </Group>
              </Stack>
            </Card>

            <Group justify="space-between" align="center">
              <Text size="sm" c="dimmed">Scan the QR on the membership card to reach this page.</Text>
              <Button component="a" href="/" variant="subtle" leftSection={<IconArrowLeft size={16} />}>
                Back to Homepage
              </Button>
            </Group>
          </Stack>
        </Card>
      </Container>
    </>
  );
}

MembershipStatus.layout = (page: React.ReactNode) => <AuthLayout title="Membership Status">{page}</AuthLayout>;
