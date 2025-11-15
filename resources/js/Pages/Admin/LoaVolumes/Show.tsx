import { router, usePage } from '@inertiajs/react';
import {
  Button,
  Card,
  Container,
  Group,
  Stack,
  Text,
  Title,
  Badge,
  Divider,
  Alert,
} from '@mantine/core';
import { IconArrowLeft, IconEdit, IconTrash, IconDownload, IconInfoCircle } from '@tabler/icons-react';
import React from 'react';
import { route } from 'ziggy-js';
import MainLayout from '../../../Layout/MainLayout';

interface Audience {
  id: number;
  first_name: string;
  last_name: string;
  paper_title: string;
  loa_authors: string;
  institution: string;
  full_paper_path?: string;
  conference: {
    id: number;
    name: string;
    initial: string;
  };
}

interface LoaVolume {
  id: number;
  volume: string;
  created_at: string;
  updated_at: string;
  creator?: {
    id: number;
    name: string;
  };
  updater?: {
    id: number;
    name: string;
  };
  audiences?: Audience[];
}

function LoaVolumeShow() {
  const { loaVolume } = usePage<{
    loaVolume: LoaVolume;
  }>().props;

  const handleEdit = () => {
    router.get(route('loa.loa-volumes.edit', loaVolume.id));
  };

  const handleDelete = () => {
    if (confirm('Are you sure you want to delete this LoA Volume?')) {
      router.delete(route('loa.loa-volumes.destroy', loaVolume.id), {
        onSuccess: () => {
          router.visit(route('loa.loa-volumes.index'));
        },
      });
    }
  };

  return (
    <MainLayout>
      <Container size="md" py="xl">
        <Stack gap="lg">
          <Group justify="space-between">
            <div>
              <Title order={2}>LoA Volume Details</Title>
              <Text c="dimmed" size="sm">
                View LoA Volume information
              </Text>
            </div>
            <Button
              variant="subtle"
              leftSection={<IconArrowLeft size={16} />}
              onClick={() => router.visit(route('loa.loa-volumes.index'))}
            >
              Back to List
            </Button>
          </Group>

          <Card withBorder>
            <Stack gap="md">
              <Group justify="space-between">
                <Title order={3}>Volume Information</Title>
                <Group gap="xs">
                  <Button
                    size="sm"
                    variant="outline"
                    leftSection={<IconEdit size={16} />}
                    onClick={handleEdit}
                  >
                    Edit
                  </Button>
                  {loaVolume.audiences && loaVolume.audiences.length === 0 && (
                    <Button
                      size="sm"
                      color="red"
                      variant="outline"
                      leftSection={<IconTrash size={16} />}
                      onClick={handleDelete}
                    >
                      Delete
                    </Button>
                  )}
                </Group>
              </Group>

              <Divider />

              <Stack gap="sm">
                <Group>
                  <Text fw={500} w={200}>
                    Volume:
                  </Text>
                  <Badge size="lg" variant="outline">
                    {loaVolume.volume}
                  </Badge>
                </Group>

                <Group>
                  <Text fw={500} w={200}>
                    Last Modified by:
                  </Text>
                  <Text>{loaVolume.updater?.name || 'System'}</Text>
                </Group>

                <Group>
                  <Text fw={500} w={200}>
                    Last Modified at:
                  </Text>
                  <Text>{new Date(loaVolume.updated_at).toLocaleString('id-ID')}</Text>
                </Group>
              </Stack>
            </Stack>
          </Card>

          {/* Audiences Section */}
          <Card withBorder>
            <Stack gap="md">
              <Group justify="space-between">
                <Title order={3}>Assigned Audiences ({loaVolume.audiences?.length || 0})</Title>
              </Group>

              <Divider />

              {loaVolume.audiences && loaVolume.audiences.length > 0 ? (
                <Stack gap="md">
                  {loaVolume.audiences.map((audience) => (
                    <Card key={audience.id} withBorder padding="md" bg="gray.0">
                      <Stack gap="sm">
                        <Group justify="space-between">
                          <div>
                            <Text fw={600} size="md">
                              {audience.first_name} {audience.last_name}
                            </Text>
                            <Text size="sm" c="dimmed">
                              {audience.institution} • {audience.conference.name}
                            </Text>
                          </div>
                          {audience.full_paper_path && (
                            <Button
                              size="xs"
                              variant="outline"
                              leftSection={<IconDownload size={14} />}
                              component="a"
                              href={`/storage/${audience.full_paper_path}`}
                              target="_blank"
                            >
                              Paper
                            </Button>
                          )}
                        </Group>

                        <div>
                          <Text fw={500} size="sm" mb={4}>
                            Paper Title:
                          </Text>
                          <Text size="sm" style={{ fontStyle: 'italic' }}>
                            &ldquo;{audience.paper_title}&rdquo;
                          </Text>
                        </div>

                        {audience.loa_authors && (
                          <div>
                            <Text fw={500} size="sm" mb={4}>
                              Authors:
                            </Text>
                            <Text size="sm">
                              {audience.loa_authors}
                            </Text>
                          </div>
                        )}
                      </Stack>
                    </Card>
                  ))}
                </Stack>
              ) : (
                <Alert icon={<IconInfoCircle size={16} />} color="blue">
                  No audiences have been assigned to this volume yet.
                </Alert>
              )}
            </Stack>
          </Card>
        </Stack>
      </Container>
    </MainLayout>
  );
}

export default LoaVolumeShow;