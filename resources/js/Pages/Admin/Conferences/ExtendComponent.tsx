import React from 'react';
import { ActionIcon, Button, CopyButton, Tooltip } from '@mantine/core';

interface CopyButtonExtProps {
  value: string;
  label: string;
}

interface ActionButtonExtProps {
  children?: React.ReactNode;
  handleClick?: (row: any) => void;
  icon?: string;
  [key: string]: unknown;
}

export const CopyButtonExt = ({ value, label }: CopyButtonExtProps) => {
  return (
    <CopyButton value={value} timeout={2000}>
      {({ copied, copy }) => (
        <Tooltip label={copied ? 'Copied' : 'Copy'} withArrow position="right">
          <Button
            size="xs"
            onClick={copy}
            rightSection={copied ? <i className="pi pi-check" /> : <i className="pi pi-copy"></i>}
          >
            {label}
          </Button>
        </Tooltip>
      )}
    </CopyButton>
  );
};

export const ActionButtonExt = ({
  children,
  handleClick,
  icon,
  ...props
}: ActionButtonExtProps) => {
  return (
    <ActionIcon
      variant="outline"
      color={props.color || 'blue'}
      onClick={() => handleClick(props.row)}
    >
      {children ? children : <i className={icon}></i>}
    </ActionIcon>
  );
};
