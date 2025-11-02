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
  Radio,
  FileInput,
  Paper,
  ThemeIcon,
  Alert,
  Divider
} from '@mantine/core';
import { IconUpload, IconCreditCard, IconBuildingBank, IconInfoCircle } from '@tabler/icons-react';
import { Conference } from '../../types';
import { formatCurrency } from '../../utils';
import AuthLayout from '../../Layout/AuthLayout';

interface RegistrationData {
  first_name: string;
  last_name: string;
  email: string;
  country: string;
  presentation_type: string;
  paid_fee: number;
}

interface RegistrationPaymentProps {
  conference: Conference;
  registrationData: RegistrationData;
}

export default function RegistrationPayment({ conference, registrationData }: RegistrationPaymentProps) {
  const { data, setData, post, processing, errors } = useForm({
    payment_method: '',
    payment_proof: null as File | null,
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    console.log('Submitting payment form with method:', data.payment_method);

    post(`/registration/${conference.public_id}/payment`, {
      forceFormData: true,
      onBefore: () => {
        console.log('Payment submission started');
      },
      onSuccess: () => {
        console.log('Payment submission successful - should redirect to PayPal');
      },
      onError: (errors) => {
        console.error('Payment submission errors:', errors);
      },
      onFinish: () => {
        console.log('Payment submission finished');
      }
    });
  };

  const isIndonesia = registrationData.country === 'ID';
  const currency = isIndonesia ? 'idr' : 'usd';

  return (
    <>
      <Head title={`Payment - ${conference.name}`} />

      <Container size="md" py="xl">
        <Card shadow="md" p="xl" radius="md">
          <Stack gap="lg">
            <div>
              <Title order={2} ta="center" mb="xs">
                Payment Information
              </Title>
              <Text ta="center" c="dimmed" size="lg">
                {conference.name}
              </Text>
            </div>

            <Paper withBorder p="md" style={{ backgroundColor: 'var(--mantine-color-green-0)' }}>
              <Group justify="space-between">
                <div>
                  <Text fw={500}>Registration Details</Text>
                  <Text size="sm" c="dimmed">
                    {registrationData.first_name} {registrationData.last_name}
                  </Text>
                  <Text size="sm" c="dimmed">
                    {registrationData.presentation_type?.replace('_', ' ').toUpperCase()}
                  </Text>
                </div>
                <div>
                  <Text fw={700} size="xl" c="green">
                    {formatCurrency(registrationData.paid_fee, currency)}
                  </Text>
                </div>
              </Group>
            </Paper>

            <Divider />

            <form onSubmit={handleSubmit}>
              <Stack gap="lg">
                <div>
                  <Title order={4} mb="md">Select Payment Method</Title>

                  <Stack gap="md">
                    <Paper
                      withBorder
                      p="md"
                      style={{
                        display: isIndonesia ? 'block' : 'none',
                        cursor: 'pointer',
                        borderColor: data.payment_method === 'transfer_bank' ? 'var(--mantine-color-blue-5)' : undefined
                      }}
                      onClick={() => setData('payment_method', 'transfer_bank')}
                    >
                      <Group>
                        <Radio
                          value="transfer_bank"
                          checked={data.payment_method === 'transfer_bank'}
                          onChange={() => { }}
                        />
                        <ThemeIcon variant="light" size="lg">
                          <IconBuildingBank size={20} />
                        </ThemeIcon>
                        <div>
                          <Text fw={500}>Bank Transfer</Text>
                          <Text size="sm" c="dimmed">
                            Transfer to our bank account
                          </Text>
                        </div>
                      </Group>
                    </Paper>

                    <Paper
                      withBorder
                      p="md"
                      style={{
                        display: isIndonesia ? 'none' : 'block',
                        cursor: 'pointer',
                        borderColor: data.payment_method === 'payment_gateway' ? 'var(--mantine-color-blue-5)' : undefined
                      }}
                      onClick={() => setData('payment_method', 'payment_gateway')}
                    >
                      <Group>
                        <Radio
                          value="payment_gateway"
                          checked={data.payment_method === 'payment_gateway'}
                          onChange={() => { }}
                        />
                        <ThemeIcon variant="light" size="lg" color="orange">
                          <IconCreditCard size={20} />
                        </ThemeIcon>
                        <div>
                          <Text fw={500}>PayPal</Text>
                          <Text size="sm" c="dimmed">
                            Pay securely with PayPal
                          </Text>
                        </div>
                      </Group>
                    </Paper>
                  </Stack>

                  {errors.payment_method && (
                    <Text c="red" size="sm" mt="sm">{errors.payment_method}</Text>
                  )}
                </div>

                {data.payment_method === 'transfer_bank' && (
                  <Paper withBorder p="md" style={{ backgroundColor: 'var(--mantine-color-blue-0)' }}>
                    <Stack gap="md">
                      <Group>
                        <ThemeIcon variant="light" color="blue">
                          <IconInfoCircle size={16} />
                        </ThemeIcon>
                        <Text fw={500}>Bank Transfer Instructions</Text>
                      </Group>

                      <div>
                        <Text size="sm" fw={500}>Bank Account Details:</Text>
                        <Text size="sm">Bank: Bank Negara Indonesia (BNI)</Text>
                        <Text size="sm">Account Number: 0310526940</Text>
                        <Text size="sm">Account Name: Alde Alanda</Text>
                        <Text size="sm" fw={500} mt="xs">
                          Amount: {formatCurrency(registrationData.paid_fee, currency)}
                        </Text>
                      </div>

                      <Alert color="orange" variant="light">
                        Please upload your payment proof after making the transfer.
                        Your registration will be verified manually.
                      </Alert>

                      <FileInput
                        label="Payment Proof"
                        placeholder="Upload payment proof"
                        accept="image/*,.pdf"
                        leftSection={<IconUpload size={14} />}
                        value={data.payment_proof}
                        onChange={(file) => setData('payment_proof', file)}
                        error={errors.payment_proof}
                        description="Upload screenshot or receipt of your transfer"
                      />
                    </Stack>
                  </Paper>
                )}

                {data.payment_method === 'payment_gateway' && (
                  <Alert color="blue" variant="light">
                    You will be redirected to PayPal to complete your payment securely.
                  </Alert>
                )}

                <Button
                  type="submit"
                  size="lg"
                  loading={processing}
                  disabled={!data.payment_method}
                  fullWidth
                >
                  {data.payment_method !== 'transfer_bank'
                    ? 'Pay with PayPal'
                    : 'Submit Registration'
                  }
                </Button>
              </Stack>
            </form>
          </Stack>
        </Card>
      </Container>
    </>
  );
}

RegistrationPayment.layout = (page: React.ReactNode) => (
  <AuthLayout title="Conference Registration">{page}</AuthLayout>
);