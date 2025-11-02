import React from 'react';
import { Head } from '@inertiajs/react';
import {
  Container,
  Card,
  Title,
  Text,
  Button,
  Stack,
  Group,
  ThemeIcon,
  Alert,
  Divider,
  List
} from '@mantine/core';
import { IconCheck, IconCircleCheck, IconClock, IconMail } from '@tabler/icons-react';
import { Conference, Audiences } from '../../types';
import { formatCurrency } from '../../utils';
import AuthLayout from '../../Layout/AuthLayout';

interface RegistrationSuccessProps {
  conference: Conference;
  audience: Audiences;
}

export default function RegistrationSuccess({ conference, audience }: RegistrationSuccessProps) {
  const currency = audience.country === 'ID' ? 'idr' : 'usd';

  return (
    <>
      <Head title={`Registration Complete - ${conference.name}`} />

      <Container size="md" py="xl">
        <Card shadow="md" p="xl" radius="md">
          <Stack gap="lg" align="center">
            <ThemeIcon size={80} radius="xl" color="green" variant="light">
              <IconCheck size={40} />
            </ThemeIcon>

            <div style={{ textAlign: 'center' }}>
              <Title order={2} mb="xs">
                Registration Successful!
              </Title>
              <Text c="dimmed" size="lg">
                Thank you for registering to {conference.name}
              </Text>
            </div>

            <Card withBorder p="md" w="100%" style={{ backgroundColor: 'var(--mantine-color-green-0)' }}>
              <Stack gap="sm">
                <Text fw={500} ta="center">Registration Details</Text>
                <Divider />
                <Group justify="space-between">
                  <Text size="sm">Name:</Text>
                  <Text size="sm" fw={500}>{audience.first_name} {audience.last_name}</Text>
                </Group>
                <Group justify="space-between">
                  <Text size="sm">Email:</Text>
                  <Text size="sm" fw={500}>{audience.email}</Text>
                </Group>
                <Group justify="space-between">
                  <Text size="sm">Registration ID:</Text>
                  <Text size="sm" fw={500}>{audience.public_id}</Text>
                </Group>
                <Group justify="space-between">
                  <Text size="sm">Amount Paid:</Text>
                  <Text size="sm" fw={500} c="green">
                    {formatCurrency(audience.paid_fee, currency)}
                  </Text>
                </Group>
                <Group justify="space-between">
                  <Text size="sm">Payment Method:</Text>
                  <Text size="sm" fw={500}>
                    {audience.payment_method === 'transfer_bank' ? 'Bank Transfer' : 'PayPal'}
                  </Text>
                </Group>
                <Group justify="space-between">
                  <Text size="sm">Status:</Text>
                  <Text
                    size="sm"
                    fw={500}
                    c={audience.payment_status === 'paid' ? 'green' : 'orange'}
                  >
                    {audience.payment_status === 'paid' ? 'Paid' : 'Pending Verification'}
                  </Text>
                </Group>
              </Stack>
            </Card>

            {audience.payment_method === 'transfer_bank' ? (
              <Alert
                icon={<IconClock size={16} />}
                title="Payment Verification"
                color="orange"
                variant="light"
                w="100%"
              >
                <Text size="sm">
                  Your registration is pending payment verification. We will review your payment proof
                  and confirm your registration within 1-2 business days.
                </Text>
              </Alert>
            ) : (
              <Alert
                icon={<IconCheck size={16} />}
                title="Payment Confirmed"
                color="green"
                variant="light"
                w="100%"
              >
                <Text size="sm">
                  Your payment has been confirmed. You will receive a confirmation email shortly.
                </Text>
              </Alert>
            )}

            <Card withBorder p="md" w="100%">
              <Card.Section p="sm" withBorder>
                <Group>
                  <ThemeIcon variant="light" color="blue">
                    <IconMail size={16} />
                  </ThemeIcon>
                  <Text fw={500}>What&apos;s Next?</Text>
                </Group>
              </Card.Section>
              <Card.Section p="sm" withBorder>
                <List size="sm"
                  icon={
                    <ThemeIcon color="teal" size={24} radius="xl">
                      <IconCircleCheck size={16} />
                    </ThemeIcon>
                  }>
                  <List.Item>You will receive a confirmation email at <strong>{audience.email}</strong></List.Item>
                  <List.Item>
                    {audience.payment_method === 'transfer_bank'
                      ? 'Wait for payment verification (1-2 business days)'
                      : 'Your registration is confirmed'
                    }
                  </List.Item>
                  <List.Item>Conference details and access information will be sent closer to the event date</List.Item>
                  <List.Item>Keep your Registration ID (<strong>{audience.public_id}</strong>) for future reference</List.Item>
                </List>
              </Card.Section>
            </Card>

            <Group>
              <Button
                variant="light"
                onClick={() => window.print()}
              >
                Print Registration
              </Button>
              <Button
                onClick={() => window.location.href = '/'}
              >
                Back to Home
              </Button>
            </Group>
          </Stack>
        </Card >
      </Container >
    </>
  );
}

RegistrationSuccess.layout = (page: React.ReactNode) => (
  <AuthLayout title="Conference Registration">{page}</AuthLayout>
);