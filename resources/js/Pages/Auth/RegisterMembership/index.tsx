import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import {
  Container,
  Title,
  TextInput,
  Select,
  Button,
  Stack,
  Group,
  Text,
  Card,
  Divider,
  Radio,
  Paper
} from '@mantine/core';
import AuthLayout from '../../../Layout/AuthLayout';
import { COUNTRIES } from '../../../Constants';
import { formatCurrency } from '../../../utils';

interface Package {
  id: number;
  name: string;
  price: number;
  duration: number;
}

interface RegisterMembershipProps {
  packages: Package[];
}

export default function RegisterMembership({ packages }: RegisterMembershipProps) {
  const { data, setData, post, processing, errors } = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone_number: '',
    institution: '',
    country: '',
    package_id: '' as string | number,
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post('/register-membership');
  };

  const selectedPackage = packages.find(p => p.id.toString() === data.package_id.toString());
  const isIndonesia = data.country === 'ID';
  const currency = isIndonesia ? 'idr' : 'usd';

  return (
    <>
      <Head title="Membership Registration" />

      <Container size="md" py="xl">
        <Stack gap="lg">
          <div>
            <Title order={2} ta="center" mb="xs">
              Membership Registration
            </Title>
            <Text ta="center" c="dimmed" size="lg">
              Join our community and get access to exclusive benefits
            </Text>
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
                    const value = e.currentTarget.value.replace(/\D/g, '');
                    setData('phone_number', value);
                  }}
                  error={errors.phone_number}
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

              <Select
                label="Country"
                placeholder="Select your country"
                data={COUNTRIES}
                value={data.country}
                onChange={(value) => setData('country', value || '')}
                error={errors.country}
                required
                searchable
              />

              <Title order={4} mt="md">Select Membership Package</Title>

              <Stack gap="sm">
                {packages.map((pkg) => (
                  <Paper
                    key={pkg.id}
                    withBorder
                    p="md"
                    style={{
                      cursor: 'pointer',
                      borderColor: data.package_id.toString() === pkg.id.toString() ? 'var(--mantine-color-blue-5)' : undefined,
                      backgroundColor: data.package_id.toString() === pkg.id.toString() ? 'var(--mantine-color-blue-0)' : undefined
                    }}
                    onClick={() => setData('package_id', pkg.id)}
                  >
                    <Group justify="space-between">
                      <Group>
                        <Radio
                          value={pkg.id.toString()}
                          checked={data.package_id.toString() === pkg.id.toString()}
                          onChange={() => { }}
                        />
                        <div>
                          <Text fw={500}>{pkg.name}</Text>
                          <Text size="sm" c="dimmed">Duration: {pkg.duration} days</Text>
                        </div>
                      </Group>
                      <Text fw={700} c="blue">
                        {/* Note: we are currently simplifying the currency conversion visual for packages. */}
                        {isIndonesia && pkg.price < 1000 ? '$' + pkg.price : formatCurrency(pkg.price, isIndonesia ? 'idr' : 'usd')}
                      </Text>
                    </Group>
                  </Paper>
                ))}
                {errors.package_id && (
                  <Text c="red" size="sm">{errors.package_id}</Text>
                )}
              </Stack>

              {selectedPackage && (
                <Card withBorder padding="md" bg="blue.0" mt="md">
                  <Group justify="space-between">
                    <Text fw={500}>Total Fee:</Text>
                    <Text fw={700} size="lg" c="blue">
                      {isIndonesia && selectedPackage.price < 1000 ? '$' + selectedPackage.price : formatCurrency(selectedPackage.price, isIndonesia ? 'idr' : 'usd')}
                    </Text>
                  </Group>
                </Card>
              )}

              <Button
                type="submit"
                size="lg"
                loading={processing}
                disabled={!data.package_id}
                fullWidth
                mt="md"
              >
                Submit To Payment
              </Button>
            </Stack>
          </form>
        </Stack>
      </Container>
    </>
  );
}

RegisterMembership.layout = (page: React.ReactNode) => (
  <AuthLayout title="Membership Registration">{page}</AuthLayout>
);
