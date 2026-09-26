import { router, useForm, usePage } from '@inertiajs/react';
import {
  ActionIcon,
  Badge,
  Button,
  Card,
  Container,
  Divider,
  Group,
  Modal,
  NumberInput,
  Paper,
  Select,
  Stack,
  Table,
  Text,
  TextInput,
  Textarea,
  Title,
  Tooltip,
} from '@mantine/core';
import { notifications } from '@mantine/notifications';
import { IconArrowLeft, IconPlus, IconTrash } from '@tabler/icons-react';
import React, { useMemo, useState } from 'react';
import { route } from 'ziggy-js';
import MainLayout from '../../../Layout/MainLayout';

interface MembershipBenefit {
  id: number;
  code: string;
  name: string;
  benefit_type: 'discount' | 'souvenir' | 'opportunity' | 'voucher' | string;
  description?: string | null;
}

interface PackageBenefit {
  id: number;
  package_id: number;
  membership_benefit_id: number;
  value_type: string | null;
  value: number | string | null;
  max_value: number | string | null;
  quota: number | string | null;
  notes: string | null;
  membership_benefit?: MembershipBenefit;
  membershipBenefit?: MembershipBenefit;
}

interface Package {
  id: number;
  name: string;
  price_idr: number;
  price_usd: number;
  status: string;
  duration: number;
  packageBenefits?: PackageBenefit[];
  package_benefits?: PackageBenefit[];
}

interface PageProps {
  package: Package;
  availableMembershipBenefits: MembershipBenefit[];
  [key: string]: any;
}

const getBenefitTypeBadgeColor = (type?: string) => {
  switch (type) {
    case 'discount':
      return 'green';
    case 'souvenir':
    case 'item':
      return 'grape';
    case 'opportunity':
      return 'indigo';
    case 'voucher':
      return 'orange';
    default:
      return 'gray';
  }
};

