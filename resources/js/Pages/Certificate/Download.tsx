import React from 'react';
import { Head } from '@inertiajs/react';
import { useForm } from '@inertiajs/react';
import {
  Box,
  Container,
  Paper,
  Title,
  Text,
  TextInput,
  Button,
  Stack,
  Alert,
  Group,
  Divider,
  Select,
} from '@mantine/core';
import { IconInfoCircle, IconDownload, IconCertificate, IconX } from '@tabler/icons-react';
import AuthLayout from '../../Layout/AuthLayout';

interface Conference {
  id: number;
  name: string;
  initial: string;
  date: string;
  city: string;
  country: string;
}

interface Props {
  conferences: Conference[];
  flash?: {
    success?: string;
    error?: string;
  };
  errors?: {
    conference_id?: string;
    email?: string;
  };
  oldInput?: {
    conference_id?: string;
    email?: string;
  };
}

export default function CertificateDownload({ conferences, flash, errors: serverErrors, oldInput }: Props) {
  const { data, setData, errors, setError, clearErrors } = useForm({
    conference_id: oldInput?.conference_id || '',
    email: oldInput?.email || '',
  });

  // Merge server errors with client errors
  const allErrors = { ...errors, ...serverErrors };

  const handleDownload = () => {
    // Clear previous errors
    clearErrors();

    // Validate required fields
    if (!data.conference_id) {
      setError('conference_id', 'Please select a conference');
      return;
    }
    if (!data.email) {
      setError('email', 'Please enter your email address');
      return;
    }

    // Create download URL with parameters
    const params = new URLSearchParams({
      conference_id: data.conference_id,
      email: data.email,
    });

    // Redirect to download URL
    window.location.href = `/certificate/download?${params.toString()}`;
  };

  const conferenceOptions = conferences.map(conference => ({
    value: conference.id.toString(),
    label: `${conference.name} (${conference.initial}) - ${new Date(conference.date).toLocaleDateString()}`
  }));

  return (
    <>
      <Head title="Download Certificate" />

      <Container size="md" py="xl">
        <Paper p="xl" radius="md" withBorder>
          <Stack gap="lg">
            <Box ta="center">
              <Group justify="center" mb="sm">
                <IconCertificate size={48} color="var(--mantine-color-blue-6)" />
              </Group>
              <Title order={2} mb="xs">
                Download Certificate
              </Title>
              <Text c="dimmed" size="sm">
                Download your conference participation certificate
              </Text>
            </Box>

            <Divider />

            <Alert icon={<IconInfoCircle size="1rem" />} color="blue">
              Please enter the conference name and your registered email address to download your certificate.
              Certificate is only available for participants with paid status.
            </Alert>

            {flash?.success && (
              <Alert icon={<IconDownload size="1rem" />} color="green">
                {flash.success}
              </Alert>
            )}

            {flash?.error && (
              <Alert icon={<IconX size="1rem" />} color="red">
                {flash.error}
              </Alert>
            )}

            <Stack gap="md">
              <Select
                label="Conference"
                placeholder="Select a conference"
                required
                data={conferenceOptions}
                value={data.conference_id}
                onChange={(value) => setData('conference_id', value || '')}
                error={allErrors.conference_id}
                description="Select the conference you attended"
                searchable
                clearable
              />

              <TextInput
                label="Email Address"
                placeholder="your.email@example.com"
                required
                type="email"
                value={data.email}
                onChange={(e) => setData('email', e.target.value)}
                error={allErrors.email}
                description="Must match your conference registration email"
              />

              <Button
                onClick={handleDownload}
                size="md"
                fullWidth
                leftSection={<IconDownload size={16} />}
              >
                Download Certificate
              </Button>
            </Stack>

            <Box mt="lg">
              <Text size="sm" c="dimmed" ta="center">
                Having trouble? Make sure you:
              </Text>
              <Stack gap="xs" mt="xs">
                <Text size="sm" c="dimmed">• Use the exact email address from your registration</Text>
                <Text size="sm" c="dimmed">• Select the correct conference from the dropdown</Text>
                <Text size="sm" c="dimmed">• Ensure your payment status is &quot;paid&quot;</Text>
                <Text size="sm" c="dimmed">• Check that the conference has set up certificate templates</Text>
              </Stack>
            </Box>
          </Stack>
        </Paper>
      </Container>
    </>
  );
}


CertificateDownload.layout = (page: React.ReactNode) => (
  <AuthLayout title="Download Certificate">{page}</AuthLayout>
);