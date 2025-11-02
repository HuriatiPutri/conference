import React from "react";
import MenuItem from "./MenuItem";

export default function Menu() {
  const items = [
    {
      label: 'Dashboard',
      icon: 'pi pi-fw pi-home',
      active: true,
      items:
        [
          {
            label: 'New',
            icon: 'pi pi-fw pi-plus',
          },
          {
            label: 'Open',
            icon: 'pi pi-fw pi-external-link',
          },
        ],
    },
    {
      label: 'Conference',
      icon: 'pi pi-fw pi-pencil',
      items: [
        {
          label: 'Left',
          icon: 'pi pi-fw pi-align-left',
        },
        {
          label: 'Right',
          icon: 'pi pi-fw pi-align-right',
        },
      ],
    }];

  return (
    <div>
      <h2>Sotvi.org</h2>
      {items.map((item, index) => (
        <MenuItem key={index} {...item} />
      ))}
    </div>
  )
}