import React from "react";
import { Title, Text, Grid, ActionIcon, Flex } from "@mantine/core";
import { Card } from "@mantine/core";
import { formatDate } from "../../../utils";
import { route } from "ziggy-js";

interface JoivRegistrationFee {
  id: number;
  usd_amount: string;
  idr_amount: string;
  notes: string | null;
  creator: {
    id: number;
    full_name: string;
  };
  created_at: string;
}

interface CurrentFeeProps {
  currentFee: JoivRegistrationFee | null;
  isShowEdit?: boolean;
}

export default function CurrentFee({ currentFee, isShowEdit }: CurrentFeeProps) {
  return (
    <Card shadow="sm" padding="lg" radius="md" withBorder>
      <Flex justify="space-between" align="center" mb="md">
        <Title order={4} mb="md">Current Registration Fee</Title>
        {isShowEdit && (
          <ActionIcon
            color={'yellow'}
            variant="outline"
            radius={'lg'}
            size={'lg'}
            component='a'
            href={route('joiv-articles.fee-settings')}
            title="Fee Settings"
          >
            <i className="pi pi-pencil" />
          </ActionIcon>
        )}
      </Flex>
      {currentFee ? (
        <Grid>
          <Grid.Col span={6}>
            <Text size="sm" c="dimmed">USD Amount (International)</Text>
            <Text size="xl" fw={700} c="blue">
              $ {Number.parseFloat(currentFee.usd_amount).toFixed(2)}
            </Text>
          </Grid.Col>
          <Grid.Col span={6}>
            <Text size="sm" c="dimmed">IDR Amount (Indonesia)</Text>
            <Text size="xl" fw={700} c="green">
              Rp {Number.parseFloat(currentFee.idr_amount).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
            </Text>
          </Grid.Col>
          <Grid.Col span={12}>
            <Text size="sm" c="dimmed">Last Updated</Text>
            <Text size="sm">{formatDate(currentFee.created_at)}</Text>
            {/* <Text size="sm" c="dimmed">by {currentFee.creator.full_name}</Text> */}
          </Grid.Col>
          {currentFee.notes && (
            <Grid.Col span={12}>
              <Text size="sm" c="dimmed">Notes</Text>
              <Text size="sm">{currentFee.notes}</Text>
            </Grid.Col>
          )}
        </Grid>
      ) : (
        <Text c="dimmed">No fee has been set yet.</Text>
      )}
    </Card>
  )
}