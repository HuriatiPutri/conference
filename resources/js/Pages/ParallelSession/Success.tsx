import React from 'react';
import { Head, Link } from '@inertiajs/react';
import {
  Container,
  Paper,
  Title,
  Text,
  Button,
  Stack,
  Box,
  Center,
  Group,
} from '@mantine/core';
import { IconCheck, IconArrowLeft } from '@tabler/icons-react';
import AuthLayout from '../../Layout/AuthLayout';
import dayjs from 'dayjs';

interface Conference {
  name: string;
  initial: string;
  date: string;
}

interface Props {
  conference: Conference;
}

export default function ParallelSessionSuccess({ conference }: Props) {
  return (
    <>
      <Head title={`Success - ${conference.name}`} />

      <Container size="sm" py="xl">
        <Paper p="xl" radius="md" withBorder>
          <Stack gap="lg" align="center">
            <Center>
              <Box
                style={{
                  borderRadius: '50%',
                  backgroundColor: 'var(--mantine-color-green-light)',
                  padding: '1rem',
                }}
              >
                <IconCheck size={48} color="var(--mantine-color-green-6)" />
              </Box>
            </Center>

            <Box ta="center">
              <Title order={2} mb="xs" c="green">
                Parallel Session Information Submitted!
              </Title>
              <Text c="dimmed" size="lg">
                Thank you for submitting your information
              </Text>
            </Box>

            <Box ta="center">
              <Text fw={600}>
                {conference.name} ({conference.initial})
              </Text>
              <Text c="dimmed" size="sm">
                {dayjs(conference.date).format('MMMM D, YYYY')}
              </Text>
            </Box>

            <Text ta="center" c="dimmed">
              Your parallel session information has been successfully submitted.
              Thank you for participating in our conference.
            </Text>
          </Stack>
        </Paper>
      </Container>
    </>
  );
}

ParallelSessionSuccess.layout = (page: React.ReactNode) => (
  <AuthLayout title="Parallel Session Feedback">{page}</AuthLayout>
);