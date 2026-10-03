import { Head, useForm, usePage } from '@inertiajs/react';
import {
  Alert,
  Badge,
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
import { IconUpload, IconInfoCircle } from '@tabler/icons-react';
import dayjs from 'dayjs';
import React, { useEffect, useState } from 'react';
import VoucherValidation from '../../Components/VoucherValidation';
import { COUNTRIES, PRESENTATION_TYPES } from '../../Constants';
import AuthLayout from '../../Layout/AuthLayout';
import { Conference } from '../../types';
import { formatCurrency } from '../../utils';

interface RegistrationCreateProps {
  conference: Conference;
}

const DEFAULT_PRESENTATION_TYPE = 'online_author';

export default function RegistrationCreate({ conference }: RegistrationCreateProps) {
  const { auth } = usePage().props as any;

  const defaultCountry = auth?.user?.membership?.country || '';
  const [selectedCountry, setSelectedCountry] = useState<string>(defaultCountry);
  const [discountVoucher, setDiscountVoucher] = useState<{ type: string; value: number; value_usd?: number; description?: string } | null>(null);

  const [selectedType, setSelectedType] = useState<string>(DEFAULT_PRESENTATION_TYPE);
  const isJOIV = conference.name === 'JOIV : International Journal on Informatics Visualization';

  const { data, setData, post, processing, errors, setError, clearErrors } = useForm({
    first_name: auth?.user?.membership?.first_name || '',
    last_name: auth?.user?.membership?.last_name || '',
    paper_title: '',
    institution: auth?.user?.membership?.institution || '',
    email: auth?.user?.membership?.email || '',
    phone_number: auth?.user?.membership?.phone_number || '',
    country: defaultCountry,
    presentation_type: DEFAULT_PRESENTATION_TYPE,
    voucher_code: '',
    full_paper: null as File | null,
  });

  const [memberInfo, setMemberInfo] = useState<{
    is_member: boolean;
    package_name?: string;
    discount_benefits?: Array<{
      benefit_id: number;
      benefit_name: string;
      benefit_type: string;
      value_type: string;
      value: number;
    }>;
  } | null>(null);

  useEffect(() => {
    const email = data.email?.trim();
    if (!email || !email.includes('@')) {
      if (auth?.user?.membership?.status === 'active') {
        setMemberInfo({
          is_member: true,
          package_name: auth.user.membership.package?.name,
          discount_benefits: auth.user.membership.package?.package_benefits
            ?.filter((pb: any) => pb.membership_benefit?.benefit_type === 'discount' || pb.membership_benefit?.benefit_type === 'free_registration')
            ?.map((pb: any) => ({
              benefit_id: pb.membership_benefit?.id,
              benefit_name: pb.membership_benefit?.name,
              benefit_type: pb.membership_benefit?.benefit_type,
              value_type: pb.value_type,
              value: Number(pb.value),
            })),
        });
      } else {
        setMemberInfo(null);
      }
      return;
    }

    const timer = setTimeout(() => {
      fetch(`/api/membership/check?email=${encodeURIComponent(email)}`)
        .then((res) => res.json())
        .then((resData) => {
          if (resData.is_member) {
            setMemberInfo(resData);
          } else {
            setMemberInfo(null);
          }
        })
        .catch(() => {
          setMemberInfo(null);
        });
    }, 400);

    return () => clearTimeout(timer);
  }, [data.email, auth]);

  const calculateFee = () => {
    const isIndonesia = selectedCountry === 'ID';
    let baseFee = 0;

    switch (selectedType) {
      case 'online_author':
        baseFee = isIndonesia ? Number(conference.online_fee) : Number(conference.online_fee_usd);
        break;
      case 'onsite':
        baseFee = isIndonesia ? Number(conference.onsite_fee) : Number(conference.onsite_fee_usd);
        break;
      case 'participant_only':
        baseFee = isIndonesia ? Number(conference.participant_fee) : Number(conference.participant_fee_usd);
        break;
    }

    let memberDiscountAmount = 0;
    let memberDiscountPercent = 0;

    if (memberInfo?.is_member && memberInfo.discount_benefits && memberInfo.discount_benefits.length > 0) {
      memberDiscountPercent = memberInfo.discount_benefits.reduce((maxDiscount, b) => {
        if (b.benefit_type === 'free_registration') return 100;
        if (b.value_type === 'percentage') return Math.max(maxDiscount, Number(b.value));
        return maxDiscount;
      }, 0);

      if (memberDiscountPercent > 0) {
        memberDiscountAmount = (baseFee * memberDiscountPercent) / 100;
      }
    }

    const feeAfterMember = Math.max(0, baseFee - memberDiscountAmount);

    let voucherDiscountAmount = 0;
    if (discountVoucher && feeAfterMember > 0) {
      if (discountVoucher.type === 'percent') {
        voucherDiscountAmount = (feeAfterMember * Number(discountVoucher.value)) / 100;
      } else if (discountVoucher.type === 'fixed') {
        const fixedDiscount = isIndonesia ? Number(discountVoucher.value || 0) : Number(discountVoucher.value_usd || 0);
        voucherDiscountAmount = Math.min(feeAfterMember, fixedDiscount);
      }
    }

    const totalDiscount = memberDiscountAmount + voucherDiscountAmount;
    const totalFee = Math.max(0, baseFee - totalDiscount);

    return {
      baseFee,
      memberDiscountAmount,
      memberDiscountPercent,
      voucherDiscountAmount,
      totalDiscount,
      totalFee,
    };
  };

  const {
    baseFee,
    memberDiscountAmount,
    memberDiscountPercent,
    voucherDiscountAmount,
    totalDiscount,
    totalFee
  } = calculateFee();
  const currency = (selectedCountry === 'ID' ? 'idr' : 'usd') as 'idr' | 'usd';

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    const MAX_FILE_SIZE = 50 * 1024 * 1024; // 50MB
    if (data.full_paper && data.full_paper.size > MAX_FILE_SIZE) {
      setError('full_paper', 'The full paper may not be greater than 50MB.');
      return;
    }
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
                      calculateFee(selectedCountry, value || '');
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
                transactionType="conference_registration"
                email={data.email}
                isIndonesia={selectedCountry === 'ID'}
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