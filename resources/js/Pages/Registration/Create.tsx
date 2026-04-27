import React, { useState } from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import {
  Container,
  Title,
  TextInput,
  Select,
  FileInput,
  Button,
  Stack,
  Group,
  Text,
  Card,
  Badge,
  Divider,
  Alert
} from '@mantine/core';
import { IconUpload } from '@tabler/icons-react';
import { Conference } from '../../types';
import { formatCurrency } from '../../utils';
import AuthLayout from '../../Layout/AuthLayout';
import dayjs from 'dayjs';
import { COUNTRIES, PRESENTATION_TYPES } from '../../Constants';

interface RegistrationCreateProps {
  conference: Conference;
}

const DEFAULT_PRESENTATION_TYPE = 'online_author';

export default function RegistrationCreate({ conference }: RegistrationCreateProps) {
  const { auth } = usePage().props as any;

  console.log('auth', auth);
  const defaultCountry = auth?.user?.membership?.country || '';
  const [selectedCountry, setSelectedCountry] = useState<string>(defaultCountry);

  const [selectedType, setSelectedType] = useState<string>(DEFAULT_PRESENTATION_TYPE);
  const isJOIV = conference.name === 'JOIV : International Journal on Informatics Visualization';

  const { data, setData, post, processing, errors } = useForm({
    first_name: auth?.user?.membership?.first_name || '',
    last_name: auth?.user?.membership?.last_name || '',
    paper_title: '',
    institution: auth?.user?.membership?.institution || '',
    email: auth?.user?.membership?.email || '',
    phone_number: auth?.user?.membership?.phone_number || '',
    country: defaultCountry,
    presentation_type: DEFAULT_PRESENTATION_TYPE,
    full_paper: null as File | null,
  });

  const [isMember] = useState<boolean>(
    auth?.user?.membership?.status === 'active'
  );

  const calculateFee = (country: string, type: string): { fee: number, discountAmount: number, totalFee: number, discountPercentage: number } => {
    if (!country || !type) return { fee: 0, discountAmount: 0, totalFee: 0, discountPercentage: 0 };

    const isIndonesia = country === 'ID';
    let fee = 0;
    let discountAmount = 0;
    let totalFee = 0;
    let discountPercentage = 0;

    switch (type) {
      case 'online_author':
        fee = isIndonesia ? conference.online_fee : conference.online_fee_usd;
        break;
      case 'onsite':
        fee = isIndonesia ? conference.onsite_fee : conference.onsite_fee_usd;
        break;
      case 'participant_only':
        fee = isIndonesia ? conference.participant_fee : conference.participant_fee_usd;
        break;
    }

    console.log('isMember', isMember)
    if (fee > 0 && isMember) {
      discountPercentage = conference.member_discount_percentage || 0;
      if (discountPercentage > 0) {
        discountAmount = fee * (discountPercentage / 100);
        totalFee = fee - discountAmount;
      } else {
        totalFee = fee;
      }
    } else {
      totalFee = fee;
    }

    return { fee, discountAmount, totalFee, discountPercentage };
  };


  const { fee, discountPercentage, totalFee } = calculateFee(selectedCountry, selectedType);
  const currency = selectedCountry === 'ID' ? 'IDR' : 'USD';

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post(`/registration/${conference.public_id}`, {
      forceFormData: true,
    });
  };

  return (
    <>
      <Head title={`Registration - ${conference.name}`} />

      <Container size="md" py="xl">
        <Stack gap="lg">
          <div>
            {conference.name !== 'JOIV : International Journal on Informatics Visualization' && (
              <Title order={2} ta="center" mb="xs">
                Conference Registration
              </Title>
            )}

            <Text ta="center" c="dimmed" size="lg">
              {conference.name}
            </Text>
            {conference.name !== 'JOIV : International Journal on Informatics Visualization' && (
              <Group justify="center" mt="sm">
                <Badge variant="light" size="lg">
                  {dayjs(conference.date).format('MMMM D, YYYY')} • {conference.city}
                </Badge>
              </Group>
            )}
          </div>

          <Divider />

          <form onSubmit={handleSubmit}>
            <Stack gap="md">
              <Title order={4}>Personal Information</Title>

              <Group grow>
                <TextInput
                  label="First Name"
                  placeholder="Enter your first name"
                  value={data.first_name}
                  onChange={(e) => setData('first_name', e.currentTarget.value)}
                  error={errors.first_name}
                  required
                />
                <TextInput
                  label="Last Name"
                  placeholder="Enter your last name"
                  value={data.last_name}
                  onChange={(e) => setData('last_name', e.currentTarget.value)}
                  error={errors.last_name}
                  required
                />
              </Group>

              <TextInput
                label="Email Address"
                placeholder="Enter your email"
                type="email"
                value={data.email}
                onChange={(e) => setData('email', e.currentTarget.value)}
                error={errors.email}
                required
              />

              <Group grow>
                <TextInput
                  label="Phone Number"
                  placeholder="Enter your phone number"
                  value={data.phone_number}
                  onChange={(e) => {
                    // Only allow numbers
                    const value = e.currentTarget.value.replace(/\D/g, '');
                    setData('phone_number', value);
                  }}
                  onKeyPress={(e) => {
                    // Prevent non-numeric characters
                    if (!/\d/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete' && e.key !== 'Tab') {
                      e.preventDefault();
                    }
                  }}
                  error={errors.phone_number}
                  description={"Phone number should include country code, e.g., 6281234567890 (numbers only)"}
                  required
                />
                <TextInput
                  label="Institution"
                  placeholder="Enter your institution"
                  value={data.institution}
                  onChange={(e) => setData('institution', e.currentTarget.value)}
                  error={errors.institution}
                  required
                />
              </Group>

              <Group grow>
                <Select
                  label="Country"
                  placeholder="Select your country"
                  data={COUNTRIES}
                  value={data.country}
                  onChange={(value) => {
                    setData('country', value || '');
                    setSelectedCountry(value || '');
                  }}
                  error={errors.country}
                  required
                />
                {!isJOIV && (
                  <Select
                    label="Presentation Type"
                    placeholder="Select presentation type"
                    data={PRESENTATION_TYPES}
                    value={data.presentation_type}
                    onChange={(value) => {
                      setData('presentation_type', value || '');
                      setSelectedType(value || '');
                    }}
                    error={errors.presentation_type}
                    required
                  />
                )}
              </Group>

              <TextInput
                label="Paper Title"
                placeholder="Enter your paper title"
                value={data.paper_title}
                onChange={(e) => setData('paper_title', e.currentTarget.value)}
                error={errors.paper_title}
                required
              />

              <FileInput
                label="Full Paper"
                placeholder="Upload your full paper"
                accept=".pdf,.doc,.docx"
                leftSection={<IconUpload size={14} />}
                value={data.full_paper}
                onChange={(file) => setData('full_paper', file)}
                error={errors.full_paper}
                description="Accepted formats: PDF, DOC, DOCX (Max: 50MB)"
                required
              />
              {(discountPercentage > 0 && isMember) && (
                <Alert color='green'>
                  <Text size="sm">Your <b>{discountPercentage}% discount</b> is applied</Text>
                </Alert>
              )}
              <Card withBorder padding="md" bg="blue.0">
                <Group justify="space-between">
                  <Text fw={500}>Registration Fee:</Text>
                  <Stack gap={0} align="flex-end">
                    {isMember && (
                      <Text fw={700} size="sm" c="orange" td="line-through">
                        {formatCurrency(fee, currency.toLowerCase() as 'idr' | 'usd')}
                      </Text>
                    )}
                    <Text fw={700} size="lg" c="blue">
                      {formatCurrency(totalFee, currency.toLowerCase() as 'idr' | 'usd')}
                    </Text>
                    {isMember && (
                      <Text size="xs" c="green" fw={500}>
                        Member discount applied
                      </Text>
                    )}
                  </Stack>
                </Group>
              </Card>

              <Button
                type="submit"
                size="lg"
                loading={processing}
                disabled={!totalFee}
                fullWidth
              >
                Continue to Payment
              </Button>
            </Stack>
          </form>
        </Stack>
      </Container>
    </>
  );
}

RegistrationCreate.layout = (page: React.ReactNode) => (
  <AuthLayout title="Conference Registration">{page}</AuthLayout>
);