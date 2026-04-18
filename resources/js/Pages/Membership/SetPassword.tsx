import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import {
  Container,
  Card,
  Title,
  Text,
  Button,
  Stack,
  PasswordInput,
  Paper,
  ThemeIcon
} from '@mantine/core';
import { IconLock } from '@tabler/icons-react';
import AuthLayout from '../../Layout/AuthLayout';

interface SetPasswordProps {
  token: string;
  email: string;
}

export default function SetPassword({ token, email }: SetPasswordProps) {
  const { data, setData, post, processing, errors } = useForm({
    email: email,
    password: '',
    password_confirmation: '',
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post(`/membership/set-password/${token}`);
  };

  return (
    <>
      <Head title="Set Password" />

      <Container size={420} py="xl">
        <Stack align="center" gap="lg">
          <ThemeIcon size={60} radius="xl" color="blue" variant="light">
            <IconLock size={30} />
          </ThemeIcon>

          <div style={{ textAlign: 'center' }}>
            <Title order={2}>Set Your Password</Title>
            <Text c="dimmed" size="sm" mt="xs">
              Welcome! Please secure your account by creating a new password.
            </Text>
          </div>

          <Paper withBorder p="sm" bg="gray.0" w="100%" style={{ textAlign: 'center' }}>
            <Text size="sm" fw={500}>Account Email:</Text>
            <Text size="sm">{email}</Text>
          </Paper>

          <form onSubmit={handleSubmit} style={{ width: '100%' }}>
            <Stack gap="md">
              <PasswordInput
                label="New Password"
                placeholder="Enter your new password"
                value={data.password}
                onChange={(e) => setData('password', e.currentTarget.value)}
                error={errors.password}
                required
                minLength={8}
                description="Password must be at least 8 characters long"
              />

              <PasswordInput
                label="Confirm Password"
                placeholder="Confirm your new password"
                value={data.password_confirmation}
                onChange={(e) => setData('password_confirmation', e.currentTarget.value)}
                error={errors.password_confirmation}
                required
                minLength={8}
              />

              <Button
                type="submit"
                size="md"
                fullWidth
                loading={processing}
                mt="sm"
              >
                Set Password & Login
              </Button>
            </Stack>
          </form>
        </Stack>
      </Container>
    </>
  );
}

SetPassword.layout = (page: React.ReactNode) => (
  <AuthLayout title="Set Password">{page}</AuthLayout>
);
