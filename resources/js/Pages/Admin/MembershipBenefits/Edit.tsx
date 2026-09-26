import { router, useForm, usePage } from '@inertiajs/react';
import { Button, Card, Container, Stack, TextInput, Textarea, Title, Select, Group } from '@mantine/core';
import { notifications } from '@mantine/notifications';
import React from 'react';
import { route } from 'ziggy-js';
import MainLayout from '../../../Layout/MainLayout';

function MembershipBenefitsEdit() {
  const { membershipBenefit: benefit } = usePage<any>().props;

  const { data, setData, put, errors, processing } = useForm({
    code: benefit.code || '',
    name: benefit.name || '',
    benefit_type: benefit.benefit_type || 'discount',
    description: benefit.description || '',
  });

  function handleSubmit(e: any) {
    e.preventDefault();
    put(route('membership-benefits.update', benefit.id), {
      onSuccess: () => {
        notifications.show({ message: 'Benefit updated', color: 'green' });
        router.visit(route('membership-benefits.index'));
      },
    });
  }

  return (
    <MainLayout title="Edit Benefit">
      <Container size="md" py="xl">
        <Stack gap="lg">
          <div>
            <Title order={2}>Edit Membership Benefit</Title>
          </div>

          <Card withBorder>
            <form onSubmit={handleSubmit}>
              <Stack gap="md">
                <TextInput label="Code" value={data.code} onChange={(e) => setData('code', e.target.value)} error={errors.code} required />
                <TextInput label="Name" value={data.name} onChange={(e) => setData('name', e.target.value)} error={errors.name} required />

                <Select
                  label="Benefit Type"
                  data={[
                    { value: 'discount', label: 'Discount' },
                    { value: 'item', label: 'Item' },
                    { value: 'cashback', label: 'Cashback' },
                    { value: 'shipping', label: 'Shipping' },
                    { value: 'reward', label: 'Reward' },
                  ]}
                  value={data.benefit_type}
                  onChange={(val) => setData('benefit_type', val || '')}
                />

                <Textarea label="Description" value={data.description} onChange={(e) => setData('description', e.target.value)} />

                <Group position="right">
                  <Button variant="subtle" onClick={() => router.visit(route('membership-benefits.index'))}>Cancel</Button>
                  <Button type="submit" loading={processing}>Update</Button>
                </Group>
              </Stack>
            </form>
          </Card>
        </Stack>
      </Container>
    </MainLayout>
  );
}

export default MembershipBenefitsEdit;
