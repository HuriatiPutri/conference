import React from 'react';
import { Button as ButtonBase } from 'primereact/button';
import styles from './Button.module.css';

interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  children: React.ReactNode;
  className?: string;
  size?: 'small' | 'large';
}

export default function Button({ children, className = '', ...props }: ButtonProps) {
  const size = props.size || 'small';
  if (size) {
    className += ` ${size}`;
  }
  const classes = [styles.customButton, className].join(' ').trim();
  return (
    <ButtonBase className={classes} {...props}>
      {children}
    </ButtonBase>
  );
}
