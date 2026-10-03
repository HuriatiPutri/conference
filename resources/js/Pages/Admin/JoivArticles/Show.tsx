import { router, useForm, usePage } from '@inertiajs/react';
import { Button, Card, Container, Divider, Group, Stack, Text, Title, Grid, Select } from '@mantine/core';
import { IconDownload, IconFileText, IconReceipt } from '@tabler/icons-react';
import React, { useState } from 'react';
import { route } from 'ziggy-js';
import MainLayout from '../../../Layout/MainLayout';
import { formatCurrency } from '../../../utils';
import { getStatusBadge } from '../../../Components/BadgeStatus';

import { JoivRegistration } from '../../../types';

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

                  {/* Original Price */}
                  <Group justify="space-between">
                    <Text fw={500}>Original Price:</Text>
                    <Text fw={500}>
                      {formatCurrency(
                        Number(registration.original_fee || (Number(registration.paid_fee) + Number(registration.discount_amount || 0))),
                        registration.country === 'ID' ? 'idr' : 'usd'
                      )}
                    </Text>
                  </Group>

                  {/* Voucher */}
                  {(registration.voucher || registration.voucher_code) && (
                    <Group justify="space-between">
                      <Text fw={500}>Voucher:</Text>
                      <Group gap="xs">
                        <Text fw={600} c="teal">{registration.voucher?.code || registration.voucher_code}</Text>
                        {registration.voucher?.discount_type === 'percentage' && (
                          <Text size="sm" c="teal">({registration.voucher.discount_value}% OFF)</Text>
                        )}
                        {registration.voucher?.discount_type === 'fixed' && (
                          <Text size="sm" c="teal">
                            (-{formatCurrency(registration.country === 'ID' ? registration.voucher.discount_value : (registration.voucher.discount_value_usd || registration.voucher.discount_value), registration.country === 'ID' ? 'idr' : 'usd')})
                          </Text>
                        )}
                      </Group>
                    </Group>
                  )}

                  {/* Membership Discount (hanya benefit potongan harga) */}
                  {registration.benefit_usages?.filter(bu => (bu.benefit_type === 'discount' || bu.benefit_type === 'free_registration') && Number(bu.consumed_value) > 0).map((bu, idx) => (
                    <Group key={idx} justify="space-between">
                      <Text fw={500}>Member Discount ({bu.membership_benefit?.name || 'Discount'}):</Text>
                      <Text size="sm" c="indigo" fw={600}>
                        -{formatCurrency(bu.consumed_value, registration.country === 'ID' ? 'idr' : 'usd')}
                      </Text>
                    </Group>
                  ))}

                  {/* Total Discount jika ada */}
                  {Number(registration.discount_amount || 0) > 0 && (
                    <Group justify="space-between">
                      <Text fw={500}>Total Discount:</Text>
                      <Text fw={600} c="red">
                        -{formatCurrency(Number(registration.discount_amount), registration.country === 'ID' ? 'idr' : 'usd')}
                      </Text>
                    </Group>
                  )}

                  {/* Final Paid Fee */}
                  <Group justify="space-between">
                    <Text fw={500}>Paid Fee (Final):</Text>
                    <Text fw={700} c="blue" size="lg">
                      {formatCurrency(registration.paid_fee, registration.country === 'ID' ? 'idr' : 'usd')}
                    </Text>
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
                      { value: 'expired', label: 'Expired' },
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
