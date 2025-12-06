import { Head, useForm, usePage } from '@inertiajs/react';
import {
  Alert,
  Button,
  Card,
  Container,
  Divider,
  FileInput,
  Group,
  Select,
  Stack,
  Text,
  TextInput,
  Title
} from '@mantine/core';
import { IconInfoCircle, IconUpload } from '@tabler/icons-react';
import React from 'react';
import { COUNTRIES } from '../../../Constants';
import AuthLayout from '../../../Layout/AuthLayout';
import { formatCurrency } from '../../../utils';

export default function JoivRegistrationIndex() {
  const { registrationFeeIDR, registrationFeeUSD } = usePage().props as unknown as {
    registrationFeeIDR: string | number;
    registrationFeeUSD: string | number;
  };
  const { data, setData, post, processing, errors } = useForm({
    first_name: '',
    last_name: '',
    email_address: '',
    phone_number: '',
    institution: '',
    country: '',
    paper_id: '',
    paper_title: '',
    full_paper: null as File | null,
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post('/joiv/registration', {
      forceFormData: true,
    });
  };

  return (
    <>
      <Head title="JOIV Registration" />
      <Container size="md" py="xl">
        <Card shadow="md" padding="xl" radius="md">
          <Stack gap="lg">
            <div>
              <Title order={2} ta="center" mb="xs">
                JOIV Article Registration
              </Title>
              <Text ta="center" c="dimmed" size="lg">
                International Journal on Informatics Visualization
              </Text>
            </div>

            <Alert icon={<IconInfoCircle size="1rem" />} title="Registration Fee" color="blue">
              <Text>
                International Fee: {formatCurrency(Number(registrationFeeUSD), 'usd')} USD
              </Text>
              <Text>
                Indonesian Fee: {formatCurrency(Number(registrationFeeIDR), 'idr')} IDR
              </Text>
            </Alert>

            <Divider />

            <form onSubmit={handleSubmit}>
              <Stack gap="md">
                <Title order={4}>Author Information</Title>

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
                  value={data.email_address}
                  onChange={(e) => setData('email_address', e.currentTarget.value)}
                  error={errors.email_address}
                  required
                />

                <Group grow>
                  <TextInput
                    label="Phone Number"
                    placeholder="Enter your phone number"
                    value={data.phone_number}
                    onChange={(e) => {
                      const value = e.currentTarget.value.replace(/\D/g, '');
                      setData('phone_number', value);
                    }}
                    error={errors.phone_number}
                    required
                  />
                  <Select
                    label="Country"
                    placeholder="Select your country"
                    data={COUNTRIES}
                    value={data.country}
                    onChange={(value) => setData('country', value || '')}
                    error={errors.country}
                    searchable
                    required
                  />
                </Group>

                <TextInput
                  label="Institution"
                  placeholder="Enter your institution/university"
                  value={data.institution}
                  onChange={(e) => setData('institution', e.currentTarget.value)}
                  error={errors.institution}
                  required
                />

                <Divider />

                <Title order={4}>Paper Information</Title>

                <TextInput
                  label="Paper ID"
                  placeholder="Enter paper ID"
                  value={data.paper_id}
                  onChange={(e) => setData('paper_id', e.currentTarget.value)}
                  error={errors.paper_id}
                  required
                />

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
                  accept="application/pdf,.doc,.docx"
                  leftSection={<IconUpload size={14} />}
                  value={data.full_paper}
                  onChange={(file) => setData('full_paper', file)}
                  error={errors.full_paper}
                  description="Accepted formats: PDF, DOC, DOCX (Max: 50MB)"
                  required
                />

                <Divider mt="md" />

                <Group justify="flex-end" mt="lg">
                  <Button
                    type="submit"
                    size="lg"
                    fullWidth
                    loading={processing}
                    disabled={processing}
                  >
                    Continue to Payment
                  </Button>
                </Group>
              </Stack>
            </form>
          </Stack>
        </Card>
      </Container>
    </>
  );
}

JoivRegistrationIndex.layout = (page: React.ReactNode) => <AuthLayout>{page}</AuthLayout>;
