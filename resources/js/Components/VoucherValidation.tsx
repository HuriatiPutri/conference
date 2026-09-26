import React, { useState, useCallback, useEffect } from 'react';
import { TextInput, Group, Loader, Text, Stack } from '@mantine/core';
import { IconCheck, IconX } from '@tabler/icons-react';

interface VoucherValidationProps {
  value: string;
  onChange: (value: string) => void;
  onValidationChange: (isValid: boolean, discount?: { type: string; value: number; description?: string }) => void;
  transactionType: 'conference_registration' | 'joiv_article' | 'membership_registration';
  email: string;
  disabled?: boolean;
}

interface ValidationResult {
  valid: boolean;
  message: string;
  discount_type?: string;
  discount_value?: number;
  discount_description?: string;
}

export default function VoucherValidation({
  value,
  onChange,
  onValidationChange,
  transactionType,
  email,
  disabled = false,
}: Readonly<VoucherValidationProps>) {
  const [validationState, setValidationState] = useState<'idle' | 'loading' | 'valid' | 'invalid'>('idle');
  const [validationMessage, setValidationMessage] = useState('');
  const [discount, setDiscount] = useState<{ type: string; value: number; description?: string } | null>(null);

  const validateVoucher = useCallback(async (code: string) => {
    if (!code.trim() || code.length !== 6) {
      setValidationState('idle');
      setValidationMessage('Code must be 6 characters long.');
      setDiscount(null);
      onValidationChange(false);
      return;
    }

    if (!email) {
      setValidationState('idle');
      setValidationMessage('Please enter your email first.');
      onValidationChange(false);
      return;
    }

    setValidationState('loading');

    try {
      const params = new URLSearchParams({
        transaction_type: transactionType,
        email: email,
      });

      const response = await fetch(`/api/vouchers/validate/${code}?${params.toString()}`);
      const data: ValidationResult = await response.json();

      if (data.valid) {
        setValidationState('valid');
        setValidationMessage(data.message);
        const discountData = {
          type: data.discount_type || 'percent',
          value: data.discount_value || 0,
          description: data.discount_description,
        };
        setDiscount(discountData);
        onValidationChange(true, discountData);
      } else {
        setValidationState('invalid');
        setValidationMessage(data.message);
        setDiscount(null);
        onValidationChange(false);
      }
    } catch (error) {
      console.error('Voucher validation error:', error);
      setValidationState('invalid');
      setValidationMessage('Error validating voucher. Please try again.');
      setDiscount(null);
      onValidationChange(false);
    }
  }, [transactionType, email]);

  // Debounce validation
  useEffect(() => {
    const timer = setTimeout(() => {
      if (value.trim()) {
        validateVoucher(value.toUpperCase());
      } else {
        setValidationState('idle');
        setValidationMessage('');
        setDiscount(null);
        onValidationChange(false);
      }
    }, 500);

    return () => clearTimeout(timer);
  }, [value, validateVoucher]);

  const getRightSection = () => {
    if (validationState === 'loading') {
      return <Loader size={20} />;
    }
    if (validationState === 'valid') {
      return <IconCheck size={20} color="green" />;
    }
    if (validationState === 'invalid') {
      return <IconX size={20} color="red" />;
    }
    return null;
  };

  const getBorderColor = () => {
    if (validationState === 'valid') return 'green';
    if (validationState === 'invalid') return 'red';
    return undefined;
  };

  return (
    <Stack gap="xs">
      <TextInput
        label="Voucher Code"
        placeholder="Enter 6-character voucher code"
        value={value}
        onChange={(e) => onChange(e.currentTarget.value.toUpperCase())}
        maxLength={6}
        disabled={disabled}
        rightSection={getRightSection()}
        style={{
          borderColor: getBorderColor(),
        }}
      />
      {validationMessage && (
        <Group gap={4}>
          <Text size="sm" c={validationState === 'valid' ? 'green' : 'red'}>
            {validationMessage}
          </Text>
        </Group>
      )}
      {discount && validationState === 'valid' && (
        <div style={{ padding: '10px', backgroundColor: '#f0f9ff', borderRadius: '4px', borderLeft: '3px solid #00a3ff' }}>
          <Text size="sm" fw={500}>
            Discount: {discount.type === 'percent' ? `${discount.value}%` : `$${discount.value.toFixed(2)}`}
          </Text>
          {discount.description && <Text size="xs" c="dimmed">{discount.description}</Text>}
        </div>
      )}
    </Stack>
  );
}
