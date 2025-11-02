import { useForm, usePage, router } from '@inertiajs/react';
import { Button, Grid, Image, Stack, Text, NumberInput, ColorInput } from '@mantine/core';
import { notifications } from '@mantine/notifications';
import React, { useState, useRef } from 'react';
import { route } from 'ziggy-js';
import MainLayout from '../../../Layout/MainLayout';
import { Conference } from '../../../types';
import styles from './styles.module.css';

interface PositionElement {
  x: number;
  y: number;
  size: number;
  width: number;
  color: string;
  align: 'left' | 'center' | 'right';
  text: string;
}

interface Positions {
  name: PositionElement;
  paper_title: PositionElement;
}

export default function ConferenceSetting() {
  const { conference } = usePage<{ conference: Conference }>().props;
  const canvasRef = useRef<HTMLCanvasElement>(null);
  const [imageLoaded, setImageLoaded] = useState(false);

  console.log(conference);
  // Default positions dari database atau default
  const getInitialPositions = () => {
    try {
      if (conference.data.certificate_template_position) {
        const parsed = JSON.parse(conference.data.certificate_template_position);
        return JSON.parse(parsed.positions);
      }
    } catch (e) {
      console.error('Error parsing positions:', e);
    }

    // Default positions
    return {
      name: { x: 419, y: 297.5, size: 20, width: 400, color: '#000000', align: 'center', text: 'NAMA PESERTA' },
      paper_title: { x: 419, y: 325.5, size: 14, width: 700, color: '#000000', align: 'center', text: 'PAPER_TITLE' }
    };
  };

  const [positions, setPositions] = useState<Positions>(getInitialPositions());

  const { data, setData, processing, errors, post } = useForm<{
    certificate_template_path: File | null;
    certificate_template_position: string | null;
  }>({
    certificate_template_path: null,
    certificate_template_position: null,
  });

  function handleFileSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    console.log('submitting file', data);
    if (!data.certificate_template_path) return;

    post(route('conferences.uploadCertificate', conference.data.id), {
      forceFormData: true,
      onSuccess: () => {
        notifications.show({ title: 'Success', message: 'Template sertifikat berhasil diunggah!', color: 'green' });
        setData('certificate_template_path', null);
      },
      onError: (e) => {
        console.log('error', e);
      },
    });
  }

  function handlePositionSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();

    const positionData = JSON.stringify({
      positions: JSON.stringify(positions)
    });

    router.post(route('conferences.uploadCertificate', conference.data.id), {
      certificate_template_position: positionData
    }, {
      onSuccess: () => {
        notifications.show({ title: 'Success', message: 'Posisi template berhasil disimpan!', color: 'green' });
      },
      onError: (e) => {
        console.log('error', e);
      },
    });
  }

  const templateUrl = React.useMemo(() => {
    if (!conference.data.certificate_template_path) return null;
    if (/^https?:\/\//i.test(conference.data.certificate_template_path)) return conference.data.certificate_template_path;
    const filename = conference.data.certificate_template_path.replace(/^conference_certificate\//, '');
    return `/storage/conference_certificate/${filename}`;
  }, [conference.data.certificate_template_path]);

  const updatePosition = (element: 'name' | 'paper_title', field: string, value: string | number) => {
    setPositions((prev: Positions) => ({
      ...prev,
      [element]: {
        ...prev[element],
        [field]: value
      }
    }));
  };

  return (
    <div className={styles.card}>
      <Text c="#101010" fw={700}>
        {conference.data.name}
      </Text>
      <Grid mt="md">
        <Grid.Col span={{ base: 12, sm: 2 }}>
          <Text c="#101010" fw={700} fz="lg">
            Pengaturan Sertifikat
          </Text>
          <Text fz="sm" c="#606060">
            Unggah template sertifikat.
          </Text>
        </Grid.Col>
        <Grid.Col span={{ base: 12, sm: 10 }}>
          {/* Upload Template Form */}
          <form onSubmit={handleFileSubmit} encType="multipart/form-data">
            <Stack gap="md" mb="md" mt="md">
              <div>
                <label style={{ fontSize: 14, fontWeight: 500, display: 'block', marginBottom: 4 }}>
                  Template Sertifikat (PNG/JPG)
                </label>
                <input
                  name="certificate_template_path"
                  type="file"
                  accept="image/png,image/jpeg"
                  onChange={(e) => setData('certificate_template_path', e.target.files?.[0] || null)}
                />
                {errors.certificate_template_path && (
                  <Text fz="xs" c="red">
                    {errors.certificate_template_path}
                  </Text>
                )}
              </div>
              <Button type="submit" loading={processing} disabled={!data.certificate_template_path}>
                Upload Template Sertifikat
              </Button>
            </Stack>
          </form>

          {/* Template Preview & Position Editor */}
          {templateUrl && (
            <div style={{ marginTop: 32 }}>
              <Text fw={500} mb="md">Preview Template & Pengaturan Posisi</Text>

              <Grid>
                <Grid.Col span={{ base: 12, md: 8 }}>
                  <div style={{ position: 'relative', border: '1px solid #ddd', borderRadius: 8 }}>
                    <Image
                      src={templateUrl}
                      alt="Certificate Template"
                      style={{ width: '100%', height: 'auto' }}
                      onLoad={() => setImageLoaded(true)}
                    />

                    {/* Overlay untuk preview posisi */}
                    {imageLoaded && (
                      <div style={{ position: 'absolute', top: 0, left: 0, right: 0, bottom: 0 }}>
                        <div
                          style={{
                            position: 'absolute',
                            left: `${(positions.name.x / 838) * 100}%`,
                            top: `${(positions.name.y / 595) * 100}%`,
                            fontSize: `${positions.name.size}px`,
                            color: positions.name.color,
                            textAlign: positions.name.align,
                            width: `${(positions.name.width / 838) * 100}%`,
                            transform: 'translate(-50%, -50%)',
                            pointerEvents: 'none',
                            fontWeight: 'bold'
                          }}
                        >
                          {positions.name.text}
                        </div>

                        <div
                          style={{
                            position: 'absolute',
                            left: `${(positions.paper_title.x / 838) * 100}%`,
                            top: `${(positions.paper_title.y / 595) * 100}%`,
                            fontSize: `${positions.paper_title.size}px`,
                            color: positions.paper_title.color,
                            textAlign: positions.paper_title.align,
                            width: `${(positions.paper_title.width / 838) * 100}%`,
                            transform: 'translate(-50%, -50%)',
                            pointerEvents: 'none'
                          }}
                        >
                          {positions.paper_title.text}
                        </div>
                      </div>
                    )}
                  </div>
                </Grid.Col>

                <Grid.Col span={{ base: 12, md: 4 }}>
                  <form onSubmit={handlePositionSubmit}>
                    <Stack gap="sm">
                      <Text fw={600}>Pengaturan Nama Peserta</Text>
                      <NumberInput
                        label="X Position"
                        value={positions.name.x}
                        onChange={(val) => updatePosition('name', 'x', val || 0)}
                      />
                      <NumberInput
                        label="Y Position"
                        value={positions.name.y}
                        onChange={(val) => updatePosition('name', 'y', val || 0)}
                      />
                      <NumberInput
                        label="Font Size"
                        value={positions.name.size}
                        onChange={(val) => updatePosition('name', 'size', val || 20)}
                      />
                      <ColorInput
                        label="Warna"
                        value={positions.name.color}
                        onChange={(val) => updatePosition('name', 'color', val)}
                      />

                      <Text fw={600} mt="md">Pengaturan Judul Paper</Text>
                      <NumberInput
                        label="X Position"
                        value={positions.paper_title.x}
                        onChange={(val) => updatePosition('paper_title', 'x', val || 0)}
                      />
                      <NumberInput
                        label="Y Position"
                        value={positions.paper_title.y}
                        onChange={(val) => updatePosition('paper_title', 'y', val || 0)}
                      />
                      <NumberInput
                        label="Font Size"
                        value={positions.paper_title.size}
                        onChange={(val) => updatePosition('paper_title', 'size', val || 14)}
                      />
                      <ColorInput
                        label="Warna"
                        value={positions.paper_title.color}
                        onChange={(val) => updatePosition('paper_title', 'color', val)}
                      />

                      <Button type="submit" mt="md" fullWidth>
                        Simpan Pengaturan Posisi
                      </Button>
                    </Stack>
                  </form>
                </Grid.Col>
              </Grid>
            </div>
          )}
        </Grid.Col>
      </Grid>
    </div>
  );
}

ConferenceSetting.layout = (page: React.ReactNode) => (
  <MainLayout title="Pengaturan Konferensi">{page}</MainLayout>
);
