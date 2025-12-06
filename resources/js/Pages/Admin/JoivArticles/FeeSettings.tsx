import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { Button, Card, Flex, Grid, NumberInput, Paper, Stack, Table, Text, Textarea, Title } from '@mantine/core';
import { notifications } from '@mantine/notifications';
import { IconArrowLeft, IconCheck, IconX } from '@tabler/icons-react';
import { route } from 'ziggy-js';
import MainLayout from '../../../Layout/MainLayout';
import { JoivRegistrationFee, PageProps } from '../../../types';
import { formatDate } from '../../../utils';
import CurrentFee from './CurrentFee';


interface FeeSettingsProps {
  auth: {
    user: PageProps['auth']['user'];
  };
  currentFee: JoivRegistrationFee | null;
  feeHistory: {
    data: JoivRegistrationFee[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}

export default function FeeSettings({ currentFee, feeHistory }: Readonly<FeeSettingsProps>) {
  const [usdAmount, setUsdAmount] = useState<number | string>('');
  const [idrAmount, setIdrAmount] = useState<number | string>('');
  const [notes, setNotes] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);

    router.post(
      route('joiv-articles.fee-settings.update'),
      {
        usd_amount: usdAmount,
        idr_amount: idrAmount,
        notes: notes || null,
      },
      {
        onSuccess: () => {
          notifications.show({
            title: 'Success',
            message: 'Registration fee has been updated successfully',
            color: 'green',
            icon: <IconCheck size={18} />,
          });
          setUsdAmount('');
          setIdrAmount('');
          setNotes('');
        },
        onError: (errors) => {
          notifications.show({
            title: 'Error',
            message: errors.usd_amount || errors.idr_amount || 'Failed to update fee settings',
            color: 'red',
            icon: <IconX size={18} />,
          });
        },
        onFinish: () => setIsSubmitting(false),
      }
    );
  };

  return (
    <MainLayout title="JOIV Registration Fee Settings">
      <Head title="JOIV Registration Fee Settings" />

      <Stack gap="lg">
        <Flex align="center" justify="space-between">
          <Title order={2}>JOIV Registration Fee Settings</Title>
          <Button
            variant="subtle"
            leftSection={<IconArrowLeft size={16} />}
            onClick={() => router.visit('/joiv-articles')}
          >
            Back
          </Button>
        </Flex>
        {/* Current Fee Display */}
        <CurrentFee currentFee={currentFee} />
        {/* Update Fee Form */}
        <Card shadow="sm" padding="lg" radius="md" withBorder>
          <Title order={4} mb="md">Update Registration Fee</Title>
          <form onSubmit={handleSubmit}>
            <Stack gap="md">
              <Grid>
                <Grid.Col span={6}>
                  <NumberInput
                    label="USD Amount (International)"
                    placeholder="Enter USD amount"
                    value={usdAmount}
                    onChange={setUsdAmount}
                    required
                    min={0}
                    decimalScale={2}
                    fixedDecimalScale
                    prefix="$ "
                    allowNegative={false}
                    description="Fee for international participants"
                  />
                </Grid.Col>
                <Grid.Col span={6}>
                  <NumberInput
                    label="IDR Amount (Indonesia)"
                    placeholder="Enter IDR amount"
                    value={idrAmount}
                    onChange={setIdrAmount}
                    required
                    min={0}
                    decimalScale={2}
                    fixedDecimalScale
                    prefix="Rp "
                    allowNegative={false}
                    thousandSeparator="."
                    decimalSeparator=","
                    description="Fee for Indonesian participants"
                  />
                </Grid.Col>
              </Grid>
              <Textarea
                label="Notes (Optional)"
                placeholder="Add notes about this fee change"
                value={notes}
                onChange={(e) => setNotes(e.currentTarget.value)}
                minRows={3}
              />
              <Button
                type="submit"
                loading={isSubmitting}
                disabled={!usdAmount || !idrAmount || isSubmitting}
              >
                Update Fee
              </Button>
            </Stack>
          </form>
        </Card>

        {/* Fee History Table */}
        <Card shadow="sm" padding="lg" radius="md" withBorder>
          <Title order={4} mb="md">Fee History</Title>
          <Paper withBorder>
            <Table striped highlightOnHover>
              <Table.Thead>
                <Table.Tr>
                  <Table.Th>Date</Table.Th>
                  <Table.Th>USD Amount</Table.Th>
                  <Table.Th>IDR Amount</Table.Th>
                  <Table.Th>Changed By</Table.Th>
                  <Table.Th>Notes</Table.Th>
                </Table.Tr>
              </Table.Thead>
              <Table.Tbody>
                {feeHistory.data.length > 0 ? (
                  feeHistory.data.map((fee) => (
                    <Table.Tr key={fee.id}>
                      <Table.Td>{formatDate(fee.created_at)}</Table.Td>
                      <Table.Td>
                        <Text fw={500}>
                          $ {Number.parseFloat(fee.usd_amount).toFixed(2)}
                        </Text>
                      </Table.Td>
                      <Table.Td>
                        <Text fw={500}>
                          Rp {Number.parseFloat(fee.idr_amount).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                        </Text>
                      </Table.Td>
                      <Table.Td>{fee.creator.full_name}</Table.Td>
                      <Table.Td>
                        {fee.notes || <Text c="dimmed">-</Text>}
                      </Table.Td>
                    </Table.Tr>
                  ))
                ) : (
                  <Table.Tr>
                    <Table.Td colSpan={5}>
                      <Text ta="center" c="dimmed">No fee history available</Text>
                    </Table.Td>
                  </Table.Tr>
                )}
              </Table.Tbody>
            </Table>
          </Paper>

          {/* Pagination Info */}
          {feeHistory.total > feeHistory.per_page && (
            <Text size="sm" c="dimmed" mt="md" ta="right">
              Page {feeHistory.current_page} of {feeHistory.last_page}
              ({feeHistory.total} total records)
            </Text>
          )}
        </Card>
      </Stack>
    </MainLayout>
  );
}
