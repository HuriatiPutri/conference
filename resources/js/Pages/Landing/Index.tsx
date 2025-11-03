import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import {
  ActionIcon,
  Anchor,
  BackgroundImage,
  Badge,
  Box,
  Button,
  Card,
  Center,
  Container,
  Divider,
  Grid,
  Group,
  Image,
  Overlay,
  SimpleGrid,
  Space,
  Stack,
  Text,
  Title
} from '@mantine/core';
import { IconCertificate, IconLicense, IconNetwork } from '@tabler/icons-react';
import PublicLayout from '../../Layout/PublicLayout';
import { Conference, PageProps } from '../../types';
import { formatCurrency } from '../../utils';

interface LandingPageProps extends PageProps {
  activeConferences: Conference[];
  allConferences: Conference[];
}

function LandingPage() {
  const { activeConferences, allConferences } = usePage<LandingPageProps>().props;

  console.log('Active Conferences:', activeConferences);
  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  };

  return (
    <Box>
      {/* Hero Section */}
      <BackgroundImage
        src="/images/hero.webp"
        h={{ base: 400, md: 500 }}
        w="100vw"
        style={{
          backgroundSize: 'cover',
          backgroundPosition: 'center',
          backgroundRepeat: 'no-repeat',
          width: '100%',
          minHeight: '100vh',

        }}
      >
        <Center h="100%">
          <Container size="lg">
            <Stack align="center" gap="xl">
              <Title
                order={1}
                c="white"
                ta="center"
                fw={700}
                style={{
                  textShadow: '2px 2px 4px rgba(0,0,0,0.5)',
                  fontSize: 'clamp(2.5rem, 5vw, 3.5rem)'
                }}
              >
                Empowering Global Knowledge Exchange
              </Title>
              <Text
                size="xl"
                c="white"
                ta="center"
                maw={600}
                style={{ textShadow: '1px 1px 2px rgba(0,0,0,0.5)' }}
              >
                Join the leading international conferences. Discover new knowledge,
                professional networks, and unlimited collaboration opportunities.
              </Text>
              <Group gap="md">
                <Button
                  size="lg"
                  radius="xl"
                  component={Link}
                  href="/register"
                  variant="filled"
                  color="blue"
                >
                  Register Now
                </Button>
                <Button
                  size="lg"
                  radius="xl"
                  variant="light"
                  color="white"
                  component="a"
                  href="#conferences"
                >
                  View Conferences
                </Button>
              </Group>
            </Stack>
          </Container>
        </Center>
      </BackgroundImage>

      <Space h="xl" />

      {/* Active Conferences Section */}
      {activeConferences.length > 0 && (
        <Container size="lg" py="xl">
          <Stack gap="xl">
            <div>
              <Title order={2} ta="center" mb="md">Open Conferences for Registration</Title>
              <Text c="dimmed" ta="center" size="lg">
                Register now for conferences that are currently open for registration
              </Text>
            </div>

            <SimpleGrid cols={{ base: 1, md: 2, lg: 3 }} spacing="lg">
              {activeConferences.map((conference) => (
                <Card key={conference.id} shadow="sm" padding="lg" radius="md" withBorder>
                  <Card.Section>
                    <Image
                      src={`storage/${conference.cover_poster_path}`}
                      height={160}
                      alt={conference.name}
                      style={{ objectFit: 'cover', width: '100%', height: '160px' }}
                    />
                  </Card.Section>

                  <Stack gap="sm" mt="md">
                    <Title order={4} lineClamp={2}>
                      {conference.name}
                    </Title>

                    <Text size="sm" c="dimmed" lineClamp={3}>
                      {conference.description || 'International conference with leading speakers from various countries.'}
                    </Text>

                    <Group gap="xs">
                      <Badge color="blue" variant="light">
                        {formatDate(conference.date)}
                      </Badge>
                      {conference.venue && (
                        <Badge color="gray" variant="light">
                          {conference.venue}
                        </Badge>
                      )}
                    </Group>

                    <Group justify="space-between" mt="md">
                      <Text fw={500} size="sm">
                        Starting from {formatCurrency(conference.participant_fee)}
                      </Text>
                      <Button
                        component={Link}
                        href={`/registration/${conference.public_id}`}
                        variant="light"
                        color="blue"
                        size="sm"
                        radius="xl"
                      >
                        Register
                      </Button>
                    </Group>
                  </Stack>
                </Card>
              ))}
            </SimpleGrid>
          </Stack>
        </Container>
      )}

      <Space h="xl" />
      <Divider />
      <Space h="xl" />

      {/* All Conferences Section */}
      <Container size="lg" py="xl" id="conferences">
        <Stack gap="xl">
          <div>
            <Title order={2} ta="center" mb="md">All Conferences</Title>
            <Text c="dimmed" ta="center" size="lg">
              Explore all conferences available on our platform
            </Text>
          </div>

          <SimpleGrid cols={{ base: 1, md: 2, lg: 3 }} spacing="lg">
            {allConferences.map((conference) => (
              <Card key={conference.id} shadow="sm" padding="lg" radius="md" withBorder>
                <Card.Section>
                  <Image
                    src={`storage/${conference.cover_poster_path}`}
                    height={160}
                    alt={conference.name}
                    style={{ objectFit: 'cover', width: '100%', height: '160px' }}
                  />
                </Card.Section>

                <Stack gap="sm" mt="md">
                  <Title order={5} lineClamp={2}>
                    {conference.name}
                  </Title>

                  <Text size="sm" c="dimmed" lineClamp={2}>
                    {conference.description || 'High-quality conference with interesting agenda.'}
                  </Text>

                  <Group gap="xs">
                    <Badge color="green" variant="light" size="sm">
                      {formatDate(conference.date)}
                    </Badge>
                    <Badge color="gray" variant="light" size="sm">
                      {conference.year}
                    </Badge>
                  </Group>

                  <Group justify="space-between" mt="sm">
                    <Text fw={500} size="sm">
                      {formatCurrency(conference.participant_fee)}
                    </Text>
                    <Button
                      component={Link}
                      href={`/detail/${conference.public_id}`}
                      variant="outline"
                      color="green"
                      size="sm"
                      radius="xl"
                    >
                      Details
                    </Button>
                  </Group>
                </Stack>
              </Card>
            ))}
          </SimpleGrid>

          {allConferences.length === 0 && (
            <Center h={200}>
              <Stack align="center" gap="md">
                <Text size="lg" c="dimmed">No conferences available yet</Text>
                <Button
                  component={Link}
                  href="/admin/conferences"
                  variant="light"
                >
                  Create New Conference
                </Button>
              </Stack>
            </Center>
          )}
        </Stack>
      </Container>

      <Space h="xl" />

      {/* Features Section */}
      <Box bg="gray.0" py="xl">
        <Container size="lg">
          <Stack gap="xl">
            <div>
              <Title order={2} ta="center" mb="md">Why Choose Our Platform?</Title>
              <Text c="dimmed" ta="center" size="lg">
                The most comprehensive conference management platform with advanced features
              </Text>
            </div>

            <SimpleGrid cols={{ base: 1, md: 3 }} spacing="xl">
              <Card shadow="sm" padding="xl" radius="md">
                <Stack align="center" gap="md">
                  <ActionIcon size={60} radius="50%" color="blue" variant="filled">
                    <IconLicense size={32} />
                  </ActionIcon>
                  <Title order={4} ta="center">Easy Registration</Title>
                  <Text ta="center" c="dimmed">
                    Simple and fast registration process with various payment methods
                  </Text>
                </Stack>
              </Card>

              <Card shadow="sm" padding="xl" radius="md">
                <Stack align="center" gap="md">
                  <ActionIcon size={60} radius="50%" color="green" variant="filled">
                    <IconCertificate size={32} />
                  </ActionIcon>
                  <Title order={4} ta="center">Digital Certificates</Title>
                  <Text ta="center" c="dimmed">
                    Get high-quality digital certificates after attending conferences
                  </Text>
                </Stack>
              </Card>

              <Card shadow="sm" padding="xl" radius="md">
                <Stack align="center" gap="md">
                  <ActionIcon size={60} radius="50%" color="orange" variant="filled">
                    <IconNetwork size={32} />
                  </ActionIcon>
                  <Title order={4} ta="center">Networking</Title>
                  <Text ta="center" c="dimmed">
                    Networking opportunities with experts and practitioners from various fields
                  </Text>
                </Stack>
              </Card>
            </SimpleGrid>
          </Stack>
        </Container>
      </Box>

      <Space h="xl" />

      {/* Footer */}
      <Box bg="dark" c="white" py="xl">
        <Container size="lg">
          <Grid>
            <Grid.Col span={{ base: 12, md: 6 }}>
              <Stack gap="sm">
                <Title order={4} c="white">SOTVI Conference</Title>
                <Text c="gray.4">
                  Leading platform for managing and attending international conferences
                  with complete features and latest technology.
                </Text>
              </Stack>
            </Grid.Col>
            <Grid.Col span={{ base: 12, md: 3 }}>
              <Stack gap="sm">
                <Title order={5} c="white">Quick Links</Title>
                <Anchor component={Link} href="/register" c="gray.4">Registration</Anchor>
                <Anchor component={Link} href="/#conferences" c="gray.4">Conferences</Anchor>
              </Stack>
            </Grid.Col>
            <Grid.Col span={{ base: 12, md: 3 }}>
              <Stack gap="sm">
                <Title order={5} c="white">Contact</Title>
                <Text c="gray.4">alde@sotvi.org</Text>
                {/* <Text c="gray.4">+62 123 456 789</Text> */}
              </Stack>
            </Grid.Col>
          </Grid>
          <Divider my="lg" color="gray.7" />
          <Text ta="center" c="gray.5">
            © 2025 Conference Management System. All rights reserved.
          </Text>
        </Container>
      </Box>
    </Box>
  );
}
LandingPage.layout = (page: React.ReactNode) => (
  <PublicLayout title="SOTVI Conference">{page}</PublicLayout>
);

export default LandingPage;