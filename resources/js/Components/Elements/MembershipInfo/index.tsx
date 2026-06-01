import { Badge, Card, Group, List, Stack, Text, ThemeIcon } from "@mantine/core";
import { IconCheck } from "@tabler/icons-react";

type MembershipInfoProps = {
  membership: any;
  packageName: string;
  packageBenefits: any[];
};

export default function MembershipInfo({
  membership,
  packageName,
  packageBenefits,
}: Readonly<MembershipInfoProps>) {
  return (
    <Card padding="lg" bg="green.0">
      <Stack gap="sm">
        <div>
          <Text fw={700} size="lg">Active Membership</Text>
          <Text c="dimmed" size="sm">Your package and available benefits</Text>
        </div>

        <Group gap="md" wrap="wrap">
          <Badge size="lg" variant="light" color="green">
            {packageName}
          </Badge>
          <Badge size="lg" variant="light" color="blue">
            Status: {membership?.status || 'inactive'}
          </Badge>
        </Group>

        <div>
          <Text fw={600} mb={4}>Benefits</Text>
          {packageBenefits.length > 0 ? (
            <List spacing="xs" size="sm" type="ordered" icon={
              <ThemeIcon color="teal" size={20} radius="xl">
                <IconCheck size={16} />
              </ThemeIcon>
            }>
              {packageBenefits.map((benefit: any) => {
                const benefitName = benefit.membershipBenefit?.name || benefit.membership_benefit?.name || 'Benefit';
                const benefitType = benefit.membershipBenefit?.benefit_type || benefit.membership_benefit?.benefit_type || benefit.value_type || '-';
                const detailParts = [] as string[];

                if (benefit.value_type === 'percentage' && benefit.value != null) {
                  detailParts.push(`${Number(benefit.value)}% discount`);
                }

                if (benefit.value_type === 'item' && benefit.notes) {
                  detailParts.push(String(benefit.notes));
                }

                if (benefit.value_type === 'quota' && benefit.quota != null) {
                  detailParts.push(`Quota ${benefit.quota}`);
                }

                if (benefit.membershipBenefit?.benefit_type === 'discount') {
                  detailParts.push('Applied on registration fee');
                }

                return (
                  <List.Item key={benefit.id}>
                    <Text size="sm">
                      <b>{benefitName}</b> <Text span c="dimmed">({benefitType})</Text>
                      {detailParts.length > 0 ? ` - ${detailParts.join(' | ')}` : ''}
                    </Text>
                  </List.Item>
                );
              })}
            </List>
          ) : (
            <Text size="sm" c="dimmed">No benefits attached to this membership package.</Text>
          )}
        </div>
      </Stack>
    </Card>
  )
}