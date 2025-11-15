import { Code, Group, ScrollArea, Text, Button } from '@mantine/core';
import { IconGauge, IconPodium, IconUsersGroup, IconLogout, IconMicrophone, IconUsers, IconRubberStamp } from '@tabler/icons-react';
import React from 'react';
import { router } from '@inertiajs/react';
import { LinksGroup } from '../LinkGroup/LinksGroup';
import classes from './NavbarNested.module.css';

const mockdata = [
  { label: 'Dashboard', icon: IconGauge, link: '/dashboard' },
  { label: 'Conference', icon: IconPodium, link: '/conferences' },
  { label: 'Audience', icon: IconUsersGroup, link: '/audiences' },
  { label: 'Keynote', icon: IconMicrophone, link: '/keynotes' },
  { label: 'Parallel Session', icon: IconUsers, link: '/parallel-sessions' },
  {
    label: 'Letter Of Approval', icon: IconRubberStamp, links: [
      { label: 'LoA Approval', link: '/letters-of-approval' },
      { label: 'LoA Volume', link: '/loa/loa-volumes' },
    ]
  }
];

export function Navigation() {

  const pathname = window.location.pathname;

  const links = mockdata.map(item => <LinksGroup {...item} active={pathname.includes(item.link)} key={item.label} />);

  const handleLogout = () => {
    router.post('/logout');
  };

  return (
    <nav className={classes.navbar}>
      <div className={classes.header}>
        <Group justify="space-between">
          <Text fw={600}>SOTVI</Text>
          <Code fw={700}>v2.0.0</Code>
        </Group>
      </div>

      <ScrollArea className={classes.links}>
        <div className={classes.linksInner}>{links}</div>
      </ScrollArea>

      <div className={classes.footer}>
        <Button
          variant="light"
          color="red"
          leftSection={<IconLogout size="1rem" />}
          onClick={handleLogout}
          fullWidth
        >
          Logout
        </Button>
      </div>
    </nav>
  );
}

export default Navigation;
