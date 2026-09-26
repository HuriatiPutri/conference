import { Head, Link } from '@inertiajs/react';
import {
  Alert,
  Button,
  Container,
  Group,
  Paper,
  Stack,
  Text,
  ThemeIcon,
  Title
} from '@mantine/core';
import { IconCircleCheck, IconClock, IconMail } from '@tabler/icons-react';
import React from 'react';
import AuthLayout from '../../Layout/AuthLayout';

interface Package {
  id: number;
  name: string;
}

interface Membership {
  public_id: string;
  first_name: string;
  last_name: string;
  email: string;
  status: string;
}

interface PaymentCompleteProps {
  membership: Membership;
  package: Package;
}

export default function MembershipPaymentComplete({ membership, package: packageData }: PaymentCompleteProps) {
  // Check the last invoice status if passed, or rely on membership status
  const isPending = membership.status === 'pending';

  return (
    <>
      <Head title="Payment Complete" />

      <Container size="sm" py="xl">
        <Stack align="center" gap="lg" ta="center">
          {isPending ? (
            <>
              <ThemeIcon size={80} radius="xl" color="orange" variant="light">
                <IconClock size={40} />
              </ThemeIcon>
              <Title order={2}>Payment Under Review</Title>
              <Text size="lg" c="black">
                We have received your payment proof for the <strong>{packageData.name}</strong> subscription.
              </Text>
              <Alert color="orange" title="What's next?" variant="light" ta="left" w="100%">
                Our administration team will verify your transfer soon. Please allow 1-2 business days for the verification process.
                Once verified, you will receive an email to set up your password.
              </Alert>
            </>
          ) : (
            <>
              <ThemeIcon size={80} radius="xl" color="green" variant="light">
                <IconCircleCheck size={40} />
              </ThemeIcon>
              <Title order={2}>Payment Successful!</Title>
              <Text size="lg" c="black">
                Thank you, <strong>{membership.first_name}</strong>. Your payment for the <strong>{packageData.name}</strong> subscription was successful.
              </Text>

              <Paper withBorder p="md" bg="blue.0" w="100%">
                <Group align="flex-start" gap="sm">
                  <ThemeIcon color="blue" variant="light" mt={2}>
                    <IconMail size={16} />
                  </ThemeIcon>
                  <div style={{ flex: 1, textAlign: 'left' }}>
                    <Text fw={500}>Check your email</Text>
                    <Text size="sm">
                      An email has been sent to <strong>{membership.email}</strong> with a link to set up your account password.
                      Please set your password before logging in.
                    </Text>
                  </div>
                </Group>
              </Paper>
            </>
          )}

          <Button
            component={Link}
            href="/"
            variant="default"
            size="md"
            fullWidth
            mt="md"
          >
            Return to Homepage
          </Button>
        </Stack>
      </Container>
    </>
  );
}

MembershipPaymentComplete.layout = (page: React.ReactNode) => (
  <AuthLayout title="Payment Complete">{page}</AuthLayout>
);
