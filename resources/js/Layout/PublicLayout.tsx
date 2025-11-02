import React from 'react';
import { MantineProvider, AppShell, Container, Group, Title, Button, Anchor } from '@mantine/core';
import { Head, Link } from '@inertiajs/react';
import '@mantine/core/styles.css';
import 'primeicons/primeicons.css';

interface PublicLayoutProps {
  children: React.ReactNode;
  title?: string;
}

function PublicLayout({ children, title }: PublicLayoutProps) {
  return (
    <>
      <Head title={title} />
      <MantineProvider>
        <AppShell
          header={{ height: 60 }}
        >
          <AppShell.Header>
            <Container size="lg" h="100%">
              <Group justify="space-between" h="100%">
                <Anchor component={Link} href="/" underline="never">
                  <Title order={3} c="blue">
                    SOTVI Conference
                  </Title>
                </Anchor>
                <Group gap="sm">
                  <Button
                    component={Link}
                    href="/register"
                    variant="light"
                    size="sm"
                  >
                    Register
                  </Button>
                </Group>
              </Group>
            </Container>
          </AppShell.Header>

          <AppShell.Main>
            {children}
          </AppShell.Main>
        </AppShell>
      </MantineProvider>
    </>
  );
}

export default PublicLayout;