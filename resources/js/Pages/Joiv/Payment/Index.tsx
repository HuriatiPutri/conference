import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import {
  Container,
  Title,
  Text,
  Card,
  Stack,
  Radio,
  Group,
  Button,
  FileInput,
  Alert,
  Divider,
  Paper,
  ThemeIcon
} from '@mantine/core';
import { IconUpload, IconInfoCircle, IconCreditCard, IconBuildingBank } from '@tabler/icons-react';
import { formatCurrency } from '../../../utils';
import AuthLayout from '../../../Layout/AuthLayout';
import { JoivRegistration } from '../../../types';

interface JoivPaymentProps {
  readonly registration: JoivRegistration;
}

export default function JoivPaymentIndex({ registration }: JoivPaymentProps) {

  const { data, setData, post, processing, errors } = useForm({
    payment_method: '',
    payment_proof: null as File | null,
  });

  const isIndonesia = registration.country === 'ID';
  const currency = isIndonesia ? 'idr' : 'usd';

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post(`/joiv/registration/${registration.public_id}/payment`, {
      forceFormData: true,
    });
  };

  return (
    <>
      <Head title="Payment - JOIV Registration" />

      <Container size="md" py="xl">
        <Card shadow="md" padding="xl" radius="md">
          <Stack gap="lg">
            <div>
              <Title order={2} ta="center" mb="xs">
                Complete Your Payment
              </Title>
              <Text ta="center" c="dimmed">
                JOIV Article Registration
              </Text>
            </div>

            <Divider />

            <Card withBorder padding="md">
              <Stack gap="sm">
                <Group justify="space-between">
                  <Text fw={500}>Registration ID:</Text>
                  <Text c="blue">{registration.public_id}</Text>
                </Group>
                <Group justify="space-between">
                  <Text fw={500}>Name:</Text>
                  <Text>{registration.first_name} {registration.last_name}</Text>
                </Group>
                <Group justify="space-between">
                  <Text fw={500}>Paper Title:</Text>
                  <Text style={{ textAlign: 'right', maxWidth: '60%' }}>
                    {registration.paper_title}
                  </Text>
                </Group>
                <Divider />
                <Group justify="space-between">
                  <Text fw={700} size="lg">Total Amount:</Text>
                  <Text fw={700} size="lg" c="blue">
                    {formatCurrency(registration.paid_fee, currency)}
                  </Text>
                </Group>
              </Stack>
            </Card>

            <form onSubmit={handleSubmit}>
              <Stack gap="lg">
                <div>
                  <Title order={4} mb="md">Select Payment Method</Title>

                  <Stack gap="md">
                    <Paper
                      withBorder
                      p="md"
                      style={{
                        display: 'block',
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
                          Amount: {formatCurrency(registration.paid_fee, currency)}
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
                  {data.payment_method === 'transfer_bank'
                    ? 'Submit Registration'
                    : 'Pay with PayPal'
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

JoivPaymentIndex.layout = (page: React.ReactNode) => <AuthLayout>{page}</AuthLayout>;
