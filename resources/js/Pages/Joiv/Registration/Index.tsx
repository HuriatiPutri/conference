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
import React, { useState } from 'react';
import { COUNTRIES } from '../../../Constants';
import AuthLayout from '../../../Layout/AuthLayout';
import { formatCurrency } from '../../../utils';
import VoucherValidation from '../../../Components/VoucherValidation';

export default function JoivRegistrationIndex() {
  const { auth } = usePage().props as any;

  const [discountVoucher, setDiscountVoucher] = useState<{ type: string; value: number; description?: string } | null>(null);
  const { registrationFeeIDR, registrationFeeUSD } = usePage().props as unknown as {
    registrationFeeIDR: string | number;
    registrationFeeUSD: string | number;
  };
  const defaultCountry = auth?.user?.membership?.country || '';
  const { data, setData, post, processing, errors } = useForm({
    first_name: auth?.user?.membership?.first_name || '',
    last_name: auth?.user?.membership?.last_name || '',
    email_address: auth?.user?.membership?.email || '',
    phone_number: auth?.user?.membership?.phone_number || '',
    institution: auth?.user?.membership?.institution || '',
    country: defaultCountry,
    paper_id: '',
    paper_title: '',
    voucher_code: '',
    full_paper: null as File | null,
  });

  const [isMember] = useState<boolean>(
    auth?.user?.membership?.status === 'active'
  );

  const membership = auth?.user?.membership;
  const packageName = membership?.package?.name || '-';
  const packageBenefits = membership?.package?.package_benefits || [];


  const calculateFee = (country: string): { fee: number, discountAmount: number, totalFee: number, discountPercentage: number } => {
    if (!country) return { fee: 0, discountAmount: 0, totalFee: 0, discountPercentage: 0 };

    const isIndonesia = country === 'ID';
    let fee = isIndonesia ? Number(registrationFeeIDR) : Number(registrationFeeUSD);
    let discountAmount = 0;
    let totalFee = 0;
    let discountPercentage = 0;


    if (isMember) {
      discountPercentage = packageBenefits.reduce((maxDiscount: number, benefit: any) => {
        console.log('Evaluating benefit for discount:', benefit);
        const isDiscount = benefit.membership_benefit?.benefit_type === 'discount' || benefit.value_type === 'percentage';
        const value = benefit.value_type === 'percentage' ? benefit.value : 0;
        return isDiscount ? Math.max(maxDiscount, value) : maxDiscount;
      }, 0);

      if (discountPercentage > 0) {
        discountAmount = fee * (discountPercentage / 100);
        totalFee = fee - discountAmount;
      } else {
        totalFee = fee;
      }
    } else {
      discountPercentage = discountVoucher?.type === 'percent' ? Number(discountVoucher.value) : 0;
      if (discountPercentage > 0) {
        discountAmount = fee * (discountPercentage / 100);
        totalFee = fee - discountAmount;
      } else {
        totalFee = fee;
      }
    }

    // Ensure total fee is not negative
    if (totalFee < 0) {
      totalFee = fee;
    }

    return { fee, discountAmount, totalFee, discountPercentage };
  };

  const { fee, discountPercentage, totalFee } = calculateFee(data.country);
  const currency = data.country === 'ID' ? 'IDR' : 'USD';

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
        <Stack gap="lg">
          <div>
            <Title order={2} ta="center" mb="xs">
              JOIV Article Registration
            </Title>
            <Text ta="center" c="dimmed" size="lg">
              International Journal on Informatics Visualization
            </Text>
          </div>

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

              {!isMember && (
                <>
                  <Divider mt="md" label="Claim Voucher to get Discount" />
                  <Text size="sm" c="dimmed">
                    If you have a voucher code, enter it below to check for discounts on your registration fee.
                  </Text>
                  <VoucherValidation
                    value={data.voucher_code}
                    onChange={(value) => setData('voucher_code', value)}
                    onValidationChange={(isValid, discountData) => {
                      console.log('Voucher validation result:', { isValid, discountData });
                      if (isValid) {
                        setDiscountVoucher(discountData as { type: string; value: number; description?: string } | null);
                      } else {
                        setDiscountVoucher(null);
                      }
                    }}
                    transactionType="joiv_article"
                    email={data.email_address}
                  />
                </>
              )}

              <Divider mt="md" />

              {(discountPercentage > 0 && isMember) && (
                <Alert color='green'>
                  <Text size="sm">Your <b>{discountPercentage}% discount</b> is applied</Text>
                </Alert>
              )}
              <Card withBorder padding="md" bg="blue.0">
                <Group justify="space-between">
                  <Text fw={500}>Registration Fee:</Text>
                  <Stack gap={0} align="flex-end">
                    {discountPercentage > 0 && (
                      <Text fw={700} size="sm" c="orange" td="line-through">
                        {formatCurrency(fee, currency.toLowerCase() as 'idr' | 'usd')}
                      </Text>
                    )}
                    <Text fw={700} size="lg" c="blue">
                      {formatCurrency(totalFee, currency.toLowerCase() as 'idr' | 'usd')}
                    </Text>
                    {discountPercentage > 0 && (
                      <Text size="xs" c="green" fw={500}>
                        You saved {formatCurrency(fee - totalFee, currency.toLowerCase() as 'idr' | 'usd')} with your {isMember ? 'membership benefits' : 'voucher'}
                      </Text>
                    )}
                  </Stack>
                </Group>
              </Card>

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
      </Container>
    </>
  );
}

JoivRegistrationIndex.layout = (page: React.ReactNode) => <AuthLayout>{page}</AuthLayout>;
