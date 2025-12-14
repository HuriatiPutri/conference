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
import { route } from 'ziggy-js';

interface LoaVolume {
  id: number;
  volume: string;
}

interface JoivRegistration {
  id: number;
  first_name: string;
  last_name: string;
  email_address: string;
  institution: string;
  paper_title: string;
  payment_status: string;
  loa_authors?: string;
  loa_volume_id?: number;
  loa_approved_at?: string;
  loa_volume?: LoaVolume;
}

interface AssignVolumeProps {
  registration: JoivRegistration;
  loaVolumes: LoaVolume[];
}

function JoivArticleAssignVolume({ registration, loaVolumes }: Readonly<AssignVolumeProps>) {
  const { data, setData, post, processing, errors } = useForm({
    authors: registration.loa_authors || `${registration.first_name} ${registration.last_name}`,
    loa_volume_id: registration.loa_volume_id ? registration.loa_volume_id.toString() : '',
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post(route('joiv-articles.update-loa-info', registration.id));
  };

  // Check if data is already saved
  const isDataSaved = registration.loa_authors && registration.loa_volume_id;

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
            <Title order={2}>Assign Volume for JOIV Article</Title>
            <Text c="dimmed" size="sm">
              Please fill in the required information for the Letter of Approval
            </Text>
          </div>
          <Button
            variant="subtle"
            leftSection={<IconArrowLeft size={16} />}
            onClick={() => router.visit('/joiv-articles')}
          >
            Back
          </Button>
        </Group>

        {/* Participant Info */}
        <Card padding="lg" radius="md" withBorder>
          <Title order={4} mb="md">Article Information</Title>
          <Stack gap="xs">
            <Group>
              <Text fw={500} w={120}>Author:</Text>
              <Text>{registration.first_name} {registration.last_name}</Text>
            </Group>
            <Group>
              <Text fw={500} w={120}>Email:</Text>
              <Text>{registration.email_address}</Text>
            </Group>
            <Group>
              <Text fw={500} w={120}>Institution:</Text>
              <Text>{registration.institution}</Text>
            </Group>
            <Group>
              <Text fw={500} w={120}>Paper Title:</Text>
              <Text>&ldquo;{registration.paper_title}&rdquo;</Text>
            </Group>
          </Stack>
        </Card>

        {/* Assign Volume Form */}
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
                      href={route('joiv-articles.downloadLoa', registration.id)}
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
                    Save & Continue
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

JoivArticleAssignVolume.layout = (page: React.ReactNode) => (
  <MainLayout title="Assign Volume - JOIV Article">{page}</MainLayout>
);

export default JoivArticleAssignVolume;
