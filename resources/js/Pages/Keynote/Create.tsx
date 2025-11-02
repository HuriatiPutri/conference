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
  Textarea,
  Button,
  Stack,
  Alert,
  Group,
  Badge,
} from '@mantine/core';
import { IconInfoCircle, IconCheck, IconX } from '@tabler/icons-react';
import AuthLayout from '../../Layout/AuthLayout';
import dayjs from 'dayjs';

interface Conference {
  id: number;
  public_id: string;
  name: string;
  initial: string;
  date: string;
  city: string;
  country: string;
}

interface Props {
  conference: Conference;
  flash?: {
    success?: string;
    error?: string;
  };
}

export default function CreateKeynote({ conference, flash }: Props) {
  const { data, setData, post, processing, errors, reset } = useForm({
    email: '',
    first_name: '',
    last_name: '',
    feedback: '',
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post(`/keynote/${conference.public_id}`, {
      onSuccess: () => {
        reset('feedback');
      },
    });
  };

  return (
    <>
      <Head title={`Keynote Feedback - ${conference.name}`} />

      <Container size="md" py="xl">
        <Paper p="xl" radius="md" withBorder>
          <Stack gap="lg">
            <Box>
              <Title order={2} mb="xs">
                Keynote Feedback
              </Title>
              <Text c="dimmed" size="sm">
                {conference.name} ({conference.initial})
              </Text>
              <Text c="dimmed" size="sm">
                {dayjs(conference.date).format('MMMM D, YYYY')} • {conference.city}, {conference.country}
              </Text>
            </Box>

            <Alert icon={<IconInfoCircle size="1rem" />} color="blue">
              Please use the same email address you used for conference registration.
            </Alert>

            {flash?.success && (
              <Alert icon={<IconCheck size="1rem" />} color="green">
                {flash.success}
              </Alert>
            )}

            {flash?.error && (
              <Alert icon={<IconX size="1rem" />} color="red">
                {flash.error}
              </Alert>
            )}

            <form onSubmit={handleSubmit}>
              <Stack gap="md">
                <TextInput
                  label="Email Address"
                  placeholder="your.email@example.com"
                  required
                  value={data.email}
                  onChange={(e) => setData('email', e.target.value)}
                  error={errors.email}
                  description="Must match your conference registration email"
                />

                <Group grow>
                  <TextInput
                    label="First Name"
                    placeholder="John"
                    required
                    value={data.first_name}
                    onChange={(e) => setData('first_name', e.target.value)}
                    error={errors.first_name}
                  />

                  <TextInput
                    label="Last Name"
                    placeholder="Doe"
                    required
                    value={data.last_name}
                    onChange={(e) => setData('last_name', e.target.value)}
                    error={errors.last_name}
                  />
                </Group>

                <Textarea
                  label="Keynote Feedback"
                  placeholder="Please share your thoughts about the keynote presentation..."
                  required
                  minRows={4}
                  maxRows={8}
                  maxLength={2000}
                  value={data.feedback}
                  onChange={(e) => setData('feedback', e.target.value)}
                  error={errors.feedback}
                  description={`${data.feedback.length}/2000 characters`}
                />

                <Button
                  type="submit"
                  loading={processing}
                  size="md"
                  fullWidth
                >
                  {processing ? 'Submitting...' : 'Submit Feedback'}
                </Button>
              </Stack>
            </form>
          </Stack>
        </Paper>
      </Container>
    </>
  );
}

CreateKeynote.layout = (page: React.ReactNode) => (
  <AuthLayout title="Create Keynote">{page}</AuthLayout>
);