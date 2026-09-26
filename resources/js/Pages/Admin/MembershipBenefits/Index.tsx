import { router, usePage } from '@inertiajs/react';
import { ActionIcon, Button, Card, Container, Group, Stack, Table, Text, Title } from '@mantine/core';
import { IconPlus, IconEdit, IconTrash } from '@tabler/icons-react';
import React from 'react';
import { route } from 'ziggy-js';
import MainLayout from '../../../Layout/MainLayout';

function MembershipBenefitsIndex() {
  const { membershipBenefits } = usePage<any>().props;
  const rows = membershipBenefits?.data || [];

  const handleDelete = (id: number) => {
    if (!confirm('Are you sure to delete this benefit?')) return;
    router.delete(route('membership-benefits.destroy', id));
  };

  return (
    <MainLayout title="Membership Benefits">
      <Container size="xl">
        <Stack gap="lg">
          <Group position="apart">
            <div>
              <Title order={2}>Membership Benefits</Title>
              <Text c="dimmed">Manage available membership benefits</Text>
            </div>
            <Button leftIcon={<IconPlus size={16} />} onClick={() => router.get(route('membership-benefits.create'))}>Add Benefit</Button>
          </Group>

          <Card withBorder>
            <Table verticalSpacing="md">
              <thead>
                <tr>
                  <th>Code</th>
                  <th>Name</th>
                  <th>Type</th>
                  <th>Description</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                {rows.map((b: any) => (
                  <tr key={b.id}>
                    <td>{b.code}</td>
                    <td>{b.name}</td>
                    <td>{b.benefit_type}</td>
                    <td>{b.description}</td>
                    <td>
                      <Group spacing="xs">
                        <ActionIcon onClick={() => router.get(route('membership-benefits.edit', b.id))}>
                          <IconEdit size={16} />
                        </ActionIcon>
                        <ActionIcon color="red" onClick={() => handleDelete(b.id)}>
                          <IconTrash size={16} />
                        </ActionIcon>
                      </Group>
                    </td>
                  </tr>
                ))}
              </tbody>
            </Table>
          </Card>
        </Stack>
      </Container>
    </MainLayout>
  );
}

export default MembershipBenefitsIndex;
