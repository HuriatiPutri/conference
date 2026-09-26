import { Head, usePage, router } from '@inertiajs/react';
import { Avatar, MantineProvider, Menu } from '@mantine/core';
import '@mantine/core/styles.css';
import '@mantine/notifications/styles.css';
import { PrimeReactProvider } from 'primereact/api';
import React from 'react';
import styles from './styles.module.css';
import Navigation from '../Components/Elements/Navbar/Navigation';
import { Notifications } from '@mantine/notifications';
import { IconKey, IconSettings, IconUser } from '@tabler/icons-react';

interface MainLayoutProps {
  title?: string;
  children: React.ReactNode;
}

export default function MainLayout({ title, children }: MainLayoutProps) {
  const [drawerVisible, setDrawerVisible] = React.useState(false);
  const [isMobile, setIsMobile] = React.useState(false);
  const { auth } = usePage().props as any;

  React.useEffect(() => {
    const handleResize = () => {
      setIsMobile(window.innerWidth <= 768);
      if (window.innerWidth > 768) {
        setDrawerVisible(true); // Ensure sidebar is visible on desktop
      } else {
        setDrawerVisible(false); // Hide sidebar on mobile by default
      }
    };

    window.addEventListener('resize', handleResize);
    handleResize(); // Initial check

    return () => window.removeEventListener('resize', handleResize);
  }, []);

  return (
    <>
      <Head title={title ? title : 'Sotvi.org'} />
      <MantineProvider>
        <Notifications position='top-center' />
        <PrimeReactProvider>
          <section className={styles.root}>
            {!isMobile || drawerVisible ? (
              <Navigation />
            ) : null}
            <main className={styles.content}>
              <div className={styles.header}>
                <div className={styles.breadcrumbs}>
                  {isMobile && (
                    <div className={styles.burger} onClick={() => setDrawerVisible(!drawerVisible)}>
                      <i className="pi pi-bars"></i>
                    </div>
                  )}
                  <span>{title}</span>
                </div>
                <div className={styles.userMenu}>
                  <Menu shadow="md" width={200}>
                    <Menu.Target>
                      <div style={{ cursor: 'pointer' }}>
                        <Avatar color="blue">
                          {auth.user?.name ? auth.user.name.charAt(0).toUpperCase() : 'U'}
                        </Avatar>
                      </div>
                    </Menu.Target>

                    <Menu.Dropdown>
                      <Menu.Item leftSection={<IconUser size={16} />} onClick={() => router.visit('/profile')}>
                        Profile
                      </Menu.Item>
                      <Menu.Item leftSection={<IconSettings size={16} />} onClick={() => router.visit('/settings')}>
                        Settings
                      </Menu.Item>
                      <Menu.Item leftSection={<IconKey size={16} />} onClick={() => router.visit('/profile/password')}>
                        Change Password
                      </Menu.Item>
                    </Menu.Dropdown>
                  </Menu>
                </div>
              </div>
              <div className="container mx-auto mt-4">{children}</div>
            </main>
          </section>
        </PrimeReactProvider>
      </MantineProvider>
    </>
  );
}