function PackageEdit() {
  const { package: pkg, availableMembershipBenefits: availableBenefits = [] } = usePage<PageProps>().props;

  const { data, setData, errors, post, processing } = useForm({
    name: pkg.name || '',
    price_idr: pkg.price_idr || 0,
    price_usd: pkg.price_usd || 0,
    status: pkg.status || 'active',
    duration: pkg.duration || 0,
  });

  const packageBenefitsList: PackageBenefit[] = useMemo(
    () => pkg.packageBenefits || pkg.package_benefits || [],
    [pkg]
  );

  const [isModalOpen, setIsModalOpen] = useState(false);

  const {
    data: pbData,
    setData: setPbData,
    post: postPb,
    processing: pbProcessing,
    reset: resetPbForm,
  } = useForm({
    package_id: pkg.id,
    membership_benefit_id: '',
    value_type: 'percentage',
    value: 0,
    max_value: 0,
    quota: 0,
    notes: '',
  });

  const selectedBenefit = useMemo(() => {
    return availableBenefits.find(
      (b) => String(b.id) === String(pbData.membership_benefit_id)
    );
  }, [availableBenefits, pbData.membership_benefit_id]);

  const handleBenefitChange = (val: string | null) => {
    const benefit = availableBenefits.find((b) => String(b.id) === String(val));
    let defaultValueType = 'percentage';
    let defaultQuota = 0;

    if (benefit?.benefit_type === 'souvenir') {
      defaultValueType = 'item';
      defaultQuota = 1;
    } else if (benefit?.benefit_type === 'opportunity') {
      defaultValueType = 'quota';
      defaultQuota = 1;
    } else if (benefit?.benefit_type === 'voucher') {
      defaultValueType = 'fixed';
      defaultQuota = 1;
    } else if (benefit?.benefit_type === 'discount') {
      defaultValueType = 'percentage';
      defaultQuota = 0;
    }

    setPbData({
      package_id: pkg.id,
      membership_benefit_id: val || '',
      value_type: defaultValueType,
      value: 0,
      max_value: 0,
      quota: defaultQuota,
      notes: '',
    });
  };

  const handleOpenModal = () => {
    resetPbForm();
    setIsModalOpen(true);
  };

  const handleAddBenefit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!pbData.membership_benefit_id) {
      notifications.show({ message: 'Please select a benefit', color: 'red' });
      return;
    }

    postPb(route('package-benefits.store'), {
      onSuccess: () => {
        notifications.show({ message: 'Benefit added to package', color: 'green' });
        setIsModalOpen(false);
        resetPbForm();
        router.reload();
      },
    });
  };

  const handleDeleteBenefit = (benefitId: number) => {
    if (confirm('Are you sure you want to remove this benefit from the package?')) {
      router.delete(route('package-benefits.destroy', benefitId), {
        preserveScroll: true,
        onSuccess: () => {
          notifications.show({ message: 'Benefit removed from package', color: 'green' });
          router.reload();
        },
      });
    }
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post(route('packages.update', pkg.id), {
      onSuccess: () => {
        notifications.show({ message: 'Package updated successfully!', color: 'green' });
        router.visit(route('packages.index'));
      },
    });
  };

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

          {/* Package Details Form */}
          <Card withBorder radius="md">
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
                  onChange={(val) => setData('price_idr', Number(val) || 0)}
                  error={errors.price_idr}
                />

                <NumberInput
                  label="Price (USD)"
                  value={data.price_usd}
                  onChange={(val) => setData('price_usd', Number(val) || 0)}
                  error={errors.price_usd}
                />

                <NumberInput
                  label="Duration (days)"
                  value={data.duration}
                  onChange={(val) => setData('duration', Number(val) || 0)}
                  error={errors.duration}
                />

                <Select
                  label="Status"
                  data={[
                    { value: 'active', label: 'Active' },
                    { value: 'inactive', label: 'Inactive' },
                  ]}
                  value={data.status}
                  onChange={(val) => setData('status', val || 'active')}
                />

                <Group justify="flex-end" pt="md">
                  <Button variant="subtle" onClick={() => router.visit(route('packages.index'))}>
                    Cancel
                  </Button>
                  <Button type="submit" loading={processing}>
                    Update Package
                  </Button>
                </Group>
              </Stack>
            </form>
          </Card>

          {/* Package Benefits Section */}
          <Card withBorder radius="md">
            <Group justify="space-between" mb="sm">
              <div>
                <Title order={4}>Package Benefits</Title>
                <Text c="dimmed" size="sm">
                  Assign membership benefits to this package according to their type
                </Text>
              </div>
              <Button leftSection={<IconPlus size={16} />} onClick={handleOpenModal}>
                Add Benefit
              </Button>
            </Group>

            <Table mt="md" striped highlightOnHover verticalSpacing="sm">
              <Table.Thead>
                <Table.Tr>
                  <Table.Th>Benefit</Table.Th>
                  <Table.Th>Type</Table.Th>
                  <Table.Th>Value</Table.Th>
                  <Table.Th>Max</Table.Th>
                  <Table.Th>Quota</Table.Th>
                  <Table.Th>Notes</Table.Th>
                  <Table.Th style={{ textAlign: 'center' }}>Action</Table.Th>
                </Table.Tr>
              </Table.Thead>

              <Table.Tbody>
                {packageBenefitsList.length > 0 ? (
                  packageBenefitsList.map((b) => {
                    const benefit = b.membership_benefit || b.membershipBenefit;
                    const benefitType = benefit?.benefit_type;

                    return (
                      <Table.Tr key={b.id}>
                        <Table.Td>
                          <Stack gap={2}>
                            <Text fw={500} size="sm">
                              {benefit?.name || '-'}
                            </Text>
                            {benefit?.code && (
                              <Badge size="xs" variant="outline" color="blue" w="fit-content">
                                {benefit.code}
                              </Badge>
                            )}
                          </Stack>
                        </Table.Td>
                        <Table.Td>
                          <Badge color={getBenefitTypeBadgeColor(benefitType)} variant="light">
                            {benefitType || b.value_type || '-'}
                          </Badge>
                        </Table.Td>
                        <Table.Td>
                          {(() => {
                            if (b.value_type === 'percentage' && b.value != null && Number(b.value) > 0) {
                              return <Text fw={500}>{Number(b.value)}%</Text>;
                            }
                            if (b.value_type === 'fixed' && b.value != null && Number(b.value) > 0) {
                              return <Text fw={500}>{Number(b.value).toLocaleString('id-ID')}</Text>;
                            }
                            if (b.value_type === 'item') {
                              return <Text c="dimmed">Physical Item</Text>;
                            }
                            if (b.value_type === 'quota') {
                              return <Text c="dimmed">Slot / Access</Text>;
                            }
                            return (b.value != null && Number(b.value) > 0) ? (
                              <Text>{String(b.value)}</Text>
                            ) : (
                              <Text c="dimmed">-</Text>
                            );
                          })()}
                        </Table.Td>
                        <Table.Td>
                          {b.max_value != null && Number(b.max_value) > 0 ? (
                            <Text>{Number(b.max_value).toLocaleString('id-ID')}</Text>
                          ) : (
                            <Text c="dimmed">-</Text>
                          )}
                        </Table.Td>
                        <Table.Td>
                          {b.quota != null && Number(b.quota) > 0 ? (
                            <Badge variant="dot" color="teal">
                              {b.quota}
                            </Badge>
                          ) : (
                            <Text c="dimmed">Unlimited</Text>
                          )}
                        </Table.Td>
                        <Table.Td>
                          {b.notes ? <Text size="sm">{b.notes}</Text> : <Text c="dimmed">-</Text>}
                        </Table.Td>
                        <Table.Td style={{ textAlign: 'center' }}>
                          <Tooltip label="Remove Benefit" withArrow>
                            <ActionIcon
                              color="red"
                              variant="subtle"
                              onClick={() => handleDeleteBenefit(b.id)}
                            >
                              <IconTrash size={16} />
                            </ActionIcon>
                          </Tooltip>
                        </Table.Td>
                      </Table.Tr>
                    );
                  })
                ) : (
                  <Table.Tr>
                    <Table.Td colSpan={7}>
                      <Text c="dimmed" ta="center" py="md">
                        No benefits assigned yet. Click "Add Benefit" to assign one.
                      </Text>
                    </Table.Td>
                  </Table.Tr>
                )}
              </Table.Tbody>
            </Table>

            {/* Modal Add Benefit */}
            <Modal
              opened={isModalOpen}
              onClose={() => setIsModalOpen(false)}
              title={<Title order={4}>Add Benefit to Package</Title>}
              centered
              size="lg"
            >
              <form onSubmit={handleAddBenefit}>
                <Stack gap="md">
                  <Select
                    label="Select Membership Benefit"
                    placeholder="Choose a benefit..."
                    data={availableBenefits.map((ab) => ({
                      value: String(ab.id),
                      label: `${ab.code} — ${ab.name} (${ab.benefit_type || 'benefit'})`,
                    }))}
                    value={pbData.membership_benefit_id}
                    onChange={handleBenefitChange}
                    required
                    searchable
                  />

                  {selectedBenefit && (
                    <Paper withBorder p="sm" radius="sm" bg="gray.0">
                      <Group justify="space-between" mb={4}>
                        <Text fw={600} size="sm">
                          {selectedBenefit.name} ({selectedBenefit.code})
                        </Text>
                        <Badge
                          color={getBenefitTypeBadgeColor(selectedBenefit.benefit_type)}
                          variant="filled"
                        >
                          {selectedBenefit.benefit_type}
                        </Badge>
                      </Group>
                      {selectedBenefit.description && (
                        <Text size="xs" c="dimmed">
                          {selectedBenefit.description}
                        </Text>
                      )}
                    </Paper>
                  )}

                  <Divider label="Benefit Configuration" labelPosition="center" />

                  {/* Form fields dynamically adjusted based on benefit_type */}
                  {selectedBenefit?.benefit_type === 'discount' && (
                    <>
                      <Select
                        label="Value Type"
                        data={[
                          { value: 'percentage', label: 'Percentage (%)' },
                          { value: 'fixed', label: 'Fixed Amount (Nominal)' },
                        ]}
                        value={pbData.value_type}
                        onChange={(val) => setPbData('value_type', val || 'percentage')}
                        required
                      />

                      <NumberInput
                        label={pbData.value_type === 'percentage' ? 'Discount Percentage (%)' : 'Discount Amount'}
                        placeholder={pbData.value_type === 'percentage' ? 'e.g. 15' : 'e.g. 50000'}
                        value={pbData.value}
                        onChange={(val) => setPbData('value', Number(val) || 0)}
                        min={0}
                        max={pbData.value_type === 'percentage' ? 100 : undefined}
                        required
                      />

                      {pbData.value_type === 'percentage' && (
                        <NumberInput
                          label="Max Discount Value (Optional)"
                          description="Maximum discount cap in IDR/USD"
                          placeholder="e.g. 100000 (0 for no limit)"
                          value={pbData.max_value}
                          onChange={(val) => setPbData('max_value', Number(val) || 0)}
                          min={0}
                        />
                      )}

                      <NumberInput
                        label="Usage Quota (Optional)"
                        description="Max number of times this benefit can be used (0 for unlimited)"
                        placeholder="0 = Unlimited"
                        value={pbData.quota}
                        onChange={(val) => setPbData('quota', Number(val) || 0)}
                        min={0}
                      />

                      <Textarea
                        label="Notes (Optional)"
                        placeholder="Additional details or terms..."
                        value={pbData.notes}
                        onChange={(e) => setPbData('notes', e.target.value)}
                        rows={2}
                      />
                    </>
                  )}

                  {selectedBenefit?.benefit_type === 'voucher' && (
                    <>
                      <Select
                        label="Voucher Type"
                        data={[
                          { value: 'fixed', label: 'Fixed Nominal Amount' },
                          { value: 'percentage', label: 'Percentage (%)' },
                        ]}
                        value={pbData.value_type}
                        onChange={(val) => setPbData('value_type', val || 'fixed')}
                        required
                      />

                      <NumberInput
                        label={pbData.value_type === 'percentage' ? 'Voucher Percentage (%)' : 'Voucher Nominal'}
                        placeholder={pbData.value_type === 'percentage' ? 'e.g. 20' : 'e.g. 100000'}
                        value={pbData.value}
                        onChange={(val) => setPbData('value', Number(val) || 0)}
                        min={0}
                        required
                      />

                      <NumberInput
                        label="Voucher Quantity / Quota"
                        description="Number of vouchers granted to this package"
                        placeholder="e.g. 1"
                        value={pbData.quota}
                        onChange={(val) => setPbData('quota', Number(val) || 0)}
                        min={1}
                        required
                      />

                      <Textarea
                        label="Voucher Notes / Terms"
                        placeholder="e.g. Valid for JOIV Article submission only"
                        value={pbData.notes}
                        onChange={(e) => setPbData('notes', e.target.value)}
                        rows={2}
                      />
                    </>
                  )}

                  {selectedBenefit?.benefit_type === 'souvenir' && (
                    <>
                      <NumberInput
                        label="Quantity / Quota"
                        description="How many souvenir packages/items"
                        placeholder="e.g. 1"
                        value={pbData.quota}
                        onChange={(val) => setPbData('quota', Number(val) || 0)}
                        min={1}
                        required
                      />

                      <Textarea
                        label="Souvenir Items / Details"
                        placeholder="e.g. Kaos, Tumbler, Sticker, Bag"
                        value={pbData.notes}
                        onChange={(e) => setPbData('notes', e.target.value)}
                        rows={3}
                        required
                      />
                    </>
                  )}

                  {selectedBenefit?.benefit_type === 'opportunity' && (
                    <>
                      <NumberInput
                        label="Slot / Quota"
                        description="Number of opportunity slots"
                        placeholder="e.g. 1"
                        value={pbData.quota}
                        onChange={(val) => setPbData('quota', Number(val) || 0)}
                        min={1}
                        required
                      />

                      <Textarea
                        label="Opportunity Details"
                        placeholder="e.g. Eligible to be selected as Keynote Speaker"
                        value={pbData.notes}
                        onChange={(e) => setPbData('notes', e.target.value)}
                        rows={3}
                        required
                      />
                    </>
                  )}

                  {(!selectedBenefit || !['discount', 'voucher', 'souvenir', 'opportunity'].includes(selectedBenefit.benefit_type)) && (
                    <>
                      <Select
                        label="Value Type"
                        data={[
                          { value: 'percentage', label: 'Percentage' },
                          { value: 'fixed', label: 'Fixed Nominal' },
                          { value: 'item', label: 'Item' },
                          { value: 'quota', label: 'Quota' },
                        ]}
                        value={pbData.value_type}
                        onChange={(val) => setPbData('value_type', val || '')}
                      />

                      <NumberInput
                        label="Value"
                        value={pbData.value}
                        onChange={(val) => setPbData('value', Number(val) || 0)}
                      />

                      <NumberInput
                        label="Max Value"
                        value={pbData.max_value}
                        onChange={(val) => setPbData('max_value', Number(val) || 0)}
                      />

                      <NumberInput
                        label="Quota"
                        value={pbData.quota}
                        onChange={(val) => setPbData('quota', Number(val) || 0)}
                      />

                      <Textarea
                        label="Notes"
                        value={pbData.notes}
                        onChange={(e) => setPbData('notes', e.target.value)}
                      />
                    </>
                  )}

                  <Group justify="flex-end" pt="sm">
                    <Button variant="subtle" onClick={() => setIsModalOpen(false)}>
                      Cancel
                    </Button>
                    <Button type="submit" loading={pbProcessing}>
                      Add Benefit
                    </Button>
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
