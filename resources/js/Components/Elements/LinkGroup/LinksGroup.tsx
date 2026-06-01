import React, { useState } from 'react';
import { IconCalendarStats, IconChevronRight } from '@tabler/icons-react';
import { Box, Collapse, Divider, Group, Text, ThemeIcon, UnstyledButton } from '@mantine/core';
import classes from './NavbarLinksGroup.module.css';
import { router } from '@inertiajs/react';

interface LinksGroupProps {
  icon?: React.FC<any>;
  label: string;
  initiallyOpened?: boolean;
  link?: string;
  active?: boolean;
  links?: { label: string; link: string }[];
  type?: 'divider';
}

export function LinksGroup({ icon: Icon, label, link, initiallyOpened, links, active, type }: LinksGroupProps) {
  const hasLinks = Array.isArray(links);
  const [opened, setOpened] = useState(initiallyOpened || false);
  const items = (hasLinks ? links : []).map(link => (
    <Text<'a'>
      component="a"
      className={classes.link}
      href={link.link}
      key={link.label}
      onClick={event => {
        event.preventDefault();
        router.visit(link.link);
      }}
    >
      {link.label}
    </Text>
  ));

  const handleMenu = () => {
    if (link) {
      router.visit(link);
    } else {
      if (hasLinks) {
        setOpened(o => !o);
      }
    }
  };
  return (
    <>
      <UnstyledButton onClick={handleMenu} className={`${classes.control} ${active ? classes.controlActive : ''}`}>
        {type === 'divider' ? (
          <Divider my="xs" label={label} labelPosition="left" />
        ) : (
          <Group justify="space-between" gap={0}>
            <Box style={{ display: 'flex', alignItems: 'center' }}>
              <ThemeIcon variant="light" size={30}>
                {Icon ? <Icon size={18} /> : null}
              </ThemeIcon>
              <Box ml="md">{label}</Box>
            </Box>
            {hasLinks && (
              <IconChevronRight
                className={classes.chevron}
                stroke={1.5}
                size={16}
                style={{ transform: opened ? 'rotate(-90deg)' : 'none' }}
              />
            )}
          </Group>
        )}
      </UnstyledButton>
      {hasLinks ? <Collapse in={opened}>{items}</Collapse> : null}

    </>
  );
}

const mockdata = {
  label: 'Releases',
  icon: IconCalendarStats,
  links: [
    { label: 'Upcoming releases', link: '/' },
    { label: 'Previous releases', link: '/' },
    { label: 'Releases schedule', link: '/' },
  ],
};

export function NavbarLinksGroup() {
  return (
    <Box mih={220} p="md">
      <LinksGroup {...mockdata} />
    </Box>
  );
}
