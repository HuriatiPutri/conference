import React from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import { Badge, Button, Container, Grid, Paper, Stack, Text, TextInput, Title, Alert } from '@mantine/core';
import MainLayout from '../../Layout/MainLayout';
import { PageProps } from '../../types';

export default function Profile() {
  const { auth, flash } = usePage<PageProps>().props;
  const user = auth.user as any;
  const membership = user?.membership;
  const role = auth.role ?? 'user';

  const { data, setData, put, processing, errors } = useForm({
    name: user?.name || '',
    email: user?.email || '',
  });

  const handleSubmit = (e: React.SyntheticEvent<HTMLFormElement>) => {
    e.preventDefault();
    put('/profile');
  };

  return (
    <>
      <Head title="Profile" />
      <Container size={720} py="md">
        <Stack gap="xs">
          <Title order={2}>Profile</Title>
          <Text c="dimmed" size="sm">
            View your account details and update the profile fields you can change.
          </Text>
        </Stack>

        {flash?.success && (
          <Alert color="green" mt="md">{flash.success}</Alert>
        )}

        <Grid mt="md">
          <Grid.Col span={{ base: 12, md: 5 }}>
            <Paper withBorder p="md" radius="md" h="100%">
              <Stack gap="md">
                <div>
                  <Text size="sm" c="dimmed">Account details</Text>
                  <Title order={4}>{user?.name}</Title>
                </div>

                <div>
                  <Text size="xs" c="dimmed">Email</Text>
                  <Text size="sm">{user?.email}</Text>
                </div>

                <div>
                  <Text size="xs" c="dimmed">Role</Text>
                  <Badge variant="light" color={role === 'admin' ? 'red' : 'blue'}>
                    {role}
                  </Badge>
                </div>

                <div>
                  <Text size="xs" c="dimmed">Membership</Text>
                  <Text size="sm">
                    {membership ? `${membership.status ?? 'active'} - ${membership.package?.name ?? 'Membership'}` : 'No membership linked'}
                  </Text>
                </div>

                <div>
                  <Text size="xs" c="dimmed">Member since</Text>
                  <Text size="sm">{user?.created_at ? new Date(user.created_at).toLocaleDateString() : '-'}</Text>
                </div>
              </Stack>
            </Paper>
          </Grid.Col>

          <Grid.Col span={{ base: 12, md: 7 }}>
            <Paper withBorder p="md" radius="md">
              <Title order={4} mb="md">Edit Profile</Title>
              <form onSubmit={handleSubmit}>
                <Stack>
                  <TextInput
                    label="Name"
                    value={data.name}
                    onChange={(e) => setData('name', e.currentTarget.value)}
                    error={errors.name}
                    required
                  />
                  <TextInput
                    label="Email"
                    value={data.email}
                    onChange={(e) => setData('email', e.currentTarget.value)}
                    error={errors.email}
                    required
                  />

                  <Button type="submit" loading={processing}>
                    Save changes
                  </Button>
                </Stack>
              </form>
            </Paper>
          </Grid.Col>
        </Grid>
      </Container>
    </>
  );
}

Profile.layout = (page: React.ReactNode) => <MainLayout title="Profile">{page}</MainLayout>;
