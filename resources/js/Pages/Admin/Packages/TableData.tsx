import { ActionIcon, Badge } from '@mantine/core';
import { IconPencil, IconTrash } from '@tabler/icons-react';
import React from 'react';
import { router } from '@inertiajs/react';
import { route } from 'ziggy-js';

export const TableData = ({ handleDelete }: { handleDelete: any }) => {
  const cols: any[] = [
    {
      field: 'name',
      label: 'Name',
      renderCell: (row: any) => <span>{row.name}</span>,
      sortable: true,
    },
    {
      field: 'price_idr',
      label: 'Price (IDR)',
      renderCell: (row: any) => <span>Rp {Number(row.price_idr).toLocaleString('id-ID')}</span>,
      sortable: true,
    },
    {
      field: 'duration',
      label: 'Duration (days)',
      renderCell: (row: any) => <span>{row.duration}</span>,
      sortable: true,
    },
    {
      field: 'benefits',
      label: 'Benefits',
      renderCell: (row: any) => {
        const packageBenefits = row.packageBenefits || row.package_benefits || [];

        if (!packageBenefits.length) {
          return <span>-</span>;
        }

        return (
          <div style={{ display: 'flex', flexWrap: 'wrap', gap: 6 }}>
            {packageBenefits.map((benefit: any) => {
              const benefitName = benefit.membership_benefit?.name || benefit.membershipBenefit?.name || 'Benefit';
              const valueType = benefit.value_type || benefit.valueType;
              const value = benefit.value;
              const notes = benefit.notes;

              const summaryParts = [
                valueType === 'percentage' && value != null ? `${Number(value)}%` : null,
                valueType === 'item' && notes ? notes : null,
                valueType === 'quota' && benefit.quota != null ? `Quota ${benefit.quota}` : null,
              ].filter(Boolean);

              return (
                <Badge key={benefit.id} variant="light" color="blue">
                  {benefitName}
                  {summaryParts.length ? ` · ${summaryParts.join(' · ')}` : ''}
                </Badge>
              );
            })}
          </div>
        );
      },
      sortable: false,
    },
    {
      field: 'status',
      label: 'Status',
      renderCell: (row: any) => (
        <Badge color={row.status === 'active' ? 'green' : 'gray'}>{row.status}</Badge>
      ),
      sortable: true,
    },
    {
      field: 'actions',
      label: 'Actions',
      renderCell: (row: any) => (
        <div style={{ display: 'flex', gap: 8 }}>
          <ActionIcon onClick={() => router.visit(route('packages.edit', row.id))}>
            <IconPencil size={16} />
          </ActionIcon>
          <ActionIcon color="red" onClick={() => handleDelete(row)}>
            <IconTrash size={16} />
          </ActionIcon>
        </div>
      ),
      sortable: false,
    },
  ];

  return cols.map((c) => ({
    field: c.field,
    label: c.label,
    renderCell: c.renderCell,
    sortable: c.sortable,
  }));
};

export default TableData;
