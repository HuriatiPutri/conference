import { useForm, usePage } from '@inertiajs/react';
import { ActionIcon, Button, Divider, Flex, Grid, Image, Stack, Text, TextInput } from '@mantine/core';
import { notifications } from '@mantine/notifications';
import React, { useState } from 'react';
import { route } from 'ziggy-js';
import MainLayout from '../../../Layout/MainLayout';
import { Conference } from '../../../types';
import styles from './styles.module.css';

function ConferenceCreate() {
  const { conference } = usePage<{ conference: Conference }>().props;
  const [rooms, setRooms] = useState(conference.data.rooms || [{ room_name: '' }]);
  const { data, setData, errors, post, processing } = useForm({
    name: conference.data.name || '',
    initial: conference.data.initial || '',
    cover_poster_path: null as File | null,
    date: conference.data.date || '',
    year: conference.data.year || new Date().getFullYear(),
    city: conference.data.city || '',
    country: conference.data.country || '',
    online_fee: conference.data.online_fee || 0,
    online_fee_usd: conference.data.online_fee_usd || 0,
    onsite_fee: conference.data.onsite_fee || 0,
    onsite_fee_usd: conference.data.onsite_fee_usd || 0,
    participant_fee: conference.data.participant_fee || 0,
    participant_fee_usd: conference.data.participant_fee_usd || 0,
    rooms: rooms,
  });

  const posterUrl = React.useMemo(() => {
    if (!conference.data.cover_poster_path) return null;
    if (/^https?:\/\//i.test(conference.data.cover_poster_path)) return conference.data.cover_poster_path;
    const filename = conference.data.cover_poster_path.replace(/^conference_posters\//, '');
    return `/storage/conference_posters/${filename}`;
  }, [conference.data.cover_poster_path]);

  function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    post(route('conferences.update', conference.data.id), {
      forceFormData: true,
      onSuccess: () => {
        notifications.show({ title: 'Success', message: 'Conference updated successfully!', color: 'green' });
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
    <div className={styles.card}>
      <Text c={'#101010'} fw={700}>
        Formulir Update Data Konferensi
      </Text>
      <Grid mt={'md'}>
        <Grid.Col span={{ base: 12, sm: 2 }}>
          <Text c={'#101010'} fw={700} fz={'lg'}>
            Data Conference
          </Text>
          <Text fz={'sm'} c={'#606060'}>
            Silakan perbarui formulir di sebelah kanan untuk mengubah data konferensi
          </Text>
        </Grid.Col>
        <Grid.Col span={{ base: 12, sm: 10 }}>
          {' '}
          <form onSubmit={handleSubmit}>
            <Stack gap={'md'} mb={'md'} mt={'md'}>
              <TextInput
                label="Nama Konferensi"
                name="name"
                required
                error={errors.name}
                value={data.name}
                onChange={e => setData('name', e.target.value)}
              />

              <TextInput
                label="Inisial Konferensi"
                name="initial"
                required
                error={errors.initial}
                value={data.initial}
                onChange={e => setData('initial', e.target.value)}
                placeholder="Contoh: SAFE2024, ICOAS2025"
              />

              <TextInput
                label="Tanggal Konferensi"
                name="date"
                type="date"
                required
                error={errors.date}
                value={data.date}
                onChange={e => setData('date', e.target.value)}
              />

              <TextInput
                label="Cover Poster Konferensi"
                name="cover_poster_path"
                type="file"
                accept="image/png,image/jpeg"
                error={errors.cover_poster_path}
                onChange={(e: any) => setData('cover_poster_path', e.target.files?.[0] ?? '')}
              />

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

              <div className="grid gap-2 lg:grid-cols-2">
                <TextInput
                  label="Kota"
                  name="city"
                  required
                  error={errors.city}
                  value={data.city}
                  onChange={e => setData('city', e.target.value)}
                />
                <TextInput
                  label="Negara"
                  name="country"
                  required
                  error={errors.country}
                  value={data.country}
                  onChange={e => setData('country', e.target.value)}
                />
              </div>

              <TextInput
                label="Tahun"
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
              Biaya Pendaftaran
            </Text>
            <Grid>
              <Grid.Col span={{ base: 12, md: 6 }}>
                <Text fw={600}>Partisipan Nasional (IDR)</Text>

                <TextInput
                  label="Biaya Online (IDR)"
                  name="online_fee"
                  type="number"
                  required
                  error={errors.online_fee}
                  value={data.online_fee}
                  onChange={e => setData('online_fee', Number(e.target.value))}
                />

                <TextInput
                  label="Biaya Onsite (IDR)"
                  name="onsite_fee"
                  type="number"
                  required
                  error={errors.onsite_fee}
                  value={data.onsite_fee}
                  onChange={e => setData('onsite_fee', Number(e.target.value))}
                />

                <TextInput
                  label="Biaya Partisipan Saja (IDR)"
                  name="participant_fee"
                  type="number"
                  required
                  error={errors.participant_fee}
                  value={data.participant_fee}
                  onChange={e => setData('participant_fee', Number(e.target.value))}
                />
              </Grid.Col>
              <Grid.Col span={{ base: 12, md: 6 }}>
                <Text fw={600}>Partisipan Internasional (USD)</Text>
                <TextInput
                  label="Biaya Onsite (USD)"
                  name="onsite_fee_usd"
                  type="number"
                  required
                  error={errors.onsite_fee_usd}
                  value={data.onsite_fee_usd}
                  onChange={e => setData('onsite_fee_usd', Number(e.target.value))}
                />

                <TextInput
                  label="Biaya Online (USD)"
                  name="online_fee_usd"
                  type="number"
                  required
                  error={errors.online_fee_usd}
                  value={data.online_fee_usd}
                  onChange={e => setData('online_fee_usd', Number(e.target.value))}
                />

                <TextInput
                  label="Biaya Partisipan Saja (USD)"
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
                Ruangan
              </Text>
              <ActionIcon color="blue" onClick={addRoom}>
                <i className="pi pi-plus" />
              </ActionIcon>
            </Flex>

            {rooms.map((room: any, index: number) => (
              <Grid key={index} align="end" mb="sm" w={'100%'}>
                <TextInput type="hidden" value={room.id} readOnly />
                <Grid.Col span={11}>
                  <TextInput
                    key={index}
                    label={`Nama Ruangan ${index + 1}`}
                    name={`rooms[${index}].room_name`}
                    required
                    error={Array.isArray(errors.rooms) ? (errors.rooms as any)[index] : undefined}
                    value={room.room_name}
                    onChange={e => {
                      const newRooms = [...rooms];
                      newRooms[index] = { ...newRooms[index], room_name: e.target.value };
                      setRooms(newRooms);
                      setData('rooms', newRooms);
                    }}
                  />
                </Grid.Col>
                <Grid.Col span={1}>
                  <ActionIcon
                    color="red"
                    onClick={() => {
                      const newRooms = rooms.filter((_: any, i: number) => i !== index);
                      setRooms(newRooms);
                      setData('rooms', newRooms);
                    }}
                  >
                    <i className="pi pi-trash" />
                  </ActionIcon>
                </Grid.Col>
              </Grid>
            ))}

            <Button loading={processing} type="submit" mt={'md'} fullWidth>
              Update Conference
            </Button>
          </form>
        </Grid.Col>
      </Grid>
    </div>
  );
}

ConferenceCreate.layout = (page: React.ReactNode) => (
  <MainLayout title="Data Conference">{page}</MainLayout>
);

export default ConferenceCreate;
