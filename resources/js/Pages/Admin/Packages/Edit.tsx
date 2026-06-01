import { router, useForm, usePage } from '@inertiajs/react';
import { Button, Card, Container, Group, Stack, Text, TextInput, Title, NumberInput, Select, Table, Modal, Textarea } from '@mantine/core';
import { notifications } from '@mantine/notifications';
import { IconArrowLeft } from '@tabler/icons-react';
import React from 'react';
import { route } from 'ziggy-js';
import MainLayout from '../../../Layout/MainLayout';

function PackageEdit() {
  const { package: pkg } = usePage<any>().props;

  const { data, setData, errors, post, processing } = useForm({
    name: pkg.name || '',
    price_idr: pkg.price_idr || 0,
    price_usd: pkg.price_usd || 0,
    status: pkg.status || 'active',
    duration: pkg.duration || 0,
  });

  // package benefits form
  const availableBenefits = (usePage<any>().props.availableMembershipBenefits || []);
  const packageBenefitsList = (pkg.packageBenefits || pkg.package_benefits || []);

  const { data: pbData, setData: setPbData, post: postPb, processing: pbProcessing } = useForm({
    membership_benefit_id: '',
    value_type: '',
    package_id: pkg.id,
    value: 0,
    max_value: 0,
    quota: 0,
    notes: '',
  });

  const [isModalOpen, setIsModalOpen] = React.useState(false);

  function handleAddBenefit(e: any) {
    e.preventDefault();
    // ensure package_id is included in the form data
    setPbData('package_id', pkg.id);

    postPb(route('package-benefits.store'), {
      onSuccess: () => {
        notifications.show({ message: 'Benefit added to package', color: 'green' });
        setIsModalOpen(false);
        router.reload();
      },
    });
  }

  function handleSubmit(e: any) {
    e.preventDefault();
    post(route('packages.update', pkg.id), {
      onSuccess: () => {
        notifications.show({ message: 'Package updated successfully!', color: 'green' });
        router.visit(route('packages.index'));
      },
    });
  }

  return (
    <MainLayout>
      <Container size="md" py="xl">
        <Stack gap="lg">
          <Group justify="space-between">
            <div>
              <Title order={2}>Edit Package</Title>
              <Text c="dimmed" size="sm">Update package details</Text>
            </div>
            <Button
              variant="subtle"
              leftSection={<IconArrowLeft size={16} />}
              onClick={() => router.visit(route('packages.index'))}
            >
              Back
            </Button>
          </Group>

          <Card withBorder>
            <form onSubmit={handleSubmit}>
              <Stack gap="md">
                <TextInput
                  label="Name"
                  value={data.name}
                  onChange={(e) => setData('name', e.target.value)}
                  error={errors.name}
                  required
                />

                <NumberInput
                  label="Price (IDR)"
                  value={data.price_idr}
                  onChange={(val) => setData('price_idr', val || 0)}
                  error={errors.price_idr}
                />

                <NumberInput
                  label="Price (USD)"
                  value={data.price_usd}
                  onChange={(val) => setData('price_usd', val || 0)}
                  error={errors.price_usd}
                />

                <NumberInput
                  label="Duration (days)"
                  value={data.duration}
                  onChange={(val) => setData('duration', val || 0)}
                  error={errors.duration}
                />

                <Select
                  label="Status"
                  data={[{ value: 'active', label: 'Active' }, { value: 'inactive', label: 'Inactive' }]}
                  value={data.status}
                  onChange={(val) => setData('status', val || 'active')}
                />

                <Group justify="flex-end" pt="md">
                  <Button variant="subtle" onClick={() => router.visit(route('packages.index'))}>Cancel</Button>
                  <Button type="submit" loading={processing}>Update Package</Button>
                </Group>
              </Stack>
            </form>
          </Card>

          <Card withBorder>
            <Group justify="space-between">
              <div>
                <Title order={4}>Package Benefits</Title>
                <Text c="dimmed" size="sm">Assign membership benefits to this package</Text>
              </div>
              <Button onClick={() => setIsModalOpen(true)}>Add Benefit</Button>
            </Group>

            <Table mt="md" striped highlightOnHover>
              <Table.Thead>
                <Table.Tr>
                  <Table.Th>Benefit</Table.Th>
                  <Table.Th>Type</Table.Th>
                  <Table.Th>Value</Table.Th>
                  <Table.Th>Max</Table.Th>
                  <Table.Th>Quota</Table.Th>
                  <Table.Th>Notes</Table.Th>
                </Table.Tr>
              </Table.Thead>

              <Table.Tbody>
                {packageBenefitsList.length > 0 ? (
                  packageBenefitsList.map((b: any) => (
                    <Table.Tr key={b.id}>
                      <Table.Td>{b.membership_benefit?.name || b.membershipBenefit?.name}</Table.Td>
                      <Table.Td>{b.value_type || <Text c="dimmed">-</Text>}</Table.Td>
                      <Table.Td>
                        {(() => {
                          if (b.value_type === 'percentage' && b.value != null) {
                            return <Text>{Number(b.value).toFixed(0)}%</Text>;
                          }

                          if ((b.value_type === 'fixed' || b.value_type === 'item') && b.value != null) {
                            return <Text>{Number.parseFloat(b.value).toFixed(2)}</Text>;
                          }

                          return (b.value != null && b.value !== '') ? <Text>{String(b.value)}</Text> : <Text c="dimmed">-</Text>;
                        })()}
                      </Table.Td>
                      <Table.Td>
                        {(b.max_value != null && b.max_value !== '') ? <Text>{Number.parseFloat(b.max_value).toFixed(2)}</Text> : <Text c="dimmed">-</Text>}
                      </Table.Td>

                      <Table.Td>
                        {(b.quota != null && b.quota !== '') ? <Text>{b.quota}</Text> : <Text c="dimmed">-</Text>}
                      </Table.Td>

                      <Table.Td>{b.notes || <Text c="dimmed">-</Text>}</Table.Td>
                    </Table.Tr>
                  ))
                ) : (
                  <Table.Tr>
                    <Table.Td colSpan={6}><Text c="dimmed">No benefits assigned.</Text></Table.Td>
                  </Table.Tr>
                )}
              </Table.Tbody>
            </Table>

            <Modal opened={isModalOpen} onClose={() => setIsModalOpen(false)} title="Add Benefit to Package">
              <form onSubmit={handleAddBenefit}>
                <Stack>
                  <Select
                    label="Benefit"
                    data={availableBenefits.map((ab: any) => ({ value: String(ab.id), label: `${ab.code} — ${ab.name}` }))}
                    value={pbData.membership_benefit_id}
                    onChange={(val) => setPbData('membership_benefit_id', val || '')}
                  />

                  <Select
                    label="Value Type"
                    data={[
                      { value: 'percentage', label: 'Percentage' },
                      { value: 'fixed', label: 'Fixed' },
                      { value: 'item', label: 'Item' },
                      { value: 'quota', label: 'Quota' },
                    ]}
                    value={pbData.value_type}
                    onChange={(val) => setPbData('value_type', val || '')}
                  />

                  <NumberInput label="Value" value={pbData.value} onChange={(val) => setPbData('value', Number(val || 0))} />
                  <NumberInput label="Max Value" value={pbData.max_value} onChange={(val) => setPbData('max_value', Number(val || 0))} />
                  <NumberInput label="Quota" value={pbData.quota} onChange={(val) => setPbData('quota', Number(val || 0))} />
                  <Textarea label="Notes" value={pbData.notes} onChange={(e) => setPbData('notes', e.target.value)} />

                  <Group justify="flex-end">
                    <Button variant="subtle" onClick={() => setIsModalOpen(false)}>Cancel</Button>
                    <Button type="submit" loading={pbProcessing}>Add</Button>
                  </Group>
                </Stack>
              </form>
            </Modal>
          </Card>
        </Stack>
      </Container>
    </MainLayout>
  );
}

export default PackageEdit;
