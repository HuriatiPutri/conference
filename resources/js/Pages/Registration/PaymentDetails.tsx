import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import {
  Container,
  Card,
  Title,
  Text,
  Button,
  Stack,
  Group,
  Paper,
  ThemeIcon,
  Alert,
  Divider,
  Badge
} from '@mantine/core';
import { IconCreditCard, IconInfoCircle, IconUser, IconMail, IconBuildingBank } from '@tabler/icons-react';
import { Conference } from '../../types';
import { formatCurrency } from '../../utils';
import AuthLayout from '../../Layout/AuthLayout';

interface Audience {
  id: number;
  public_id: string;
  first_name: string;
  last_name: string;
  email: string;
  institution: string;
  paper_title?: string;
  presentation_type: string;
  paid_fee: number;
  payment_status: string;
  payment_method: string;
  country: string;
}

interface PaymentDetailsProps {
  conference: Conference;
  audience: Audience;
}

export default function PaymentDetails({ conference, audience }: PaymentDetailsProps) {
  const { post, processing } = useForm({
    payment_method: 'payment_gateway',
    audience_id: audience.public_id
  });

  const handlePayPalPayment = () => {
    // Process PayPal payment using existing endpoint
    post(`/registration/${conference.public_id}/payment`);
  };

  const isIndonesia = audience.country === 'ID';
  const currency = isIndonesia ? 'idr' : 'usd';

  return (
    <>
      <Head title={`Payment Details - ${conference.name}`} />

      <Container size="md" py="xl">
        <Card shadow="md" p="xl" radius="md">
          <Stack gap="lg">
            <div>
              <Title order={2} ta="center" mb="xs">
                Order Summary
              </Title>
              <Text ta="center" c="dimmed" size="lg">
                {conference.name}
              </Text>
              <Text ta="center" c="dimmed" size="sm" mt="xs">
                {conference.city}, {conference.country} • {conference.date}
              </Text>
            </div>

            <Paper withBorder p="md" style={{ backgroundColor: 'var(--mantine-color-blue-0)' }}>
              <Stack gap="sm">
                <Group justify="space-between">
                  <Text fw={500} size="lg">Registration Details</Text>
                  <Badge color="yellow" variant="light">
                    Pending Payment
                  </Badge>
                </Group>

                <Group>
                  <ThemeIcon variant="light" size="sm">
                    <IconUser size={14} />
                  </ThemeIcon>
                  <Text size="sm">
                    {audience.first_name} {audience.last_name}
                  </Text>
                </Group>

                <Group>
                  <ThemeIcon variant="light" size="sm">
                    <IconMail size={14} />
                  </ThemeIcon>
                  <Text size="sm">{audience.email}</Text>
                </Group>

                <Group>
                  <ThemeIcon variant="light" size="sm">
                    <IconBuildingBank size={14} />
                  </ThemeIcon>
                  <Text size="sm">{audience.institution}</Text>
                </Group>

                <Group>
                  <Text size="sm" fw={500}>Participation Type:</Text>
                  <Badge variant="outline">
                    {audience.presentation_type?.replace('_', ' ').toUpperCase()}
                  </Badge>
                </Group>

                {audience.paper_title && (
                  <div>
                    <Text size="sm" fw={500} mb="xs">Paper Title:</Text>
                    <Text size="sm" style={{ fontStyle: 'italic' }}>
                      &ldquo;{audience.paper_title}&rdquo;
                    </Text>
                  </div>
                )}
              </Stack>
            </Paper>

            <Paper withBorder p="md" style={{ backgroundColor: 'var(--mantine-color-green-0)' }}>
              <Group justify="space-between" align="center">
                <div>
                  <Text fw={500} size="lg">Total Amount</Text>
                  <Text size="sm" c="dimmed">
                    Registration fee for {audience.presentation_type?.replace('_', ' ')}
                  </Text>
                </div>
                <Text fw={700} size="xl" c="green">
                  {formatCurrency(audience.paid_fee, currency)}
                </Text>
              </Group>
            </Paper>

            <Divider />

            <Paper withBorder p="md">
              <Stack gap="md">
                <Group>
                  <ThemeIcon variant="light" color="orange" size="lg">
                    <IconCreditCard size={20} />
                  </ThemeIcon>
                  <div>
                    <Text fw={500}>PayPal Payment</Text>
                    <Text size="sm" c="dimmed">
                      You will be redirected to PayPal to complete your payment securely
                    </Text>
                  </div>
                </Group>

                <Alert color="blue" variant="light">
                  <Group>
                    <ThemeIcon variant="light" color="blue" size="sm">
                      <IconInfoCircle size={14} />
                    </ThemeIcon>
                    <div>
                      <Text size="sm" fw={500}>Secure Payment</Text>
                      <Text size="sm">
                        Your payment will be processed securely through PayPal.
                        You will receive a confirmation email once payment is completed.
                      </Text>
                    </div>
                  </Group>
                </Alert>
              </Stack>
            </Paper>

            <Stack gap="md">
              <Button
                size="lg"
                loading={processing}
                onClick={handlePayPalPayment}
                leftSection={<IconCreditCard size={20} />}
                fullWidth
                color="orange"
              >
                Proceed to PayPal Payment
              </Button>

              <Text ta="center" size="xs" c="dimmed">
                By proceeding, you agree to the conference terms and conditions.
                Registration ID: {audience.public_id}
              </Text>
            </Stack>
          </Stack>
        </Card>
      </Container>
    </>
  );
}

PaymentDetails.layout = (page: React.ReactNode) => (
  <AuthLayout title="Payment Details">{page}</AuthLayout>
);