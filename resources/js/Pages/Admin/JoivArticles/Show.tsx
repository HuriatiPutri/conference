import { router, useForm, usePage } from '@inertiajs/react';
import { Button, Card, Container, Divider, Group, Stack, Text, Title, Badge, Grid, Select } from '@mantine/core';
import { IconDownload, IconFileText, IconReceipt } from '@tabler/icons-react';
import React, { useState } from 'react';
import { route } from 'ziggy-js';
import MainLayout from '../../../Layout/MainLayout';
import { formatCurrency } from '../../../utils';

interface JoivRegistration {
  id: number;
  public_id: string;
  first_name: string;
  last_name: string;
  email_address: string;
  phone_number: string;
  institution: string;
  country: string;
  paper_id: string | null;
  paper_title: string;
  full_paper_path: string | null;
  payment_status: string;
  payment_method: string | null;
  payment_proof_path: string | null;
  paid_fee: number;
  created_at: string;
  updated_at: string;
}

function JoivArticleShow() {
  const { registration } = usePage<{
    registration: JoivRegistration;
  }>().props;

  const [isUpdatingStatus, setIsUpdatingStatus] = useState(false);

  const { data, setData, patch, processing } = useForm({
    payment_status: registration.payment_status,
  });

  const handleStatusUpdate = () => {
    setIsUpdatingStatus(true);
    patch(route('joiv-articles.updatePaymentStatus', registration.id), {
      onSuccess: () => {
        setIsUpdatingStatus(false);
      },
      onError: () => {
        setIsUpdatingStatus(false);
      },
    });
  };

  const handleDownloadPaper = () => {
    window.location.href = route('joiv-articles.downloadPaper', registration.id);
  };

  const handleDownloadPaymentProof = () => {
    window.location.href = route('joiv-articles.downloadPaymentProof', registration.id);
  };

  const handleDownloadReceipt = () => {
    window.location.href = route('joiv-articles.downloadReceipt', registration.id);
  };

  const getStatusBadge = (status: string) => {
    const statusMap: Record<string, { color: string; label: string }> = {
      paid: { color: 'green', label: 'Paid' },
      pending_payment: { color: 'yellow', label: 'Pending' },
      cancelled: { color: 'red', label: 'Cancelled' },
      refunded: { color: 'gray', label: 'Refunded' },
    };

    const statusInfo = statusMap[status] || { color: 'gray', label: status };
    return <Badge color={statusInfo.color} size="lg">{statusInfo.label}</Badge>;
  };

  const getPaymentMethodText = (method: string | null) => {
    if (!method) return '-';
    return method === 'transfer_bank' ? 'Bank Transfer' : 'PayPal';
  };

  return (
    <MainLayout>
      <Container size="lg">
        <Stack gap="lg">
          <Group justify="space-between">
            <div>
              <Title order={2}>JOIV Article Details</Title>
              <Text c="dimmed">Registration ID: {registration.public_id}</Text>
            </div>
            <Button variant="subtle" onClick={() => router.visit(route('joiv-articles.index'))}>
              Back to List
            </Button>
          </Group>

          <Grid>
            <Grid.Col span={{ base: 12, md: 8 }}>
              <Card withBorder>
                <Stack gap="md">
                  <Title order={4}>Personal Information</Title>
                  <Divider />

                  <Group justify="space-between">
                    <Text fw={500}>First Name:</Text>
                    <Text>{registration.first_name}</Text>
                  </Group>

                  <Group justify="space-between">
                    <Text fw={500}>Last Name:</Text>
                    <Text>{registration.last_name}</Text>
                  </Group>

                  <Group justify="space-between">
                    <Text fw={500}>Email:</Text>
                    <Text>{registration.email_address}</Text>
                  </Group>

                  <Group justify="space-between">
                    <Text fw={500}>Phone Number:</Text>
                    <Text>{registration.phone_number}</Text>
                  </Group>

                  <Group justify="space-between">
                    <Text fw={500}>Institution:</Text>
                    <Text>{registration.institution}</Text>
                  </Group>

                  <Group justify="space-between">
                    <Text fw={500}>Country:</Text>
                    <Text>{registration.country}</Text>
                  </Group>

                  <Title order={4} mt="md">Paper Information</Title>
                  <Divider />

                  <Group justify="space-between">
                    <Text fw={500}>Paper ID:</Text>
                    <Text>{registration.paper_id || '-'}</Text>
                  </Group>

                  <Group justify="space-between">
                    <Text fw={500}>Paper Title:</Text>
                    <Text style={{ textAlign: 'right', maxWidth: '60%' }}>
                      {registration.paper_title}
                    </Text>
                  </Group>

                  {registration.full_paper_path && (
                    <Group justify="space-between">
                      <Text fw={500}>Full Paper:</Text>
                      <Button
                        size="xs"
                        leftSection={<IconFileText size={14} />}
                        onClick={handleDownloadPaper}
                      >
                        Download Paper
                      </Button>
                    </Group>
                  )}

                  <Title order={4} mt="md">Payment Information</Title>
                  <Divider />

                  <Group justify="space-between">
                    <Text fw={500}>Payment Method:</Text>
                    <Text>{getPaymentMethodText(registration.payment_method)}</Text>
                  </Group>

                  <Group justify="space-between">
                    <Text fw={500}>Amount:</Text>
                    <Text fw={700} c="blue">{formatCurrency(registration.paid_fee, 'usd')}</Text>
                  </Group>

                  <Group justify="space-between">
                    <Text fw={500}>Payment Status:</Text>
                    {getStatusBadge(registration.payment_status)}
                  </Group>

                  {registration.payment_proof_path && (
                    <Group justify="space-between">
                      <Text fw={500}>Payment Proof:</Text>
                      <Button
                        size="xs"
                        leftSection={<IconDownload size={14} />}
                        onClick={handleDownloadPaymentProof}
                      >
                        Download Proof
                      </Button>
                    </Group>
                  )}

                  {registration.payment_status === 'paid' && (
                    <Group justify="space-between">
                      <Text fw={500}>Receipt:</Text>
                      <Button
                        size="xs"
                        leftSection={<IconReceipt size={14} />}
                        onClick={handleDownloadReceipt}
                      >
                        Download Receipt
                      </Button>
                    </Group>
                  )}
                </Stack>
              </Card>
            </Grid.Col>

            <Grid.Col span={{ base: 12, md: 4 }}>
              <Card withBorder>
                <Stack gap="md">
                  <Title order={4}>Update Payment Status</Title>
                  <Divider />
                  <Select
                    label="Payment Status"
                    data={[
                      { value: 'pending_payment', label: 'Pending' },
                      { value: 'paid', label: 'Paid' },
                      { value: 'cancelled', label: 'Cancelled' },
                      { value: 'refunded', label: 'Refunded' },
                    ]}
                    value={data.payment_status}
                    onChange={(value) => setData('payment_status', value || 'pending_payment')}
                  />

                  <Button
                    fullWidth
                    onClick={handleStatusUpdate}
                    loading={processing || isUpdatingStatus}
                    disabled={data.payment_status === registration.payment_status}
                  >
                    Update Status
                  </Button>

                  <Divider />

                  <div>
                    <Text size="sm" c="dimmed">Registered At</Text>
                    <Text size="sm">{new Date(registration.created_at).toLocaleString()}</Text>
                  </div>

                  <div>
                    <Text size="sm" c="dimmed">Last Updated</Text>
                    <Text size="sm">{new Date(registration.updated_at).toLocaleString()}</Text>
                  </div>
                </Stack>
              </Card>
            </Grid.Col>
          </Grid>
        </Stack>
      </Container>
    </MainLayout>
  );
}

export default JoivArticleShow;
