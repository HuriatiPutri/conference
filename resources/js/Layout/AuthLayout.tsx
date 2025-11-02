import { Head } from '@inertiajs/react';
import { MantineProvider } from '@mantine/core';
import '@mantine/core/styles.css';
import { Notifications } from '@mantine/notifications';
import '@mantine/notifications/styles.css';
import React from 'react';
import styles from './styles.module.css';

interface AuthLayoutProps {
  title?: string;
  children: React.ReactNode;
}

export default function AuthLayout({ title, children }: AuthLayoutProps) {
  return (
    <>
      <Head title={title ? title : 'Laravel + Inertia + React'} />
      <MantineProvider>
        <Notifications position='top-center' />
        <section className={styles.root}>
          <main className={styles.content}>
            {children}
          </main>
        </section>
      </MantineProvider>
    </>
  );
}
