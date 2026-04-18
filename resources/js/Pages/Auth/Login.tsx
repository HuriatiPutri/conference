import React from 'react';
import { useForm } from '@inertiajs/react';
import {
  Paper,
  TextInput,
  PasswordInput,
  Button,
  Title,
  Text,
  Container,
  Alert,
  Checkbox,
  Stack,
  Anchor
} from '@mantine/core';
import { IconAlertCircle } from '@tabler/icons-react';
import AuthLayout from '../../Layout/AuthLayout';

interface LoginForm {
  email: string;
  password: string;
  remember: boolean;
}

export default function Login() {
  const { data, setData, post, processing, errors } = useForm<LoginForm>({
    email: '',
    password: '',
    remember: false,
  });

  function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    post(route('login'));
  }

  return (
    <Container size={420} my={40}>
      <Title ta="center">
        Sign In to SOTVI
      </Title>
      <Text c="dimmed" size="sm" ta="center" mb="xl">
        Welcome Back! Please enter your details to sign in.
      </Text>

      <Paper p={30} mt={20} radius="md">
        {errors.email && (
          <Alert
            icon={<IconAlertCircle size="1rem" />}
            title="Error!"
            color="red"
            mb="md"
          >
            {errors.email}
          </Alert>
        )}

        <form onSubmit={handleSubmit}>
          <Stack>
            <TextInput
              label="Email"
              placeholder="admin@example.com"
              required
              value={data.email}
              onChange={(e) => setData('email', e.target.value)}
              error={errors.email}
            />

            <PasswordInput
              label="Password"
              placeholder="Your password"
              required
              value={data.password}
              onChange={(e) => setData('password', e.target.value)}
              error={errors.password}
            />

            <Checkbox
              label="Remember me"
              checked={data.remember}
              onChange={(e) => setData('remember', e.currentTarget.checked)}
            />

            <Button
              type="submit"
              fullWidth
              loading={processing}>
              Sign in
            </Button>
          </Stack>
        </form>
        <Text ta="center" mt="md">
          Don&apos;t have an account?{' '}
          <Anchor href={'/register-membership'} fw={500}>
            Register
          </Anchor>
        </Text>
      </Paper>
    </Container>
  );
}
Login.layout = (page: React.ReactNode) => <AuthLayout title="Login">{page}</AuthLayout>;
