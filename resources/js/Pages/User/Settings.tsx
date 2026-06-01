import React from 'react';
import { Head, usePage } from '@inertiajs/react';
import { Container, Stack, Paper, Title, Text, Alert } from '@mantine/core';
import MainLayout from '../../Layout/MainLayout';
import { PageProps } from '../../types';

export default function Settings({ user }: any) {
  const { flash } = usePage<PageProps>().props;

  return (
    <>
      <Head title="Settings" />
      <Container size={720} py="md">
        <Title order={2}>Settings</Title>

        {flash?.success && (
          <Alert color="green" mt="md">{flash.success}</Alert>
        )}

        <Paper withBorder p="md" mt="md">
          <Stack>
            <Text size="sm">Account Email: {user?.email}</Text>
            <Text size="sm">(Account settings placeholder — add preferences here)</Text>
          </Stack>
        </Paper>
      </Container>
    </>
  );
}

Settings.layout = (page: React.ReactNode) => <MainLayout title="Settings">{page}</MainLayout>;
