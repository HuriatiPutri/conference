import React from 'react';
import { Modal, Button, Select, Stack, Text, Group } from '@mantine/core';
import { useForm } from '@inertiajs/react';
import { notifications } from '@mantine/notifications';
import { Audiences } from '../../types';
import { PAYMENT_STATUS } from '../../Constants';

interface PaymentStatusModalProps {
  opened: boolean;
  onClose: () => void;
  audience: Audiences | null;
}

const PAYMENT_STATUS_OPTIONS = [
  { value: PAYMENT_STATUS.PENDING_PAYMENT, label: 'Pending' },
  { value: PAYMENT_STATUS.PAID, label: 'Paid' },
  { value: PAYMENT_STATUS.REFUNED, label: 'Refund' },
  { value: PAYMENT_STATUS.CANCELLED, label: 'Cancelled' },
];

export function PaymentStatusModal({ opened, onClose, audience }: PaymentStatusModalProps) {
  const { data, setData, patch, processing, errors } = useForm({
    payment_status: audience?.payment_status || 'pending',
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();

    if (!audience) return;

    patch(`/audiences/${audience.id}/payment-status`, {
      onSuccess: () => {
        notifications.show({
          title: 'Berhasil',
          message: 'Status pembayaran berhasil diupdate',
          color: 'green',
        });
        onClose();
      },
      onError: () => {
        notifications.show({
          title: 'Error',
          message: 'Gagal mengupdate status pembayaran',
          color: 'red',
        });
      },
    });
  };

  if (!audience) return null;

  return (
    <Modal
      opened={opened}
      onClose={onClose}
      title="Update Status Pembayaran"
      size="md"
    >
      <form onSubmit={handleSubmit}>
        <Stack gap="md">
          <div>
            <Text size="sm" fw={500} mb="xs">Audience:</Text>
            <Text size="sm">{audience.first_name} {audience.last_name}</Text>
          </div>

          <div>
            <Text size="sm" fw={500} mb="xs">Email:</Text>
            <Text size="sm">{audience.email}</Text>
          </div>

          <div>
            <Text size="sm" fw={500} mb="xs">Metode Pembayaran:</Text>
            <Text size="sm">Transfer Bank</Text>
          </div>

          <Select
            label="Status Pembayaran"
            placeholder="Pilih status pembayaran"
            data={PAYMENT_STATUS_OPTIONS}
            value={data.payment_status}
            onChange={(value) => setData('payment_status', value || 'pending')}
            error={errors.payment_status}
            required
          />

          <Group justify="flex-end" mt="md">
            <Button variant="light" onClick={onClose} disabled={processing}>
              Batal
            </Button>
            <Button type="submit" loading={processing}>
              Update Status
            </Button>
          </Group>
        </Stack>
      </form>
    </Modal>
  );
}