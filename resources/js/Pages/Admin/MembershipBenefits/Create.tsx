import { router, useForm } from '@inertiajs/react';
import { Button, Card, Container, Stack, TextInput, Textarea, Title, Select, Group } from '@mantine/core';
import { notifications } from '@mantine/notifications';
import React from 'react';
import { route } from 'ziggy-js';
import MainLayout from '../../../Layout/MainLayout';

function MembershipBenefitsCreate() {
  const { data, setData, post, errors, processing } = useForm({
    code: '',
    name: '',
    benefit_type: 'discount',
    description: '',
  });

  function handleSubmit(e: any) {
    e.preventDefault();
    post(route('membership-benefits.store'), {
      onSuccess: () => {
        notifications.show({ message: 'Benefit created', color: 'green' });
        router.visit(route('membership-benefits.index'));
      },
    });
  }

  return (
    <MainLayout title="Add Benefit">
      <Container size="md" py="xl">
        <Stack gap="lg">
          <div>
            <Title order={2}>Add Membership Benefit</Title>
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
                  <Button type="submit" loading={processing}>Create</Button>
                </Group>
              </Stack>
            </form>
          </Card>
        </Stack>
      </Container>
    </MainLayout>
  );
}

export default MembershipBenefitsCreate;
