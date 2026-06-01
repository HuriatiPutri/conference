import React from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import { Container, Stack, PasswordInput, Button, Paper, Title, Alert } from '@mantine/core';
import MainLayout from '../../Layout/MainLayout';
import { PageProps } from '../../types';

export default function ChangePassword() {
  const { flash } = usePage<PageProps>().props;
  const { data, setData, put, processing, errors } = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    put('/profile/password');
  };

  return (
    <>
      <Head title="Change Password" />
      <Container size={520} py="md">
        <Title order={2}>Change Password</Title>

        {flash?.success && (
          <Alert color="green" mt="md">{flash.success}</Alert>
        )}

        <Paper withBorder p="md" mt="md">
          <form onSubmit={handleSubmit}>
            <Stack>
              <PasswordInput label="Current Password" value={data.current_password} onChange={(e) => setData('current_password', e.currentTarget.value)} error={errors.current_password} required />
              <PasswordInput label="New Password" value={data.password} onChange={(e) => setData('password', e.currentTarget.value)} error={errors.password} required />
              <PasswordInput label="Confirm New Password" value={data.password_confirmation} onChange={(e) => setData('password_confirmation', e.currentTarget.value)} error={errors.password_confirmation} required />

              <Button type="submit" loading={processing}>Update Password</Button>
            </Stack>
          </form>
        </Paper>
      </Container>
    </>
  );
}

ChangePassword.layout = (page: React.ReactNode) => <MainLayout title="Change Password">{page}</MainLayout>;
