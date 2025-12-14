import React from "react";
import { Button, Card, Grid, Select, Title } from "@mantine/core"

type Props = {
  conferences: Array<{ id: number; name: string }>;
  conferenceFilter: string;
  paymentMethodFilter: string;
  paymentStatusFilter: string;
  setConferenceFilter: (value: string) => void;
  setPaymentMethodFilter: (value: string) => void;
  setPaymentStatusFilter: (value: string) => void;
  handleFilterChange: () => void;
  clearFilters: () => void;
}

export const FilterData = ({ conferences, paymentStatusFilter, conferenceFilter, paymentMethodFilter, setConferenceFilter, setPaymentMethodFilter, setPaymentStatusFilter, handleFilterChange, clearFilters }: Props) => {
  return (
    <Card padding="lg" radius="md" withBorder>
      <Title order={4} mb="md">Filter Audience Data</Title>
      <Grid>
        <Grid.Col span={{ base: 12, md: 3 }}>
          <Select
            placeholder="-- All Conferences --"
            data={[
              { value: '', label: '-- All Conferences --' },
              ...conferences.map(conf => ({ value: conf.id.toString(), label: conf.name }))
            ]}
            value={conferenceFilter}
            onChange={(value) => setConferenceFilter(value || '')}
            style={{ minWidth: 200 }}
          />
        </Grid.Col>

        <Grid.Col span={{ base: 12, md: 3 }}>
          <Select
            placeholder="-- All Payment Methods --"
            data={[
              { value: '', label: '-- All Payment Methods --' },
              { value: 'payment_gateway', label: 'Payment Gateway' },
              { value: 'transfer_bank', label: 'Bank Transfer' }
            ]}
            value={paymentMethodFilter}
            onChange={(value) => setPaymentMethodFilter(value || '')}
            style={{ minWidth: 200 }}
          />
        </Grid.Col>

        <Grid.Col span={{ base: 12, md: 3 }}>
          <Select
            placeholder="-- All Payment Status --"
            data={[
              { value: '', label: '-- All Payment Status --' },
              { value: 'paid', label: 'Paid' },
              { value: 'pending_payment', label: 'Pending' },
              { value: 'cancelled', label: 'Cancelled' },
              { value: 'refunded', label: 'Refunded' }
            ]}
            value={paymentStatusFilter}
            onChange={(value) => setPaymentStatusFilter(value || '')}
            style={{ minWidth: 200 }}
          />
        </Grid.Col>

        <Grid.Col span={{ base: 12, md: 3 }}>
          <Button onClick={handleFilterChange} variant="filled" mr={'sm'}>
            Apply Filter
          </Button>
          <Button onClick={clearFilters} variant="outline" mr={'sm'}>
            Clear Filter
          </Button>
        </Grid.Col>
      </Grid>
    </Card>
  )
}