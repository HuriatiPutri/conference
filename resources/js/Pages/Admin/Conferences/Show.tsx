import { usePage } from '@inertiajs/react';
import { Button, Card, Container, Divider, Flex, Grid, Group, Image, Stack, Text, Textarea, TextInput, Title } from '@mantine/core';
import React from 'react';
import { CopyButtonExt } from './ExtendComponent';
import { Conference, Room } from '../../../types';
import MainLayout from '../../../Layout/MainLayout';
import { IconArrowLeft } from '@tabler/icons-react';

export default function ConferenceShow() {
  const { conference } = usePage<{ conference: Conference }>().props;
  const { data } = conference;

  // Helper fallback
  const val = (v: unknown): string => (v === null || v === undefined || v === '' ? '-' : String(v));

  const posterUrl = React.useMemo(() => {
    if (!data.cover_poster_path) return null;
    if (/^https?:\/\//i.test(data.cover_poster_path)) return data.cover_poster_path;
    const filename = data.cover_poster_path.replace(/^conference_posters\//, '');
    return `/storage/conference_posters/${filename}`;
  }, [data.cover_poster_path]);

  const rooms = (data.rooms as Room[]) ?? [];

  return (
    <Container size="md" py="xl">
      <Stack gap="lg">
        <Group justify="space-between">
          <div>
            <Title order={2}>Conference Details</Title>
            <Text c="dimmed" size="sm">
              View conference information and settings
            </Text>
          </div>
          <Button
            variant="subtle"
            leftSection={<IconArrowLeft size={16} />}
            onClick={() => window.history.back()}
          >
            Back
          </Button>
        </Group>
        <Card padding="lg" radius="md" withBorder>
          <form /* hanya untuk layout konsisten */>
            <Stack gap="md" mb="md">
              <TextInput label="Conference Name" value={val(data.name)} readOnly />
              <TextInput label="Conference Initial" value={val(data.initial)} readOnly />
              <Textarea label="Conference Description" value={val(data.description)} readOnly />
              {posterUrl && (
                <div>
                  <Text fz="sm" fw={500} mb={4}>
                    Cover Poster
                  </Text>
                  <Image
                    src={posterUrl}
                    alt="Cover Poster"
                    radius="sm"
                    h={180}
                    w="auto"
                    fit="contain"
                  />
                </div>
              )}
              <TextInput label="Conference Date" value={val(data.date)} readOnly />
              <Grid>
                <Grid.Col span={{ base: 12, sm: 6 }}>
                  <TextInput label="Registration Start Date" value={val(data.registration_start_date)} readOnly />
                </Grid.Col>
                <Grid.Col span={{ base: 12, sm: 6 }}>
                  <TextInput label="Registration End Date" value={val(data.registration_end_date)} readOnly />
                </Grid.Col>
              </Grid>
              <Grid>
                <Grid.Col span={{ base: 12, sm: 6 }}>
                  <TextInput label="City" value={val(data.city)} readOnly />
                </Grid.Col>
                <Grid.Col span={{ base: 12, sm: 6 }}>
                  <TextInput label="Country" value={val(data.country)} readOnly />
                </Grid.Col>
              </Grid>
              <TextInput label="Year" value={val(data.year)} readOnly />
            </Stack>
            <Grid>
              <Grid.Col span={{ base: 12, sm: 6 }}>
                <Text>Registration Links</Text>
              </Grid.Col>
              <Grid.Col span={{ base: 12, sm: 6 }}>
                <Flex gap={'xs'}>
                  <CopyButtonExt value={'abc'} label={'Registration'} />
                  <CopyButtonExt value={'abc'} label={'Key Note'} />
                  <CopyButtonExt value={'abc'} label={'Parallel Session'} />
                </Flex>
              </Grid.Col>
            </Grid>
            <Divider my="md" />
            <Text fw={700} fz="lg">
              Registration Fees
            </Text>
            <Grid mt="xs">
              <Grid.Col span={{ base: 12, md: 6 }}>
                <Text fw={600}>Domestic Participants (IDR)</Text>
                <TextInput label="Online Fee (IDR)" value={val(data.online_fee)} readOnly />
                <TextInput label="Onsite Fee (IDR)" value={val(data.onsite_fee)} readOnly />
                <TextInput
                  label="Participant Only Fee (IDR)"
                  value={val(data.participant_fee)}
                  readOnly
                />
              </Grid.Col>
              <Grid.Col span={{ base: 12, md: 6 }}>
                <Text fw={600}>International Participants (USD)</Text>
                <TextInput label="Online Fee (USD)" value={val(data.online_fee_usd)} readOnly />
                <TextInput label="Onsite Fee (USD)" value={val(data.onsite_fee_usd)} readOnly />
                <TextInput
                  label="Participant Only Fee (USD)"
                  value={val(data.participant_fee_usd)}
                  readOnly
                />
              </Grid.Col>
            </Grid>

            <Divider my="md" />
            <Flex justify="space-between" align="center" mb="sm">
              <Text fw={700} fz="lg">
                Rooms
              </Text>
            </Flex>

            <Stack gap="sm">
              {rooms.length === 0 && (
                <Text fz="sm" c="dimmed">
                  No rooms available.
                </Text>
              )}
              {rooms.map((r, i) => (
                <TextInput
                  key={r.id ?? i}
                  label={`Room Name ${i + 1}`}
                  value={val(r.room_name)}
                  readOnly
                />
              ))}
            </Stack>
          </form>
        </Card>
      </Stack>
    </Container>
  );
}

ConferenceShow.layout = (page: React.ReactNode) => (
  <MainLayout title="Conference Details">{page}</MainLayout>
);
