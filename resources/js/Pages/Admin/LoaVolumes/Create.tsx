import { router, useForm } from '@inertiajs/react';
import {
  Button,
  Card,
  Container,
  Group,
  Stack,
  Text,
  TextInput,
  Title,
} from '@mantine/core';
import { notifications } from '@mantine/notifications';
import { IconArrowLeft } from '@tabler/icons-react';
import React from 'react';
import { route } from 'ziggy-js';
import MainLayout from '../../../Layout/MainLayout';

function LoaVolumeCreate() {
  const { data, setData, errors, post, processing } = useForm({
    volume: '',
  });

  function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    post(route('loa.loa-volumes.store'), {
      onSuccess: () => {
        notifications.show({ message: 'LoA Volume created successfully!', color: 'green' });
      },
      onError: (e) => {
        console.log('error', e);
      },
    });
  }

  return (
    <MainLayout>
      <Container size="md" py="xl">
        <Stack gap="lg">
          <Group justify="space-between">
            <div>
              <Title order={2}>Add New LoA Volume</Title>
              <Text c="dimmed" size="sm">
                Create a new LoA Volume entry
              </Text>
            </div>
            <Button
              variant="subtle"
              leftSection={<IconArrowLeft size={16} />}
              onClick={() => router.visit(route('loa.loa-volumes.index'))}
            >
              Back
            </Button>
          </Group>

          <Card withBorder>
            <form onSubmit={handleSubmit}>
              <Stack gap="md">
                <TextInput
                  label="Volume"
                  placeholder="Enter volume (e.g., Volume 1, Vol. 2, etc.)"
                  value={data.volume}
                  onChange={(e) => setData('volume', e.target.value)}
                  error={errors.volume}
                  required
                  description="Enter the volume identifier for the Letter of Approval"
                />

                <Group justify="flex-end" pt="md">
                  <Button
                    variant="subtle"
                    onClick={() => router.visit(route('loa.loa-volumes.index'))}
                  >
                    Cancel
                  </Button>
                  <Button
                    type="submit"
                    loading={processing}
                  >
                    Create LoA Volume
                  </Button>
                </Group>
              </Stack>
            </form>
          </Card>
        </Stack>
      </Container>
    </MainLayout>
  );
}

export default LoaVolumeCreate;