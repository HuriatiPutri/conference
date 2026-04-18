import React from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import {
  Container,
  Card,
  Title,
  Text,
  Button,
  Stack,
  Group,
  Table,
  Badge,
  ActionIcon,
  Menu,
  Modal
} from '@mantine/core';
import { IconDotsVertical, IconCheck, IconX, IconEye } from '@tabler/icons-react';
import MainLayout from '../../../Layout/MainLayout';
import { formatCurrency } from '../../../utils';
import dayjs from 'dayjs';

interface Invoice {
  id: string;
  status: string;
  payment_method: string;
  amount: number;
  payment_proof_path: string | null;
}

interface Package {
  name: string;
  price: number;
}

interface Membership {
  id: number;
  public_id: string;
  first_name: string;
  last_name: string;
  email: string;
  status: string;
  package: Package;
  invoices: Invoice[];
  start_date: string;
  end_date: string;
}

interface Props {
  memberships: {
    data: Membership[];
    current_page: number;
    last_page: number;
  };
}

export default function MembershipIndex({ memberships }: Props) {
  const [selectedInvoice, setSelectedInvoice] = React.useState<{invoiceId: string, membershipId: number, proof: string | null} | null>(null);

  const handleVerify = (membershipId: number, invoiceId: string, status: 'completed' | 'failed') => {
    if (confirm(`Are you sure you want to mark this payment as ${status}?`)) {
      router.patch(`/memberships/${membershipId}/payment-status/${invoiceId}`, { status });
      setSelectedInvoice(null);
    }
  };

  return (
    <Container fluid>
      <Head title="Membership Management" />
      <Stack gap="lg">
        <Group justify="space-between">
          <div>
            <Title order={2}>Membership Management</Title>
            <Text c="dimmed">Manage community memberships and verify payments</Text>
          </div>
        </Group>

        <Card padding="lg" radius="md" withBorder>
          <Table striped highlightOnHover>
            <Table.Thead>
              <Table.Tr>
                <Table.Th>Name</Table.Th>
                <Table.Th>Package</Table.Th>
                <Table.Th>Payment Status</Table.Th>
                <Table.Th>Account Status</Table.Th>
                <Table.Th>Actions</Table.Th>
              </Table.Tr>
            </Table.Thead>
            <Table.Tbody>
              {memberships.data.map((membership) => {
                const latestInvoice = membership.invoices[membership.invoices.length - 1];
                let paymentStatusColor = 'gray';
                if (latestInvoice?.status === 'completed') paymentStatusColor = 'green';
                if (latestInvoice?.status === 'pending') paymentStatusColor = 'orange';
                if (latestInvoice?.status === 'failed') paymentStatusColor = 'red';

                return (
                  <Table.Tr key={membership.id}>
                    <Table.Td>
                      <Text fw={500}>{membership.first_name} {membership.last_name}</Text>
                      <Text size="xs" c="dimmed">{membership.email}</Text>
                    </Table.Td>
                    <Table.Td>
                      <Text size="sm">{membership.package?.name}</Text>
                      {latestInvoice && (
                        <Text size="xs" c="dimmed">
                          {formatCurrency(latestInvoice.amount, 'idr')} via {latestInvoice.payment_method.replace('_', ' ')}
                        </Text>
                      )}
                    </Table.Td>
                    <Table.Td>
                      {latestInvoice ? (
                        <Badge color={paymentStatusColor} variant="light">
                          {latestInvoice.status}
                        </Badge>
                      ) : (
                        <Badge color="gray" variant="light">No Payment</Badge>
                      )}
                    </Table.Td>
                    <Table.Td>
                      <Badge color={membership.status === 'active' ? 'blue' : 'gray'} variant="filled">
                        {membership.status}
                      </Badge>
                      {membership.status === 'active' && (
                        <Text size="xs" c="dimmed" mt={4}>
                          Valid until: {dayjs(membership.end_date).format('DD MMM YYYY')}
                        </Text>
                      )}
                    </Table.Td>
                    <Table.Td>
                      <Menu position="bottom-end" shadow="md">
                        <Menu.Target>
                          <ActionIcon variant="subtle"><IconDotsVertical size={16} /></ActionIcon>
                        </Menu.Target>
                        <Menu.Dropdown>
                          {latestInvoice?.payment_method === 'transfer_bank' && latestInvoice?.status === 'pending' && (
                            <Menu.Item
                              leftSection={<IconEye size={14} />}
                              onClick={() => setSelectedInvoice({ 
                                invoiceId: latestInvoice.id, 
                                membershipId: membership.id,
                                proof: latestInvoice.payment_proof_path 
                              })}
                            >
                              Verify Payment Proof
                            </Menu.Item>
                          )}
                        </Menu.Dropdown>
                      </Menu>
                    </Table.Td>
                  </Table.Tr>
                );
              })}
              {memberships.data.length === 0 && (
                <Table.Tr>
                  <Table.Td colSpan={5} ta="center">
                    <Text c="dimmed" py="xl">No memberships found</Text>
                  </Table.Td>
                </Table.Tr>
              )}
            </Table.Tbody>
          </Table>
        </Card>
      </Stack>

      {/* Verification Modal */}
      <Modal 
        opened={!!selectedInvoice} 
        onClose={() => setSelectedInvoice(null)}
        title={<Title order={4}>Verify Payment Proof</Title>}
        size="lg"
      >
        {selectedInvoice && (
          <Stack>
            {selectedInvoice.proof ? (
              <img 
                src={`/storage/${selectedInvoice.proof}`} 
                alt="Payment Proof" 
                style={{ maxWidth: '100%', maxHeight: '400px', objectFit: 'contain' }} 
              />
            ) : (
              <Text c="dimmed" ta="center">No payment proof uploaded</Text>
            )}
            
            <Group justify="flex-end" mt="md">
              <Button 
                color="red" 
                variant="outline" 
                leftSection={<IconX size={16} />}
                onClick={() => handleVerify(selectedInvoice.membershipId, selectedInvoice.invoiceId, 'failed')}
              >
                Reject
              </Button>
              <Button 
                color="green" 
                leftSection={<IconCheck size={16} />}
                onClick={() => handleVerify(selectedInvoice.membershipId, selectedInvoice.invoiceId, 'completed')}
              >
                Approve & Activate
              </Button>
            </Group>
          </Stack>
        )}
      </Modal>
    </Container>
  );
}

MembershipIndex.layout = (page: React.ReactNode) => <MainLayout title="Memberships">{page}</MainLayout>;
