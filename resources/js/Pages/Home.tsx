import { Column } from 'primereact/column';
import { TreeTable } from 'primereact/treetable';
import React, { useState } from 'react';
import MainLayout from '../Layout/MainLayout';

function Home({ name }) {
  const [nodes, setNodes] = useState<any[]>([
    {
      key: '0',
      label: 'Documents',
      data: 'Documents Folder',
      icon: 'pi pi-fw pi-inbox',
      children: [
        {
          key: '0-0',
          label: 'Work',
          data: 'Work Folder',
          icon: 'pi pi-fw pi-cog',
          children: [
            {
              key: '0-0-0',
              label: 'Expenses.doc',
              icon: 'pi pi-fw pi-file',
              data: 'Expenses Document',
            },
            {
              key: '0-0-1',
              label: 'Resume.doc',
              icon: 'pi pi-fw pi-file',
              data: 'Resume Document',
            },
          ],
        },
        {
          key: '0-1',
          label: 'Home',
          data: 'Home Folder',
          icon: 'pi pi-fw pi-home',
          children: [
            {
              key: '0-1-0',
              label: 'Invoices.txt',
              icon: 'pi pi-fw pi-file',
              data: 'Invoices for this month',
            },
          ],
        },
      ],
    },
  ]);
  return (
    <div className="card">
      <TreeTable value={nodes} tableStyle={{ minWidth: '50rem' }}>
        <Column field="name" header="Name" expander></Column>
        <Column field="size" header="Size"></Column>
        <Column field="type" header="Type"></Column>
      </TreeTable>
    </div>
  );
}

Home.layout = (page: React.ReactNode) => <MainLayout title="Dashboard">{page}</MainLayout>;

export default Home;
