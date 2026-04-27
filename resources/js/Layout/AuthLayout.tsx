import { Head, Link, usePage } from '@inertiajs/react';
import { Anchor, AppShell, Button, Container, Group, MantineProvider, Paper, Title } from '@mantine/core';
import '@mantine/core/styles.css';
import { Notifications } from '@mantine/notifications';
import '@mantine/notifications/styles.css';
import React from 'react';
import classes from './styles.module.css';

interface AuthLayoutProps {
  title?: string;
  children: React.ReactNode;
}

export default function AuthLayout({ title, children }: AuthLayoutProps) {
  const { auth } = usePage().props as any;

  return (
    <>
      <Head title={title ? title : 'Sotvi.org'} />
      <MantineProvider>
        <Notifications position='top-center' />
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
                {auth?.user ? (
                  <Button
                    component={Link}
                    href="/dashboard"
                    variant="subtle"
                    size="sm"
                  >
                    Dashboard
                  </Button>
                ) : (
                  <Group gap="sm">
                    <Button
                      component={Link}
                      href="/register-membership"
                      variant="subtle"
                      size="sm"
                    >
                      Join Us
                    </Button>
                    <Button
                      component={Link}
                      href="/login/member"
                      variant="subtle"
                      size="sm"
                    >
                      Login
                    </Button>
                  </Group>
                )}
              </Group>
            </Container>
          </AppShell.Header>

          <AppShell.Main>

            <div className={classes.wrapper}>
              <Paper className={classes.form}>
                {children}
              </Paper>
            </div>
          </AppShell.Main>
        </AppShell>
      </MantineProvider>
    </>
  );
}
