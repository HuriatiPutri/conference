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

  const [discountVoucher, setDiscountVoucher] = useState<{ type: string; value: number; value_usd?: number; description?: string } | null>(null);
  const { registrationFeeIDR, registrationFeeUSD } = usePage().props as unknown as {
    registrationFeeIDR: string | number;
    registrationFeeUSD: string | number;
  };
  const defaultCountry = auth?.user?.membership?.country || '';
  const { data, setData, post, processing, errors, setError, clearErrors } = useForm({
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

  const [memberInfo, setMemberInfo] = useState<{
    is_member: boolean;
    package_name?: string;
    discount_benefits?: Array<{
      benefit_name: string;
      benefit_type: string;
      value_type: string;
      value: number;
    }>;
  } | null>(
    auth?.user?.membership?.status === 'active'
      ? {
          is_member: true,
          package_name: auth.user.membership.package?.name,
          discount_benefits: auth.user.membership.package?.package_benefits
            ?.filter((b: any) => b.membership_benefit?.benefit_type === 'discount' || b.value_type === 'percentage')
            ?.map((b: any) => ({
              benefit_name: b.membership_benefit?.name || 'Discount',
              benefit_type: b.membership_benefit?.benefit_type || 'discount',
              value_type: b.value_type,
              value: Number(b.value),
            })) || [],
        }
      : null
  );

  // Dynamic membership check by email
  React.useEffect(() => {
    if (!data.email_address || !data.email_address.includes('@')) {
      if (!auth?.user?.membership || auth.user.membership.status !== 'active') {
        setMemberInfo(null);
      }
      return;
    }

    if (auth?.user?.membership?.status === 'active' && data.email_address === auth.user.membership.email) {
      return;
    }

    const timer = setTimeout(async () => {
      try {
        const res = await fetch(`/api/membership/check?email=${encodeURIComponent(data.email_address)}`);
        const result = await res.json();
        if (result.is_member) {
          setMemberInfo({
            is_member: true,
            package_name: result.membership?.package_name,
            discount_benefits: result.membership?.discount_benefits || [],
          });
        } else {
          setMemberInfo(null);
        }
      } catch (err) {
        console.error('Error checking membership:', err);
      }
    }, 500);

    return () => clearTimeout(timer);
  }, [data.email_address]);

  const calculateFee = (country: string) => {
    if (!country) {
      return {
        baseFee: 0,
        memberDiscountAmount: 0,
        memberDiscountPercent: 0,
        voucherDiscountAmount: 0,
        totalDiscount: 0,
        totalFee: 0,
        isIndonesia: false,
      };
    }

    const isIndonesia = country === 'ID';
    const baseFee = isIndonesia ? Number(registrationFeeIDR) : Number(registrationFeeUSD);

    // 1. Calculate Member Discount
    let memberDiscountAmount = 0;
    let memberDiscountPercent = 0;

    if (memberInfo?.is_member && memberInfo.discount_benefits && memberInfo.discount_benefits.length > 0) {
      for (const b of memberInfo.discount_benefits) {
        if (b.benefit_type === 'free_registration') {
          memberDiscountAmount = baseFee;
          memberDiscountPercent = 100;
          break;
        } else if (b.benefit_type === 'discount') {
          if (b.value_type === 'percentage') {
            const discount = baseFee * (b.value / 100);
            if (discount > memberDiscountAmount) {
              memberDiscountAmount = discount;
              memberDiscountPercent = b.value;
            }
          } else if (b.value_type === 'fixed') {
            const fixedDiscount = b.value;
            if (fixedDiscount > memberDiscountAmount) {
              memberDiscountAmount = Math.min(baseFee, fixedDiscount);
            }
          }
        }
      }
    }

    const feeAfterMember = Math.max(0, baseFee - memberDiscountAmount);

    // 2. Calculate Voucher Discount (applied on fee after member discount)
    let voucherDiscountAmount = 0;
    if (discountVoucher) {
      if (discountVoucher.type === 'percent') {
        voucherDiscountAmount = feeAfterMember * (Number(discountVoucher.value) / 100);
      } else if (discountVoucher.type === 'fixed') {
        const fixedVoucher = isIndonesia ? Number(discountVoucher.value || 0) : Number(discountVoucher.value_usd || discountVoucher.value || 0);
        voucherDiscountAmount = Math.min(feeAfterMember, fixedVoucher);
      }
    }

    const totalFee = Math.max(0, feeAfterMember - voucherDiscountAmount);
    const totalDiscount = memberDiscountAmount + voucherDiscountAmount;

    return {
      baseFee,
      memberDiscountAmount,
      memberDiscountPercent,
      voucherDiscountAmount,
      totalDiscount,
      totalFee,
      isIndonesia,
    };
  };

  const {
    baseFee,
    memberDiscountAmount,
    memberDiscountPercent,
    voucherDiscountAmount,
    totalDiscount,
    totalFee,
    isIndonesia,
  } = calculateFee(data.country);

  const currency = isIndonesia ? 'idr' : 'usd';

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    const MAX_FILE_SIZE = 50 * 1024 * 1024; // 50MB
    if (data.full_paper && data.full_paper.size > MAX_FILE_SIZE) {
      setError('full_paper', 'The full paper may not be greater than 50MB.');
      return;
    }
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
                onChange={(file) => {
                  const MAX_FILE_SIZE = 50 * 1024 * 1024; // 50MB
                  if (file && file.size > MAX_FILE_SIZE) {
                    setError('full_paper', 'The full paper may not be greater than 50MB.');
                    setData('full_paper', null);
                  } else {
                    clearErrors('full_paper');
                    setData('full_paper', file);
                  }
                }}
                error={errors.full_paper}
                description="Accepted formats: PDF, DOC, DOCX (Max: 50MB)"
                required
              />

              {memberInfo?.is_member && (
                <Alert color="blue" icon={<IconInfoCircle size={16} />}>
                  <Text size="sm">
                    <strong>Active Member detected:</strong> You are registered as a <strong>{memberInfo.package_name}</strong> member.
                    {memberDiscountPercent > 0 && ` Your ${memberDiscountPercent}% discount benefit is applied automatically.`}
                  </Text>
                </Alert>
              )}

              <Divider mt="md" label="Claim Voucher to get Additional Discount" />
              <Text size="sm" c="dimmed">
                If you have a voucher code, enter it below to check for additional discounts on your registration fee.
              </Text>
              <VoucherValidation
                value={data.voucher_code}
                onChange={(value) => setData('voucher_code', value)}
                onValidationChange={(isValid, discountData) => {
                  if (isValid) {
                    setDiscountVoucher(discountData as { type: string; value: number; value_usd?: number; description?: string } | null);
                  } else {
                    setDiscountVoucher(null);
                  }
                }}
                transactionType="joiv_article"
                email={data.email_address}
                isIndonesia={isIndonesia}
              />

              <Divider mt="md" />

              <Card withBorder padding="md" bg="blue.0">
                <Stack gap="xs">
                  <Group justify="space-between">
                    <Text fw={500}>Base Registration Fee:</Text>
                    <Text fw={totalDiscount > 0 ? 500 : 700} td={totalDiscount > 0 ? 'line-through' : undefined} c={totalDiscount > 0 ? 'dimmed' : undefined}>
                      {formatCurrency(baseFee, currency)}
                    </Text>
                  </Group>

                  {memberDiscountAmount > 0 && (
                    <Group justify="space-between">
                      <Text fw={500} c="indigo">
                        Member Discount ({memberInfo?.package_name || 'Member'}{memberDiscountPercent > 0 ? ` - ${memberDiscountPercent}%` : ''}):
                      </Text>
                      <Text fw={600} c="indigo">
                        -{formatCurrency(memberDiscountAmount, currency)}
                      </Text>
                    </Group>
                  )}

                  {voucherDiscountAmount > 0 && (
                    <Group justify="space-between">
                      <Text fw={500} c="teal">
                        Voucher Discount ({data.voucher_code}):
                      </Text>
                      <Text fw={600} c="teal">
                        -{formatCurrency(voucherDiscountAmount, currency)}
                      </Text>
                    </Group>
                  )}

                  <Divider my={4} />

                  <Group justify="space-between">
                    <div>
                      <Text fw={700} size="md">Total Fee to Pay:</Text>
                      {totalDiscount > 0 && (
                        <Text size="xs" c="green" fw={500}>
                          You saved {formatCurrency(totalDiscount, currency)}
                        </Text>
                      )}
                    </div>
                    <Text fw={700} size="xl" c="blue">
                      {formatCurrency(totalFee, currency)}
                    </Text>
                  </Group>
                </Stack>
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
