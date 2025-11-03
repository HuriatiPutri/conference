import React from "react";
import { Head } from "@inertiajs/react";
import {
  Container,
  Paper,
  Title,
  Text,
  Stack,
  Group,
  Button,
  ThemeIcon,
  Card,
  Divider,
  Center
} from "@mantine/core";
import { IconCalendarX, IconClock, IconHome, IconMail } from "@tabler/icons-react";
import AuthLayout from "../../Layout/AuthLayout";

interface Conference {
  name: string;
  registration_end_date?: string;
  initial?: string;
  city?: string;
  country?: string;
  date?: string;
}

export default function Closed({ conference }: { conference: Conference }) {
  const formatDate = (dateString: string | undefined) => {
    if (!dateString) return 'Not specified';
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  };

  return (
    <>
      <Head title={`Registration Closed - ${conference.name}`} />
      <Container size="md" py="xl">
        <Center>
          <Card shadow="md" padding="xl" radius="md" withBorder style={{ maxWidth: 600, width: '100%' }}>
            <Stack gap="lg" align="center">
              {/* Icon */}
              <ThemeIcon size={80} radius="xl" color="red" variant="light">
                <IconCalendarX size={40} />
              </ThemeIcon>

              {/* Main Title */}
              <Title order={1} ta="center" c="red">
                Registration Closed
              </Title>

              {/* Conference Name */}
              <Title order={2} ta="center" fw={600} c="dark">
                {conference.name}
              </Title>

              <Divider w="100%" />

              {/* Conference Details */}
              <Stack gap="md" w="100%">
                <Paper p="md" bg="gray.0" radius="md">
                  <Stack gap="sm">
                    <Group gap="sm">
                      <IconClock size={18} color="gray" />
                      <Text size="sm" fw={500}>Registration Period</Text>
                    </Group>
                    <Text size="sm" c="dimmed" pl="md">
                      Registration for this conference has ended on{' '}
                      <Text component="span" fw={600} c="red">
                        {formatDate(conference.registration_end_date)}
                      </Text>
                    </Text>
                  </Stack>
                </Paper>

                {conference.date && (
                  <Paper p="md" bg="blue.0" radius="md">
                    <Stack gap="sm">
                      <Group gap="sm">
                        <IconCalendarX size={18} color="blue" />
                        <Text size="sm" fw={500}>Conference Date</Text>
                      </Group>
                      <Text size="sm" c="dimmed" pl="md">
                        {formatDate(conference.date)}
                        {conference.city && conference.country && (
                          <Text component="span" c="blue">
                            {' '} in {conference.city}, {conference.country}
                          </Text>
                        )}
                      </Text>
                    </Stack>
                  </Paper>
                )}
              </Stack>

              <Divider w="100%" />

              {/* Message */}
              <Stack gap="sm" ta="center">
                <Text size="lg" fw={500} c="dark">
                  We apologize for any inconvenience
                </Text>
                <Text size="sm" c="dimmed" ta="center" lh={1.6}>
                  The registration period for this conference has ended.
                  Please check our website for information about future conferences
                  or contact us if you have any questions.
                </Text>
              </Stack>

              {/* Action Buttons */}
              <Group gap="md" mt="md">
                <Button
                  leftSection={<IconHome size={16} />}
                  variant="filled"
                  color="blue"
                  onClick={() => window.location.href = '/'}
                >
                  Back to Home
                </Button>

                <Button
                  leftSection={<IconMail size={16} />}
                  variant="outline"
                  color="gray"
                  onClick={() => window.location.href = 'mailto:alde@sotvi.org'}
                >
                  Contact Us
                </Button>
              </Group>

              {/* Footer Note */}
              <Text size="xs" c="dimmed" ta="center" mt="lg">
                For updates on future conferences, please visit our website regularly
                or subscribe to our newsletter.
              </Text>
            </Stack>
          </Card>
        </Center>
      </Container>
    </>
  )
}

Closed.layout = (page: React.ReactNode) => (
  <AuthLayout title="Conference Closed">{page}</AuthLayout>
);