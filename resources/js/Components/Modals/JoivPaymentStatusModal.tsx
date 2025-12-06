import React from 'react';
import { Modal, Button, Select, Stack, Text, Group } from '@mantine/core';
import { useForm } from '@inertiajs/react';
import { notifications } from '@mantine/notifications';

interface JoivRegistration {
  id: number;
  public_id: string;
  first_name: string;
  last_name: string;
  email_address: string;
  payment_status: string;
  payment_method: string | null;
}

interface JoivPaymentStatusModalProps {
  opened: boolean;
  onClose: () => void;
  registration: JoivRegistration | null;
}

const PAYMENT_STATUS_OPTIONS = [
  { value: 'pending_payment', label: 'Pending' },
  { value: 'paid', label: 'Paid' },
  { value: 'cancelled', label: 'Cancelled' },
  { value: 'refunded', label: 'Refunded' },
];

export function JoivPaymentStatusModal({ opened, onClose, registration }: JoivPaymentStatusModalProps) {
  const { data, setData, patch, processing, errors, reset } = useForm({
    payment_status: registration?.payment_status || 'pending_payment',
  });

  React.useEffect(() => {
    if (registration) {
      setData('payment_status', registration.payment_status);
    }
  }, [registration]);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();

    if (!registration) return;

    patch(`/joiv-articles/${registration.id}/payment-status`, {
      onSuccess: () => {
        notifications.show({
          title: 'Success',
          message: 'Payment status updated successfully',
          color: 'green',
        });
        onClose();
        reset();
      },
      onError: () => {
        notifications.show({
          title: 'Error',
          message: 'Failed to update payment status',
          color: 'red',
        });
      },
    });
  };

  const getPaymentMethodText = (method: string | null) => {
    if (!method) return '-';
    return method === 'transfer_bank' ? 'Bank Transfer' : 'PayPal';
  };

  if (!registration) return null;

  return (
    <Modal
      opened={opened}
      onClose={onClose}
      title="Update Payment Status"
      size="md"
    >
      <form onSubmit={handleSubmit}>
        <Stack gap="md">
          <div>
            <Text size="sm" fw={500} mb="xs">Registrant:</Text>
            <Text size="sm">{registration.first_name} {registration.last_name}</Text>
          </div>

          <div>
            <Text size="sm" fw={500} mb="xs">Email:</Text>
            <Text size="sm">{registration.email_address}</Text>
          </div>

          <div>
            <Text size="sm" fw={500} mb="xs">Payment Method:</Text>
            <Text size="sm">{getPaymentMethodText(registration.payment_method)}</Text>
          </div>

          <Select
            label="Payment Status"
            placeholder="Select payment status"
            data={PAYMENT_STATUS_OPTIONS}
            value={data.payment_status}
            onChange={(value) => setData('payment_status', value || 'pending_payment')}
            error={errors.payment_status}
            required
          />

          <Group justify="flex-end" mt="md">
            <Button variant="light" onClick={onClose} disabled={processing}>
              Cancel
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
