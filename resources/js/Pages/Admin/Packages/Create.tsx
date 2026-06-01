import { router, useForm } from '@inertiajs/react';
import { Button, Card, Container, Group, Stack, Text, TextInput, Title, NumberInput, Select } from '@mantine/core';
import { notifications } from '@mantine/notifications';
import { IconArrowLeft } from '@tabler/icons-react';
import React from 'react';
import { route } from 'ziggy-js';
import MainLayout from '../../../Layout/MainLayout';

function PackageCreate() {
  const { data, setData, errors, post, processing } = useForm({
    name: '',
    price_idr: 0,
    price_usd: 0,
    status: 'active',
    duration: 0,
  });

  function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    post(route('packages.store'), {
      onSuccess: () => {
        notifications.show({ message: 'Package created successfully!', color: 'green' });
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
              <Title order={2}>Add New Package</Title>
              <Text c="dimmed" size="sm">Create a new subscription package</Text>
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
                  parser={(value) => value ? value.replace(/[^0-9]/g, '') : ''}
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
                  <Button type="submit" loading={processing}>Create Package</Button>
                </Group>
              </Stack>
            </form>
          </Card>
        </Stack>
      </Container>
    </MainLayout>
  );
}

export default PackageCreate;
