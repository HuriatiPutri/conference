import { forwardRef } from 'react';
import { Badge, Box, Card, CardSection, Divider, Flex, Group, Image, Stack, Text, ThemeIcon } from "@mantine/core";
import { IconCalendarCheck, IconIdBadge2, IconTag } from "@tabler/icons-react";
import dayjs from "dayjs";
import QRCode from "react-qr-code";
import { Membership } from "../../types";
import styles from './Membership.module.css';

type MembershipCardProps = {
    membership: Partial<Membership>,
    packageBenefits?: any[],
    statusUrl: string,
    isActive: boolean
}
const MembershipCard = forwardRef<HTMLDivElement, MembershipCardProps>(
    function MembershipCard({ membership, packageBenefits = [], statusUrl, isActive }, ref) {
        return (
            <Card ref={ref} className={styles.membershipCard}>
                <Flex justify={'space-between'} mb={'md'} align={'center'}>
                    <Image src="../../../images/logo.png" w={150} h={50} />
                    <Badge bg="white" c="blue.9">{isActive ? 'ACTIVE' : 'INACTIVE'}</Badge>
                </Flex>
                <Flex justify={'space-between'} mb={'md'}>
                    <Stack gap={0}>
                        <Text c="white" tt="uppercase" fw={600} size="12px">SOTVI Membership</Text>
                        <Text c="white" tt="uppercase" fw={700} lts={3}>{membership.package?.name}</Text>
                        <Divider />
                        <Group gap="sm" mt={'sm'}>
                            <ThemeIcon variant="white" color="blue.8" size="lg" radius="xl">
                                <IconIdBadge2 size={18} />
                            </ThemeIcon>
                            <Text fw={700} size="sm" c={'white'}>
                                {membership.public_id}
                            </Text>
                        </Group>
                    </Stack>
                    <Box p={6} bg="white" style={{ borderRadius: 12 }}>
                        {statusUrl && (
                            <QRCode value={statusUrl} size={80} fgColor="#111827" bgColor="#ffffff" />
                        )}
                    </Box>
                </Flex>
                <Flex gap={16} direction={{ base: 'column', sm: 'row' }}>
                    <Stack gap={0} className={styles.cardHolder}>
                        <Text c="#38b6ff" size="12px" tt="uppercase" fw={600}>Card Holder</Text>
                        <Text c="white" fw={600} size="sm"> {membership.first_name} {membership.last_name}</Text>
                        <Text c="white" size="9px" >{membership.email}</Text>
                    </Stack>
                    <Stack gap={6} style={{ flex: 1, minWidth: 0 }}>
                        <Text c="#38b6ff" size="12px" tt="uppercase" fw={600}>Package Benefits</Text>
                        <Group gap={5}>
                            {packageBenefits.map((benefit: any, index: number) => {
                                const benefitName = benefit.membershipBenefit?.name || benefit.membership_benefit?.name || 'Benefit';
                                // const benefitType = benefit.membershipBenefit?.benefit_type || benefit.membership_benefit?.benefit_type || benefit.value_type || '-';
                                const parts: string[] = [];

                                if (benefit.value_type === 'percentage' && benefit.value != null) {
                                    parts.push(`${Number(benefit.value)}%`);
                                }

                                if (benefit.value_type === 'item' && benefit.notes) {
                                    parts.push(String(benefit.notes));
                                }

                                if (benefit.value_type === 'quota' && benefit.quota != null) {
                                    parts.push(`Quota ${benefit.quota}`);
                                }

                                return (
                                    <Badge bg="white" size="sm" c="blue.9" key={index} pl={0} style={{ maxWidth: '100%' }} leftSection={
                                        <ThemeIcon variant="#227bf2" color="white" size="18px" radius="xl">
                                            <IconTag size={10} />
                                        </ThemeIcon >
                                    }>{benefitName} {parts.length ? ` • ${parts.join(' • ')}` : ''}</Badge>
                                );
                            })}
                        </Group>
                    </Stack>
                </Flex>
                <CardSection className={styles.footer}>
                    <Flex justify={'center'} align={'center'} gap={'xl'}>
                        <Flex gap={8} align="center">
                            <ThemeIcon variant="#227bf2" color="white" size="lg" radius="xl">
                                <IconCalendarCheck size={18} />
                            </ThemeIcon>
                            <Stack gap={0}>
                                <Text c="#38b6ff" size="9px" tt="uppercase" fw={600}>Start Date</Text>
                                <Text c="white" size="xs" >{dayjs(membership.start_date).format('DD MMM YYYY')}</Text>
                            </Stack>
                        </Flex>
                        <Divider orientation="vertical" color="white" />
                        <Flex gap={8} align="center">
                            <ThemeIcon variant="#227bf2" color="white" size="lg" radius="xl">
                                <IconCalendarCheck size={18} />
                            </ThemeIcon>
                            <Stack gap={0}>
                                <Text c="#38b6ff" size="9px" tt="uppercase" fw={600}>Valid Until</Text>
                                <Text c="white" size="xs" >{dayjs(membership.end_date).format('DD MMM YYYY')}</Text>
                            </Stack>
                        </Flex>
                    </Flex>
                </CardSection>
            </Card>
        );
    });

export default MembershipCard;