import { router, useForm } from '@inertiajs/react';
import {
  ActionIcon,
  Button,
  Card,
  Container,
  Divider,
  Flex,
  Grid,
  Group,
  Stack,
  Text,
  Textarea,
  TextInput,
  Title
} from '@mantine/core';
import { notifications } from '@mantine/notifications';
import React, { useState } from 'react';
import { route } from 'ziggy-js';
import MainLayout from '../../../Layout/MainLayout';
import { IconArrowLeft } from '@tabler/icons-react';

function ConferenceCreate() {
  const [rooms, setRooms] = useState([{ room_name: '' }]);
  const { data, setData, errors, post, processing } = useForm({
    name: '',
    description: '',
    initial: '',
    cover_poster_path: null as File | null,
    date: '',
    registration_start_date: '',
    registration_end_date: '',
    year: new Date().getFullYear(),
    city: '',
    country: '',
    online_fee: 0,
    online_fee_usd: 0,
    onsite_fee: 0,
    onsite_fee_usd: 0,
    participant_fee: 0,
    participant_fee_usd: 0,
    rooms: rooms,
  });

  function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    console.log('submitting', data);
    post(route('conferences.store'), {
      onSuccess: () => {
        notifications.show({ message: 'Conference created successfully!', color: 'green' });
      },
      onError: (e) => {
        console.log('error', e);
      },
    });
  }

  const addRoom = () => {
    const next = [...rooms, { room_name: '' }];
    setRooms(next);
    setData('rooms', next);
  };

  return (
    <Container size="md" py="xl">
      <Stack gap="lg">
        <Group justify="space-between">
          <div>
            <Title order={2}>Add New Conference</Title>
            <Text c="dimmed" size="sm">
              Create a new conference and set its details
            </Text>
          </div>
          <Button
            variant="subtle"
            leftSection={<IconArrowLeft size={16} />}
            onClick={() => router.visit('/conferences')}
          >
            Back
          </Button>
        </Group>
        <Card padding="lg" radius="md" withBorder>
          <form onSubmit={handleSubmit}>
            <Stack gap={'md'} mb={'md'}>
              <TextInput
                label="Conference Name"
                name="name"
                required
                error={errors.name}
                value={data.name}
                onChange={e => setData('name', e.target.value)}
              />

              <TextInput
                label="Conference Initial"
                name="initial"
                required
                error={errors.initial}
                value={data.initial}
                onChange={e => setData('initial', e.target.value)}
                placeholder="Example: SAFE2024, ICOAS2025"
              />

              <Textarea
                label="Conference Description"
                name="description"
                minRows={4}
                error={errors.description}
                value={data.description}
                onChange={e => setData('description', e.target.value)}
              />

              <TextInput
                label="Conference Date"
                name="date"
                type="date"
                required
                error={errors.date}
                value={data.date}
                onChange={e => setData('date', e.target.value)}
              />
              <Grid>
                <Grid.Col span={6}>
                  <TextInput
                    label="Registration Start Date"
                    name="registration_start_date"
                    type="date"
                    required
                    error={errors.registration_start_date}
                    value={data.registration_start_date}
                    onChange={e => setData('registration_start_date', e.target.value)}
                  />
                </Grid.Col>
                <Grid.Col span={6}>
                  <TextInput
                    label="Registration End Date"
                    name="registration_end_date"
                    type="date"
                    required
                    error={errors.registration_end_date}
                    value={data.registration_end_date}
                    onChange={e => setData('registration_end_date', e.target.value)}
                  />
                </Grid.Col>
              </Grid>

              <TextInput
                label="Conference Cover Poster"
                name="cover_poster_path"
                type="file"
                error={errors.cover_poster_path}
                onChange={(e: React.ChangeEvent<HTMLInputElement>) => setData('cover_poster_path', e.target.files?.[0] ?? null)}
              />

              <div className="grid gap-2 lg:grid-cols-2">
                <TextInput
                  label="City"
                  name="city"
                  required
                  error={errors.city}
                  value={data.city}
                  onChange={e => setData('city', e.target.value)}
                />
                <TextInput
                  label="Country"
                  name="country"
                  required
                  error={errors.country}
                  value={data.country}
                  onChange={e => setData('country', e.target.value)}
                />
              </div>

              <TextInput
                label="Year"
                name="year"
                type="number"
                required
                error={errors.year}
                value={data.year}
                onChange={e => setData('year', Number(e.target.value))}
              />
            </Stack>

            <Divider my={'md'} />
            <Text fw={700} fz={'lg'}>
              Registration Fees
            </Text>
            <Grid>
              <Grid.Col span={{ base: 12, md: 6 }}>
                <Text fw={600}>Domestic Participants (IDR)</Text>

                <TextInput
                  label="Online Fee (IDR)"
                  name="online_fee"
                  type="number"
                  required
                  error={errors.online_fee}
                  value={data.online_fee}
                  onChange={e => setData('online_fee', Number(e.target.value))}
                />

                <TextInput
                  label="Onsite Fee (IDR)"
                  name="onsite_fee"
                  type="number"
                  required
                  error={errors.onsite_fee}
                  value={data.onsite_fee}
                  onChange={e => setData('onsite_fee', Number(e.target.value))}
                />

                <TextInput
                  label="Participant Only Fee (IDR)"
                  name="participant_fee"
                  type="number"
                  required
                  error={errors.participant_fee}
                  value={data.participant_fee}
                  onChange={e => setData('participant_fee', Number(e.target.value))}
                />
              </Grid.Col>
              <Grid.Col span={{ base: 12, md: 6 }}>
                <Text fw={600}>International Participants (USD)</Text>
                <TextInput
                  label="Onsite Fee (USD)"
                  name="onsite_fee_usd"
                  type="number"
                  required
                  error={errors.onsite_fee_usd}
                  value={data.onsite_fee_usd}
                  onChange={e => setData('onsite_fee_usd', Number(e.target.value))}
                />

                <TextInput
                  label="Online Fee (USD)"
                  name="online_fee_usd"
                  type="number"
                  required
                  error={errors.online_fee_usd}
                  value={data.online_fee_usd}
                  onChange={e => setData('online_fee_usd', Number(e.target.value))}
                />

                <TextInput
                  label="Participant Only Fee (USD)"
                  name="participant_fee_usd"
                  type="number"
                  required
                  error={errors.participant_fee_usd}
                  value={data.participant_fee_usd}
                  onChange={e => setData('participant_fee_usd', Number(e.target.value))}
                />
              </Grid.Col>
            </Grid>

            <Divider my={'md'} />
            <Flex justify={'space-between'} align={'center'} mb={'sm'}>
              <Text fw={700} fz={'lg'}>
                Rooms
              </Text>
              <ActionIcon color="blue" onClick={addRoom}>
                <i className="pi pi-plus" />
              </ActionIcon>
            </Flex>

            {rooms.map((room, index) => (
              <TextInput
                key={index}
                label={`Room Name ${index + 1}`}
                name={`rooms[${index}].room_name`}
                required
                error={Array.isArray(errors.rooms) ? (errors.rooms as string[])[index] : undefined}
                value={room.room_name}
                onChange={e => {
                  const newRooms = [...rooms];
                  newRooms[index] = { ...newRooms[index], room_name: e.target.value };
                  setRooms(newRooms);
                  setData('rooms', newRooms);
                }}
              />
            ))}

            <Button loading={processing} type="submit" mt={'md'} fullWidth>
              Create Conference
            </Button>
          </form>
        </Card>
      </Stack>
    </Container>
  );
}

ConferenceCreate.layout = (page: React.ReactNode) => (
  <MainLayout title="Create Conference">{page}</MainLayout>
);

export default ConferenceCreate;
