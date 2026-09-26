import React from "react"
import { Card, Group, Text, ThemeIcon, Title } from "@mantine/core"
import { IconCalendar } from "@tabler/icons-react"

interface EmptyEventProps {
    title: string,
    description: string
}
export const EmptyEvent = ({ title, description }: EmptyEventProps) => {

    return (
        <Card
            radius="xl"
            p="xl"
            withBorder
            mt="sm"
            style={{
                background: 'linear-gradient(135deg, var(--mantine-color-gray-0) 0%, var(--mantine-color-gray-1) 100%)',
                borderStyle: 'dashed'
            }}
        >
            <Group justify="space-between" align="center">
                <div>
                    <Group gap="sm" mb="xs">
                        <ThemeIcon color="gray" variant="light" size="lg" radius="xl">
                            <IconCalendar size={18} />
                        </ThemeIcon>
                        <Title order={3} c="dark.7">{title}</Title>
                    </Group>
                    <Text c="dimmed" size="md">
                        {description}
                    </Text>
                </div>
            </Group>
        </Card>
    )
}