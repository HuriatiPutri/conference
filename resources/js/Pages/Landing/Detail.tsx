import React from "react";
import { Head, usePage } from "@inertiajs/react";
import {
  Container,
  Grid,
  Card,
  Title,
  Text,
  Badge,
  Group,
  Stack,
  Paper,
  Button,
  ThemeIcon,
  Box,
  SimpleGrid,
  BackgroundImage,
  Overlay,
  Center,
  Flex
} from "@mantine/core";
import {
  IconCalendar,
  IconMapPin,
  IconClock,
  IconUsers,
  IconWorld,
  IconBuilding,
  IconMail,
  IconExternalLink,
  IconDownload,
  IconUserCheck,
  IconCertificate
} from "@tabler/icons-react";
import { Conference, PageProps } from "../../types";
import PublicLayout from "../../Layout/PublicLayout";

interface DetailPageProps extends PageProps {
  conference: Conference;
}

export default function Detail() {
  const { conference } = usePage<DetailPageProps>().props;

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  };

  const formatCurrency = (amount: number, currency: 'idr' | 'usd' = 'idr') => {
    if (currency === 'usd') {
      return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
      }).format(amount);
    }
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0
    }).format(amount);
  };

  const isRegistrationOpen = () => {
    const now = new Date();
    const startDate = new Date(conference.registration_start_date);
    const endDate = new Date(conference.registration_end_date);
    return now >= startDate && now <= endDate;
  };

  return (
    <>
      <Head title={`${conference.name} - Conference Detail`} />

      {/* Hero Section */}
      <Box pos="relative" h={400}>
        {conference.cover_poster_path ? (
          <BackgroundImage
            src={`/storage/${conference.cover_poster_path}`}
            h={400}
            radius={0}
          >
            <Overlay color="#000" opacity={0.7} />
          </BackgroundImage>
        ) : (
          <Paper h={400} bg="gradient-to-r from-blue-600 to-blue-800" radius={0}>
            <Center h="100%">
              <Container size="xl">
                <Stack gap="md" align="center" ta="center">
                  <Badge size="lg" variant="light" color="white">
                    {conference.initial}
                  </Badge>
                  <Title order={1} c="white" size="3rem" fw={700}>
                    {conference.name}
                  </Title>
                  <Text size="xl" c="white" fw={500}>
                    {conference.city}, {conference.country} • {conference.year}
                  </Text>
                </Stack>
              </Container>
            </Center>
          </Paper>
        )}
      </Box>

      <Container size="xl" py="xl">
        <Grid>
          {/* Main Content */}
          <Grid.Col span={{ base: 12, md: 8 }}>
            <Stack gap="xl">
              {/* Conference Overview */}
              <Card padding="xl" radius="md" withBorder>
                <Stack gap="lg">
                  <Title order={2}>Conference Overview</Title>

                  {conference.description && (
                    <Text size="md" lh={1.7} c="dark">
                      {conference.description}
                    </Text>
                  )}

                  <SimpleGrid cols={{ base: 1, sm: 2 }} spacing="md">
                    <Card p="md" radius="md" withBorder>
                      <Group gap="sm">
                        <ThemeIcon color="blue" variant="light" size="lg">
                          <IconCalendar size={20} />
                        </ThemeIcon>
                        <Stack gap={2}>
                          <Text size="sm" fw={600} c="blue">Conference Date</Text>
                          <Text size="sm" c="dimmed">
                            {formatDate(conference.date)}
                          </Text>
                        </Stack>
                      </Group>
                    </Card>

                    <Card p="md" radius="md" withBorder>
                      <Group gap="sm">
                        <ThemeIcon color="green" variant="light" size="lg">
                          <IconMapPin size={20} />
                        </ThemeIcon>
                        <Stack gap={2}>
                          <Text size="sm" fw={600} c="green">Location</Text>
                          <Text size="sm" c="dimmed">
                            {conference.venue ? `${conference.venue}, ` : ''}{conference.city}, {conference.country}
                          </Text>
                        </Stack>
                      </Group>
                    </Card>

                    <Card p="md" radius="md" withBorder>
                      <Flex gap="sm">
                        <ThemeIcon color="orange" variant="light" size="lg">
                          <IconClock size={20} />
                        </ThemeIcon>
                        <Stack gap={2}>
                          <Text size="sm" fw={600} c="orange">Registration Period</Text>
                          <Text size="sm" c="dimmed">
                            {formatDate(conference.registration_start_date)} - {formatDate(conference.registration_end_date)}
                          </Text>
                        </Stack>
                      </Flex>
                    </Card>

                    <Card p="md" radius="md" withBorder>
                      <Group gap="sm">
                        <ThemeIcon color="grape" variant="light" size="lg">
                          <IconUsers size={20} />
                        </ThemeIcon>
                        <Stack gap={2}>
                          <Text size="sm" fw={600} c="grape">Status</Text>
                          <Badge
                            color={isRegistrationOpen() ? "green" : "red"}
                            variant="filled"
                            size="sm"
                          >
                            {isRegistrationOpen() ? "Registration Open" : "Registration Closed"}
                          </Badge>
                        </Stack>
                      </Group>
                    </Card>
                  </SimpleGrid>
                </Stack>
              </Card>

              {/* Registration Fees */}
              <Card padding="xl" radius="md" withBorder>
                <Stack gap="lg">
                  <Title order={2}>Registration Fees</Title>

                  <SimpleGrid cols={{ base: 1, sm: 2, md: 3 }} spacing="md">
                    {/* Online Participation */}
                    <Paper p="lg" bg="blue.0" radius="md">
                      <Stack gap="sm" align="center" ta="center">
                        <ThemeIcon color="blue" variant="light" size={50}>
                          <IconWorld size={24} />
                        </ThemeIcon>
                        <Text fw={600} c="blue">Online Participation</Text>
                        <Stack gap={4}>
                          <Text size="lg" fw={700} c="blue">
                            {formatCurrency(conference.online_fee, 'idr')}
                          </Text>
                          <Text size="sm" c="dimmed">
                            {formatCurrency(conference.online_fee_usd, 'usd')}
                          </Text>
                        </Stack>
                      </Stack>
                    </Paper>

                    {/* Onsite Participation */}
                    <Paper p="lg" bg="green.0" radius="md">
                      <Stack gap="sm" align="center" ta="center">
                        <ThemeIcon color="green" variant="light" size={50}>
                          <IconBuilding size={24} />
                        </ThemeIcon>
                        <Text fw={600} c="green">Onsite Participation</Text>
                        <Stack gap={4}>
                          <Text size="lg" fw={700} c="green">
                            {formatCurrency(conference.onsite_fee, 'idr')}
                          </Text>
                          <Text size="sm" c="dimmed">
                            {formatCurrency(conference.onsite_fee_usd, 'usd')}
                          </Text>
                        </Stack>
                      </Stack>
                    </Paper>

                    {/* Participant Only */}
                    <Paper p="lg" bg="orange.0" radius="md">
                      <Stack gap="sm" align="center" ta="center">
                        <ThemeIcon color="orange" variant="light" size={50}>
                          <IconUserCheck size={24} />
                        </ThemeIcon>
                        <Text fw={600} c="orange">Participant Only</Text>
                        <Stack gap={4}>
                          <Text size="lg" fw={700} c="orange">
                            {formatCurrency(conference.participant_fee, 'idr')}
                          </Text>
                          <Text size="sm" c="dimmed">
                            {formatCurrency(conference.participant_fee_usd, 'usd')}
                          </Text>
                        </Stack>
                      </Stack>
                    </Paper>
                  </SimpleGrid>

                  <Text size="sm" c="dimmed" ta="center">
                    * All fees include conference materials, certificate, and refreshments
                  </Text>
                </Stack>
              </Card>
            </Stack>
          </Grid.Col>

          {/* Sidebar */}
          <Grid.Col span={{ base: 12, md: 4 }}>
            <Stack gap="lg">
              {/* Registration CTA */}
              <Card padding="xl" radius="md" withBorder>
                <Stack gap="md" align="center" ta="center">
                  <ThemeIcon color="blue" variant="gradient" size={60}>
                    <IconUserCheck size={30} />
                  </ThemeIcon>
                  <Title order={3} c="blue">Ready to Join?</Title>
                  <Text size="sm" c="dimmed" ta="center">
                    Register now and be part of this amazing conference experience
                  </Text>

                  {isRegistrationOpen() ? (
                    <Button
                      size="lg"
                      fullWidth
                      leftSection={<IconExternalLink size={18} />}
                      onClick={() => window.location.href = `/registration/${conference.public_id}`}
                    >
                      Register Now
                    </Button>
                  ) : (
                    <Button
                      size="lg"
                      fullWidth
                      variant="outline"
                      color="gray"
                      disabled
                    >
                      Registration Closed
                    </Button>
                  )}
                </Stack>
              </Card>

              {/* Quick Actions */}
              <Card padding="lg" radius="md" withBorder>
                <Stack gap="md">
                  <Title order={4} c="dark">Quick Actions</Title>

                  <Button
                    variant="outline"
                    leftSection={<IconDownload size={16} />}
                    fullWidth
                    onClick={() => window.open(`/storage/${conference.cover_poster_path}`, '_blank')}
                  >
                    Download Poster
                  </Button>

                  <Button
                    variant="outline"
                    color="green"
                    leftSection={<IconMail size={16} />}
                    fullWidth
                    onClick={() => window.location.href = 'mailto:info@conference.com'}
                  >
                    Contact Organizer
                  </Button>

                  <Button
                    variant="outline"
                    color="orange"
                    leftSection={<IconCertificate size={16} />}
                    fullWidth
                    onClick={() => window.location.href = `/keynote/${conference.public_id}`}
                  >
                    Submit Keynote
                  </Button>
                  <Button
                    variant="outline"
                    color="black"
                    leftSection={<IconCertificate size={16} />}
                    fullWidth
                    onClick={() => window.location.href = `/parallel-session/${conference.public_id}`}
                  >
                    Submit Parallel Session
                  </Button>
                </Stack>
              </Card>

              {/* Conference Info */}
              <Card padding="lg" radius="md" withBorder>
                <Stack gap="md">
                  <Title order={4} c="dark">Conference Information</Title>

                  <Stack gap="sm">
                    <Group gap="sm">
                      <IconCalendar size={16} color="gray" />
                      <Text size="sm" c="dimmed">
                        Started: {formatDate(conference.registration_start_date)}
                      </Text>
                    </Group>

                    <Group gap="sm">
                      <IconClock size={16} color="gray" />
                      <Text size="sm" c="dimmed">
                        Deadline: {formatDate(conference.registration_end_date)}
                      </Text>
                    </Group>

                    <Group gap="sm">
                      <IconMapPin size={16} color="gray" />
                      <Text size="sm" c="dimmed">
                        {conference.city}, {conference.country}
                      </Text>
                    </Group>
                  </Stack>
                </Stack>
              </Card>
            </Stack>
          </Grid.Col>
        </Grid>
      </Container>
    </>
  );
}

Detail.layout = (page: React.ReactNode) => (
  <PublicLayout title="SOTVI Conference">{page}</PublicLayout>
);

