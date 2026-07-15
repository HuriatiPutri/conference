import { Link, usePage } from '@inertiajs/react';
import { Badge, Box, Button, Card, Container, Group, Stack, Text, ThemeIcon, Title } from '@mantine/core';
import { IconAlertCircle, IconArrowLeft, IconCrown, IconIdBadge2, IconDownload } from '@tabler/icons-react';
import dayjs from 'dayjs';
import React, { useRef, useState } from 'react';
import QRCode from 'react-qr-code';
import { route } from 'ziggy-js';
import MainLayout from '../../../Layout/MainLayout';
import MembershipCard from '../../../Components/MembershipCard';

import { Membership } from '../../../types';

interface MembershipPackage {
  name: string;
  packageBenefits?: any[];
  package_benefits?: any[];
}

interface MembershipCardProps {
  membership: Membership;
  user: {
    name: string;
    email: string;
  };
}

export default function MembershipCardPage() {
  const { membership, user } = usePage().props as unknown as MembershipCardProps;
  const cardRef = useRef<HTMLDivElement>(null);
  const [downloading, setDownloading] = useState(false);

  const isExpired = membership
    ? dayjs().isAfter(dayjs(membership.end_date).endOf('day'))
    : false;

  const isActive = membership?.status === 'active' && !isExpired;
  const statusUrl = membership ? route('membership.status', membership.public_id) : '';

  const handleDownload = () => {
    if (!cardRef.current) return;
    setDownloading(true);

    import('html-to-image')
      .then(({ toPng }) => {
        toPng(cardRef.current!, { cacheBust: true })
          .then((dataUrl) => {
            const link = document.createElement('a');
            link.download = `membership-card-${membership?.public_id || 'card'}.png`;
            link.href = dataUrl;
            link.click();
            setDownloading(false);
          })
          .catch((err) => {
            console.error('Failed to download card:', err);
            setDownloading(false);
          });
      })
      .catch((err) => {
        console.error('Failed to load html-to-image:', err);
        setDownloading(false);
      });
  };

  const renderEmptyState = () => (
    <Card radius="xl" p="xl" withBorder>
      <Stack gap="sm" align="center" py="md">
        <ThemeIcon color="gray" variant="light" size="xl" radius="xl">
          <IconCrown size={24} />
        </ThemeIcon>
        <Title order={3}>No Membership Found</Title>
        <Text c="dimmed" ta="center">
          {user?.name || 'User'}, you do not have an active membership yet.
        </Text>
        <Button component={Link} href="/register-membership" radius="xl" variant="gradient" gradient={{ from: 'indigo', to: 'cyan' }}>
          Register Membership
        </Button>
      </Stack>
    </Card>
  );


  return (
    <Container size="md">
      <Stack gap="lg">
        <Group justify="space-between" align="center">
          <div>
            <Title order={2}>Membership Card</Title>
            <Text c="dimmed">Digital card for your current membership.</Text>
          </div>
          <Group gap="sm">
            {membership && (
              <Button
                onClick={handleDownload}
                loading={downloading}
                variant="filled"
                color="blue"
                leftSection={<IconDownload size={16} />}
              >
                Download
              </Button>
            )}
            <Button component={Link} href="/dashboard" variant="subtle" leftSection={<IconArrowLeft size={16} />}>
              Back to Dashboard
            </Button>
          </Group>
        </Group>
        {membership ?
          <MembershipCard
            ref={cardRef}
            isActive={isActive}
            membership={membership}
            packageBenefits={membership.package?.package_benefits}
            statusUrl={statusUrl} />
          : renderEmptyState()
        }
      </Stack>
    </Container>
  );
}

MembershipCardPage.layout = (page: React.ReactNode) => <MainLayout title="Membership Card">{page}</MainLayout>;
