import React from 'react';
import { router, useForm } from '@inertiajs/react';
import {
  Container,
  Title,
  Button,
  Group,
  Text,
  Stack,
  Card,
  Textarea,
  Divider,
  Alert,
  Select
} from '@mantine/core';
import { IconInfoCircle, IconDownload, IconArrowLeft, IconAlertCircle } from '@tabler/icons-react';
import MainLayout from '../../../Layout/MainLayout';
import { Audiences } from '../../../types';
import { route } from 'ziggy-js';

interface LoaVolume {
  id: number;
  volume: string;
}

interface DownloadFormProps {
  audience: Audiences & {
    loa_authors?: string;
    loa_volume_id?: number;
    loa_status?: string;
    loa_volume?: LoaVolume;
  };
  loaVolumes: LoaVolume[];
}

function LettersOfApprovalDownloadForm({ audience, loaVolumes }: DownloadFormProps) {
  const { data, setData, post, processing, errors } = useForm({
    authors: audience.loa_authors || `${audience.first_name} ${audience.last_name}`,
    loa_volume_id: audience.loa_volume_id ? audience.loa_volume_id.toString() : '',
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post(route('letters-of-approval.update-info', audience.id));
  };

  // Check if data is already saved
  const isDataSaved = audience.loa_authors && audience.loa_volume_id;

  // Check for general errors
  const hasGeneralError = errors && typeof errors === 'object' && 'error' in errors;

  return (
    <Container size="md" py="xl">
      <Stack gap="lg">
        {/* Success Alert */}
        {isDataSaved && (
          <Alert
            icon={<IconInfoCircle size={16} />}
            color="green"
            title="Information Saved"
            variant="filled"
          >
            LoA information has been saved successfully. You can now download the PDF or update the information.
          </Alert>
        )}

        {/* General Error Alert */}
        {hasGeneralError && (
          <Alert
            icon={<IconAlertCircle size={16} />}
            color="red"
            title="Error"
            variant="filled"
          >
            {(errors as Record<string, string>).error}
          </Alert>
        )}
        {/* Header */}
        <Group justify="space-between">
          <div>
            <Title order={2}>Download JOIV Acceptance Letter</Title>
            <Text c="dimmed" size="sm">
              Please fill in the required information for the acceptance letter
            </Text>
          </div>
          <Button
            variant="subtle"
            leftSection={<IconArrowLeft size={16} />}
            onClick={() => router.visit('/letters-of-approval')}
          >
            Back
          </Button>
        </Group>
        
        {/* Participant Info */}
        <Card padding="lg" radius="md" withBorder>
          <Title order={4} mb="md">Participant Information</Title>
          <Stack gap="xs">
            <Group>
              <Text fw={500} w={120}>Name:</Text>
              <Text>{audience.first_name} {audience.last_name}</Text>
            </Group>
            <Group>
              <Text fw={500} w={120}>Institution:</Text>
              <Text>{audience.institution}</Text>
            </Group>
            <Group>
              <Text fw={500} w={120}>Paper Title:</Text>
              <Text>&ldquo;{audience.paper_title}&rdquo;</Text>
            </Group>
          </Stack>
        </Card>

        {/* Download Form */}
        <Card padding="lg" radius="md" withBorder>
          <Title order={4} mb="md">JOIV Publication Details</Title>

          <Alert icon={<IconInfoCircle size={16} />} color="blue" mb="md">
            Please provide the authors list and select the appropriate LoA volume as they should appear in the acceptance letter.
          </Alert>

          <form onSubmit={handleSubmit}>
            <Stack gap="md">
              <Textarea
                label="Authors"
                description="List all authors as they should appear in the publication (e.g., John Doe, Jane Smith, ...)"
                placeholder="Enter all authors separated by commas"
                value={data.authors}
                onChange={(e) => setData('authors', e.target.value)}
                error={errors.authors}
                required
                rows={3}
              />

              <Select
                label="LoA Volume"
                description="Select the volume for this Letter of Approval"
                placeholder="Select volume"
                value={data.loa_volume_id}
                onChange={(value) => setData('loa_volume_id', value || '')}
                error={errors.loa_volume_id}
                data={loaVolumes.map(volume => ({
                  value: volume.id.toString(),
                  label: volume.volume
                }))}
                required
                searchable
                clearable
              />

              <Divider />

              <Group justify="flex-end" gap="md">
                {isDataSaved ? (
                  <>
                    <Button
                      component="a"
                      href={route('letters-of-approval.download', audience.id)}
                      leftSection={<IconDownload size={16} />}
                      color="green"
                      size="md"
                    >
                      Download PDF
                    </Button>
                    <Button
                      type="submit"
                      variant="outline"
                      loading={processing}
                      size="md"
                    >
                      Update Information
                    </Button>
                  </>
                ) : (
                  <Button
                    type="submit"
                    leftSection={<IconDownload size={16} />}
                    loading={processing}
                    size="md"
                  >
                    Approve & Save
                  </Button>
                )}
              </Group>
            </Stack>
          </form>
        </Card>

        {/* Preview Info */}
        <Card padding="lg" radius="md" withBorder bg="gray.0">
          <Title order={5} mb="sm">Preview Information</Title>
          <Text size="sm" c="dimmed">
            The generated PDF will be formatted as a JOIV (International Journal on Informatics Visualization)
            acceptance letter, similar to the official journal acceptance format with Scopus indexing information.
          </Text>
        </Card>
      </Stack>
    </Container>
  );
}

LettersOfApprovalDownloadForm.layout = (page: React.ReactNode) => (
  <MainLayout title="Download JOIV Acceptance Letter">{page}</MainLayout>
);

export default LettersOfApprovalDownloadForm;