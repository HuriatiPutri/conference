import React from "react";
import { Badge, Button, Checkbox, Stack, Text } from "@mantine/core";
import { Audiences } from "../../../types";
import { BadgeStatus } from "./ExtendComponent";
import { formatCurrency } from "../../../utils";
import { route } from 'ziggy-js';
import { PAYMENT_METHOD, PRESENTATION_TYPE } from "../../../Constants";
import { ActionButtonExt } from "../Conferences/ExtendComponent";

type DataProps = {
  _handleRedirectWa: (row: Audiences) => void;
  handlePaymentStatusClick: (row: Audiences) => void;
  role?: string;
}
export const TableData = ({ _handleRedirectWa, handlePaymentStatusClick, role }: DataProps) => [
  {
    field: 'serial_number',
    label: 'No.',
    style: { minWidth: '5rem' },
    sortable: false,
    renderCell: (_: Audiences, { rowIndex }: { rowIndex: number }) => rowIndex + 1,
  },
  {
    label: 'Conference',
    name: 'conference.name',
    className: 'text-wrap w-40',
    renderCell: (row: Audiences) => (
      <Text size='sm' style={{ textWrap: 'wrap' }}>{row.conference?.name}</Text>
    ),
    sortable: true,
  },
  {
    label: 'First Name',
    name: 'first_name',
  },
  {
    label: 'Last Name',
    name: 'last_name',
  },
  {
    label: 'Phone Number',
    name: 'phone_number',
    renderCell: (row: Audiences) => (
      <Text component='a' c={'blue'} onClick={() => _handleRedirectWa(row)}>{row.phone_number}</Text>
    ),
  },
  {
    label: 'Email',
    name: 'email',
    renderCell: (row: Audiences) => (
      <Text fz={'sm'} style={{ whiteSpace: 'nowrap' }}>
        {row.email}
      </Text>
    ),
  },
  {
    label: 'Participant Type',
    name: 'presentation_type',
    renderCell: (row: Audiences) => PRESENTATION_TYPE[row.presentation_type as keyof typeof PRESENTATION_TYPE],
  },
  {
    label: 'Payment Method',
    name: 'payment_method',
    renderCell: (row: Audiences) => {
      const isTransferWithProof = row.payment_method === 'transfer_bank' && row.payment_proof_path;

      return (
        <Stack>
          <span>{PAYMENT_METHOD[row.payment_method as keyof typeof PAYMENT_METHOD]}</span>
          {isTransferWithProof && (
            <Button
              color="blue"
              size="xs"
              variant="light"
              leftSection={<i className="pi pi-download" />}
              onClick={() => window.open(`/storage/${row.payment_proof_path}`, '_blank')}
            >
              Download Proof
            </Button>
          )}
        </Stack>
      );
    },
  },
  {
    label: 'Amount Paid',
    name: 'paid_fee',
    renderCell: (row: Audiences) => {
      const currency = row.country === 'ID' ? 'idr' : 'usd';
      const hasVoucher = !!(row.voucher || row.voucher_code);
      const discountBenefitUsages = row.benefit_usages?.filter(
        (bu) => (bu.benefit_type === 'discount' || bu.benefit_type === 'free_registration') && Number(bu.consumed_value) > 0
      ) || [];
      const originalFee = Number(row.original_fee || (Number(row.paid_fee) + Number(row.discount_amount || 0)));
      const hasDiscount = originalFee > Number(row.paid_fee) || hasVoucher || discountBenefitUsages.length > 0;

      return (
        <Stack gap={3}>
          <Text fw={600} size="sm" style={{ whiteSpace: 'nowrap' }}>
            {formatCurrency(row.paid_fee, currency)}
          </Text>

          {hasDiscount && originalFee > Number(row.paid_fee) && (
            <Text size="xs" c="dimmed" style={{ textDecoration: 'line-through', whiteSpace: 'nowrap' }}>
              {formatCurrency(originalFee, currency)}
            </Text>
          )}

          {hasVoucher && (
            <Badge size="xs" color="teal" variant="light">
              Voucher: {row.voucher?.code || row.voucher_code}
              {row.voucher?.discount_type === 'percentage'
                ? ` (-${row.voucher.discount_value}%)`
                : row.voucher?.discount_value
                ? ` (-${formatCurrency(row.country === 'ID' ? row.voucher.discount_value : (row.voucher.discount_value_usd || row.voucher.discount_value), currency)})`
                : ''}
            </Badge>
          )}

          {discountBenefitUsages.map((bu, idx) => (
            <Badge key={idx} size="xs" color="indigo" variant="light">
              {bu.membership_benefit?.name || 'Member Discount'}
              {bu.consumed_value ? ` (-${formatCurrency(bu.consumed_value, currency)})` : ''}
            </Badge>
          ))}
        </Stack>
      );
    },
  },
  {
    label: 'Payment Status',
    name: 'payment_status',
    renderCell: (row: Audiences) => (
      <Stack>
        <BadgeStatus status={row.payment_status} />
        {row.payment_status === 'paid' && (
          <Button
            component="a"
            size="xs"
            variant="light"
            leftSection={<i className="pi pi-download" />}
            href={route('audiences.receipt', row.id)}
            target="_blank"
          >
            Download Receipt
          </Button>
        )}
      </Stack>
    ),
  },
  {
    label: 'Paper',
    name: 'paper_title',
    renderCell: (row: Audiences) => (
      <Stack w={250}>
        <Text size='sm' style={{ textWrap: 'wrap' }}>{row.paper_title}</Text>
        {row.full_paper_path && (
          <Button
            color="blue"
            size="xs"
            variant="light"
            leftSection={<i className="pi pi-download" />}
            onClick={() => window.open(`/storage/${row.full_paper_path}`, '_blank')}
          >
            Download Paper
          </Button>
        )}
      </Stack>
    ),
  },
  {
    label: 'Keynote',
    name: 'key_notes',
    renderCell: (row: Audiences) => <Checkbox checked={row.key_notes.length > 0} />,
  },
  {
    label: 'Parallel Session',
    name: 'parallel_sessions',
    renderCell: (row: Audiences) => <Checkbox checked={row.parallel_sessions.length > 0} />,
  },
  {
    label: 'Certificate',
    name: 'certificate',
    renderCell: (row: Audiences) => {
      const hasTemplate = row.conference?.certificate_template_path && row.conference?.certificate_template_position;
      const hasSubmissions = row.key_notes.length > 0 && row.parallel_sessions.length > 0;
      const canDownload = hasTemplate && hasSubmissions;

      if (canDownload) {
        return (
          <Button
            color="green"
            size="xs"
            component="a"
            href={route('audiences.download', row.public_id)}
            target="_blank"
            variant="light"
            leftSection={<i className="pi pi-download" />}
          >
            Download
          </Button>
        );
      }

      let message = 'Certificate Not Available';
      if (!hasTemplate) message = 'Template Not Set';
      else if (!hasSubmissions) message = 'No Keynote/Parallel Session';

      return (
        <Text fz={'xs'} c="dimmed">
          {message}
        </Text>
      );
    },
  },
  {
    label: 'Action',
    name: 'action',
    hidden: role === 'user',
    renderCell: (row: Audiences) => (
      <Stack gap={'xs'} justify="center" align="center">
        {row.payment_method === 'transfer_bank' && (
          <ActionButtonExt
            color="blue"
            handleClick={() => handlePaymentStatusClick(row)}
            icon="pi pi-fw pi-credit-card"
            title="Update Payment Status"
          />
        )}
      </Stack>
    ),
  },
];