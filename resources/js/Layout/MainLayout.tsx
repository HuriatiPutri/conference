import { Head } from '@inertiajs/react';
import { MantineProvider } from '@mantine/core';
import '@mantine/core/styles.css';
import '@mantine/notifications/styles.css';
import { PrimeReactProvider } from 'primereact/api';
import { Avatar } from 'primereact/avatar';
import React from 'react';
import styles from './styles.module.css';
import Navigation from '../Components/Elements/Navbar/Navigation';
import { Notifications } from '@mantine/notifications';

interface MainLayoutProps {
  title?: string;
  children: React.ReactNode;
}

export default function MainLayout({ title, children }: MainLayoutProps) {
  const [drawerVisible, setDrawerVisible] = React.useState(false);
  const [isMobile, setIsMobile] = React.useState(false);

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
                  <Avatar label="P" shape="circle" />
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
