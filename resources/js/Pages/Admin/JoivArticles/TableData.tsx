import React from "react"
import { Badge, Button, Flex, Stack, Text } from "@mantine/core"
import { JoivRegistration } from "../../../types"
import { PAYMENT_METHOD } from "../../../Constants";
import { getStatusBadge } from "../../../Components/BadgeStatus";
import { formatCurrency } from "../../../utils";
import { ActionButtonExt } from "../Conferences/ExtendComponent";

type DataProps = {
  handleUpdateStatus: (registration: JoivRegistration) => void;
  handleView: (registration: JoivRegistration) => void;
}

export const TableData = ({ handleUpdateStatus, handleView }: DataProps) => [
  {
    label: 'No',
    name: 'serial_number',
    renderCell: (_: JoivRegistration, { rowIndex }: { rowIndex: number }) => rowIndex + 1,
  },
  {
    label: 'First Name',
    name: 'first_name',
    sortable: true,
  },
  {
    label: 'Last Name',
    name: 'last_name',
    sortable: true,
  },
  {
    label: 'Email',
    name: 'email_address',
    sortable: true,
  },
  {
    label: 'Institution',
    name: 'institution',
    sortable: true,
  },
  {
    label: 'Country',
    name: 'country',
    sortable: true,
  },
  {
    label: 'Paper ID',
    name: 'paper_id',
    sortable: true,
  },
  {
    label: 'Paper Title',
    name: 'paper_title',
    renderCell: (row: JoivRegistration) => (
      <Stack w={250}>
        <Text size='sm' style={{ textWrap: 'wrap' }}>{row.paper_title}</Text>
        {row.full_paper_path && (
          <Button
            color="blue"
            size="xs"
            variant="light"
            leftSection={<i className="pi pi-download" />}
            onClick={() => window.open(`/storage/${row.full_paper_path}`, '_blank')}
          >
            Download Paper
          </Button>
        )}
      </Stack>
    ),
  },
  {
    label: 'LOA Volume',
    name: 'loa_volume',
    renderCell: (row: JoivRegistration) => (
      <Stack>
        {row.loa_volume ? (
          <>
            <Badge color="blue" variant="outline">
              {row.loa_volume.volume}
            </Badge>
            {row.loa_authors && row.payment_status === 'paid' && (
              <Button
                color="green"
                size="xs"
                variant="light"
                leftSection={<i className="pi pi-download" />}
                onClick={() => window.open(`/joiv-articles/${row.id}/download-loa`, '_blank')}
              >
                Download LoA
              </Button>
            )}
          </>
        ) : (
          <Text size="sm" c="dimmed" fs="italic">Not assigned</Text>
        )}
      </Stack>
    ),
  },
  {
    label: 'Payment Method',
    name: 'payment_method',
    renderCell: (row: JoivRegistration) => {
      const isTransferWithProof = row.payment_method === 'transfer_bank' && row.payment_proof_path;

      return (
        <Stack>
          <Text size='sm'>{PAYMENT_METHOD[row.payment_method as keyof typeof PAYMENT_METHOD]}</Text>
          {isTransferWithProof && (
            <Button
              color="blue"
              size="xs"
              variant="light"
              leftSection={<i className="pi pi-download" />}
              onClick={() => window.open(`/storage/${row.payment_proof_path}`, '_blank')}
            >
              Download Proof
            </Button>
          )}
        </Stack>
      );
    }
  },
  {
    label: 'Payment Status',
    name: 'payment_status',
    renderCell: (row: JoivRegistration) => (
      <Stack>
        {getStatusBadge(row.payment_status)}
        {row.payment_status === 'paid' && (
          <Button
            component="a"
            size="xs"
            variant="light"
            leftSection={<i className="pi pi-download" />}
            href={route('joiv-articles.downloadReceipt', row.id)}
            target="_blank"
          >
            Download Receipt
          </Button>
        )}
      </Stack>
    )
      
  },
  {
    label: 'paid_fee',
    name: 'paid_fee',
    renderCell: (row: JoivRegistration) => formatCurrency(row.paid_fee, row.country === 'ID' ? 'idr' : 'usd'),
  },
  {
    label: 'Actions',
    name: 'actions',
    renderCell: (row: JoivRegistration) => (
      <Flex gap="xs">
        <ActionButtonExt
          color="green"
          handleClick={() => handleView(row)}
          icon="pi pi-fw pi-eye"
        />
        {row.payment_status === 'paid' && (
          <ActionButtonExt
            color="violet"
            handleClick={() => window.location.href = `/joiv-articles/${row.id}/assign-volume`}
            icon="pi pi-fw pi-book"
            title="Assign Volume"
          />
        )}
        {row.payment_method === 'transfer_bank' && (
          <ActionButtonExt
            color="blue"
            handleClick={() => handleUpdateStatus(row)}
            icon="pi pi-fw pi-credit-card"
          />
        )}
      </Flex>
    )
  }
]