import React, { useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
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
  Divider
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
  const [selectedCountry, setSelectedCountry] = useState<string>('');
  
  const [selectedType, setSelectedType] = useState<string>(DEFAULT_PRESENTATION_TYPE);
  const isJOIV = conference.name === 'JOIV : International Journal on Informatics Visualization';
    
  const { data, setData, post, processing, errors } = useForm({
    first_name: '',
    last_name: '',
    paper_title: '',
    institution: '',
    email: '',
    phone_number: '',
    country: '',
	presentation_type: DEFAULT_PRESENTATION_TYPE,
    full_paper: null as File | null,
  });

  const calculateFee = (country: string, type: string): number => {
    if (!country || !type) return 0;

    const isIndonesia = country === 'ID';

    switch (type) {
      case 'online_author':
        return isIndonesia ? conference.online_fee : conference.online_fee_usd;
      case 'onsite':
        return isIndonesia ? conference.onsite_fee : conference.onsite_fee_usd;
      case 'participant_only':
        return isIndonesia ? conference.participant_fee : conference.participant_fee_usd;
      default:
        return 0;
    }
  };

  const currentFee = calculateFee(selectedCountry, selectedType);
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
        <Card shadow="md" padding="xl" radius="md">
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

                {currentFee > 0 && (
                  <Card withBorder padding="md" bg="blue.0">
                    <Group justify="space-between">
                      <Text fw={500}>Registration Fee:</Text>
                      <Text fw={700} size="lg" c="blue">
                        {formatCurrency(currentFee, currency.toLowerCase() as 'idr' | 'usd')}
                      </Text>
                    </Group>
                  </Card>
                )}

                <Button
                  type="submit"
                  size="lg"
                  loading={processing}
                  disabled={!currentFee}
                  fullWidth
                >
                  Continue to Payment
                </Button>
              </Stack>
            </form>
          </Stack>
        </Card>
      </Container>
    </>
  );
}

RegistrationCreate.layout = (page: React.ReactNode) => (
  <AuthLayout title="Conference Registration">{page}</AuthLayout>
);