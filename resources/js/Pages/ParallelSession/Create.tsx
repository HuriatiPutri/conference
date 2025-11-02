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
  Radio,
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

interface Room {
  id: number;
  room_name: string;
}

interface Props {
  conference: Conference;
  rooms: Room[];
  flash?: {
    success?: string;
    error?: string;
  };
}

export default function CreateParallelSession({ conference, rooms, flash }: Props) {
  const { data, setData, post, processing, errors, reset } = useForm({
    email: '',
    first_name: '',
    last_name: '',
    room_id: '',
    paper_title: '',
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post(`/parallel-session/${conference.public_id}`, {
      onSuccess: () => {
        reset('paper_title');
      },
    });
  };


  return (
    <>
      <Head title={`Parallel Session - ${conference.name}`} />

      <Container size="md" py="xl">
        <Paper p="xl" radius="md" withBorder>
          <Stack gap="lg">
            <Box>
              <Title order={2} mb="xs">
                Parallel Session Information
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

                <Radio.Group
                  label="Room"
                  required
                  value={data.room_id}
                  onChange={(value) => setData('room_id', value)}
                  error={errors.room_id}
                >
                  <Stack gap="xs" mt="xs">
                    {rooms.map((room) => (
                      <Radio
                        key={room.id}
                        value={room.id.toString()}
                        label={room.room_name}
                      />
                    ))}
                  </Stack>
                </Radio.Group>

                <TextInput
                  label="Paper Title"
                  placeholder="Enter your paper title"
                  required
                  maxLength={500}
                  value={data.paper_title}
                  onChange={(e) => setData('paper_title', e.target.value)}
                  error={errors.paper_title}
                  description={`${data.paper_title.length}/500 characters`}
                />

                <Button
                  type="submit"
                  loading={processing}
                  size="md"
                  fullWidth
                >
                  {processing ? 'Submitting...' : 'Submit Information'}
                </Button>
              </Stack>
            </form>
          </Stack>
        </Paper>
      </Container>
    </>
  );
}

CreateParallelSession.layout = (page: React.ReactNode) => (
  <AuthLayout title="Create Parallel Session">{page}</AuthLayout>
);