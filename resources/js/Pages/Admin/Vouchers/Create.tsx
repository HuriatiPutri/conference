import { router, useForm } from '@inertiajs/react';
import { Button, Card, Checkbox, Container, Group, Stack, Text, TextInput, Title, Select } from '@mantine/core';
import { notifications } from '@mantine/notifications';
import { IconArrowLeft, IconRefresh } from '@tabler/icons-react';
import React from 'react';
import { route } from 'ziggy-js';
import MainLayout from '../../../Layout/MainLayout';

const generateCode = () => Math.random().toString(36).slice(2, 8).toUpperCase();

function VoucherCreate() {
  const { data, setData, errors, post, processing } = useForm({
    code: '',
    start_date: '',
    end_date: '',
    quota: 1,
    discount_type: 'percent',
    discount_value: 0,
    discount_description: '',
    status: 'active',
    applies_to: [] as string[],
  });

  const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();

    post(route('vouchers.store'), {
      onSuccess: () => {
        notifications.show({ message: 'Voucher created successfully!', color: 'green' });
        router.visit(route('vouchers.index'));
      },
    });
  };

  return (
    <MainLayout title="Create Voucher">
      <Container size="md" py="xl">
        <Stack gap="lg">
          <Group justify="space-between">
            <div>
              <Title order={2}>Create Voucher</Title>
              <Text c="dimmed" size="sm">Create voucher with custom code or auto-generated code</Text>
            </div>
            <Button variant="subtle" leftSection={<IconArrowLeft size={16} />} onClick={() => router.visit(route('vouchers.index'))}>
              Back
            </Button>
          </Group>

          <Card withBorder>
            <form onSubmit={handleSubmit}>
              <Stack gap="md">
                <Group align="end">
                  <TextInput
                    label="Voucher Code"
                    placeholder="6 random letters and numbers"
                    value={data.code}
                    onChange={(e) => setData('code', e.target.value.toUpperCase())}
                    error={errors.code}
                    required
                    maxLength={6}
                    style={{ flex: 1 }}
                  />
                  <Button leftSection={<IconRefresh size={16} />} variant="light" onClick={() => setData('code', generateCode())}>
                    Generate
                  </Button>
                </Group>

                <Group grow>
                  <TextInput
                    label="Start Date"
                    type="date"
                    value={data.start_date}
                    onChange={(e) => setData('start_date', e.target.value)}
                    error={errors.start_date}
                    required
                  />
                  <TextInput
                    label="End Date"
                    type="date"
                    value={data.end_date}
                    onChange={(e) => setData('end_date', e.target.value)}
                    error={errors.end_date}
                    required
                  />
                </Group>

                <TextInput
                  label="Quota"
                  type="number"
                  min={1}
                  value={data.quota}
                  onChange={(e) => setData('quota', Number(e.target.value || 0))}
                  error={errors.quota}
                  required
                />

                <Select
                  label="Discount Type"
                  placeholder="Select discount type"
                  data={[
                    { value: 'percent', label: 'Percentage (%)' },
                    { value: 'fixed', label: 'Fixed Amount' },
                  ]}
                  value={data.discount_type}
                  onChange={(value) => setData('discount_type', value || 'percent')}
                  error={errors.discount_type}
                  required
                />

                <TextInput
                  label={data.discount_type === 'percent' ? 'Discount Percentage' : 'Discount Amount'}
                  type="number"
                  step={data.discount_type === 'percent' ? 1 : 0.01}
                  min={0}
                  max={data.discount_type === 'percent' ? 100 : undefined}
                  placeholder={data.discount_type === 'percent' ? '10' : '5.00'}
                  value={data.discount_value}
                  onChange={(e) => setData('discount_value', Number(e.target.value || 0))}
                  error={errors.discount_value}
                  required
                />

                <TextInput
                  label="Discount Description (Optional)"
                  placeholder="e.g., 10% discount for early birds"
                  value={data.discount_description}
                  onChange={(e) => setData('discount_description', e.target.value)}
                  error={errors.discount_description}
                />

                <Text fw={500} size="sm">Applicable Transactions</Text>
                <Checkbox
                  label="Conference Registration"
                  checked={data.applies_to.includes('conference_registration')}
                  onChange={(e) => setData('applies_to', e.target.checked
                    ? [...data.applies_to, 'conference_registration']
                    : data.applies_to.filter((x) => x !== 'conference_registration'))}
                />
                <Checkbox
                  label="JOIV Article"
                  checked={data.applies_to.includes('joiv_article')}
                  onChange={(e) => setData('applies_to', e.target.checked
                    ? [...data.applies_to, 'joiv_article']
                    : data.applies_to.filter((x) => x !== 'joiv_article'))}
                />
                <Checkbox
                  label="Membership Registration"
                  checked={data.applies_to.includes('membership_registration')}
                  onChange={(e) => setData('applies_to', e.target.checked
                    ? [...data.applies_to, 'membership_registration']
                    : data.applies_to.filter((x) => x !== 'membership_registration'))}
                />
                {errors.applies_to && <Text c="red" size="xs">{errors.applies_to}</Text>}

                <Group justify="flex-end" pt="md">
                  <Button variant="subtle" onClick={() => router.visit(route('vouchers.index'))}>Cancel</Button>
                  <Button type="submit" loading={processing}>Create Voucher</Button>
                </Group>
              </Stack>
            </form>
          </Card>
        </Stack>
      </Container>
    </MainLayout>
  );
}

export default VoucherCreate;
