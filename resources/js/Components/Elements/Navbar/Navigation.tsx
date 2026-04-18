import { Code, Group, ScrollArea, Text, Button } from '@mantine/core';
import { IconGauge, IconPodium, IconUsersGroup, IconLogout, IconMicrophone, IconUsers, IconRubberStamp, IconArticle } from '@tabler/icons-react';
import React from 'react';
import { router, usePage } from '@inertiajs/react';
import { LinksGroup } from '../LinkGroup/LinksGroup';
import classes from './NavbarNested.module.css';

const adminMenus = [
  { label: 'Dashboard', icon: IconGauge, link: '/dashboard' },
  { label: 'Conference', icon: IconPodium, link: '/conferences' },
  { label: 'Audience', icon: IconUsersGroup, link: '/audiences' },
  { label: 'Keynote', icon: IconMicrophone, link: '/keynotes' },
  { label: 'Parallel Session', icon: IconUsers, link: '/parallel-sessions' },
  { label: 'Memberships', icon: IconUsersGroup, link: '/memberships' },
  { label: 'Joiv Article', icon: IconArticle, link: '/joiv-articles' },
  {
    label: 'Letter Of Approval', icon: IconRubberStamp, links: [
      { label: 'LoA Approval', link: '/letters-of-approval' },
      { label: 'LoA Volume', link: '/loa/loa-volumes' },
    ]
  }
];

const userMenus = [
  { label: 'Dashboard', icon: IconGauge, link: '/dashboard' },
  { label: 'My Conferences', icon: IconUsersGroup, link: '/audiences' },
  { label: 'Joiv Article', icon: IconArticle, link: '/joiv-articles' },
];
export function Navigation() {

  const { auth } = usePage().props as any;

  const role = auth.role;
  const pathname = window.location.pathname;

  const links = role === 'admin' ?
    adminMenus.map(item => <LinksGroup {...item} active={!!item.link && pathname.includes(item.link)} key={item.label} />)
    : userMenus.map(item => <LinksGroup {...item} active={!!item.link && pathname.includes(item.link)} key={item.label} />);

  const handleLogout = () => {
    router.post('/logout');
  };

  return (
    <nav className={classes.navbar}>
      <div className={classes.header}>
        <Group justify="space-between">
          <Text fw={600}>SOTVI </Text>
          <Code fw={700}>{role}</Code>
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
