import React from "react";
import styles from "./styles.module.css";
import { Grid, Text, TextInput } from "@mantine/core";
import { usePage } from "@inertiajs/react";
import { Audiences } from "../../../types";
import MainLayout from "../../../Layout/MainLayout";

export default function AudienceShow() {
  const { audience } = usePage<{ audience: Audiences }>().props;

  console.log(audience);
  return (
    <div className={styles.card}>
      <Text c="#101010" fw={700}>
        Detail Data Audiens
      </Text>
      <Grid mt="md">
        <Grid.Col span={{ base: 12, sm: 2 }}>
          <Text c="#101010" fw={700} fz="lg">
            Data Audience
          </Text>
          <Text fz="sm" c="#606060">
            Berikut detail lengkap audiens.
          </Text>
        </Grid.Col>
        <Grid.Col span={{ base: 12, sm: 10 }}>
          <TextInput label="Nama Depan" value="John" readOnly />
        </Grid.Col>
      </Grid>

    </div>
  );
}

AudienceShow.layout = (page: React.ReactNode) => (
  <MainLayout title="Data Audience">{page}</MainLayout>
);
