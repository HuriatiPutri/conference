import { Config } from 'ziggy-js';

export interface User {
  id: number;
  name: string;
  first_name: string;
  last_name: string;
  email: string;
  owner: string;
  photo: string;
  deleted_at: string;
  account: Account;
}

export interface Account {
  id: number;
  name: string;
  users: User[];
  contacts: Contact[];
  organizations: Organization[];
}

export interface Contact {
  id: number;
  name: string;
  first_name: string;
  last_name: string;
  email: string;
  phone: string;
  address: string;
  city: string;
  region: string;
  country: string;
  postal_code: string;
  deleted_at: string;
  organization_id: number;
  organization: Organization;
}

export interface Organization {
  id: number;
  name: string;
  email: string;
  phone: string;
  address: string;
  city: string;
  region: string;
  country: string;
  postal_code: string;
  deleted_at: string;
  contacts: Contact[];
}

export interface Room {
  id: number;
  room_name: string;
  conference_id: number;
  created_at: string;
  updated_at: string;
  deleted_at: string;
}

export interface Conference {
  data: any;
  id: number;
  public_id: string;
  name: string;
  decscription?: string;
  initial: string;
  cover_poster_path: string;
  date: string;
  start_date: string;
  end_date: string;
  registration_start_date: string;
  registration_end_date: string;
  description?: string;
  venue?: string;
  city: string;
  country: string;
  year: string;
  online_fee: number;
  online_fee_usd: number;
  onsite_fee: number;
  onsite_fee_usd: number;
  participant_fee: number;
  participant_fee_usd: number;
  registration_fee: number;
  certificate_template_path: string;
  certificate_template_position: string;
  deleted_at: string;
  rooms: Room[];
}

export interface KeyNote {
  id: number;
  audience_id: number;
  first_name: string;
  last_name: string;
  feedback: string;
  created_at: string;
  updated_at: string;
  audience: {
    id: number;
    email: string;
    conference: Conference;
  };
}

export interface ParallelSession {
  id: number;
  audience_id: number;
  first_name: string;
  last_name: string;
  room_id: number;
  paper_title: string;
  created_at: string;
  updated_at: string;
  audience: {
    id: number;
    email: string;
    conference: Conference;
  };
  room: {
    id: number;
    room_name: string;
  } | null;
}

export interface Audiences {
  id: number;
  public_id: string;
  first_name: string;
  last_name: string;
  email: string;
  paper_title: string;
  institution: string;
  phone_number: string;
  country: string;
  presentation_type: string;
  paid_fee: number;
  payment_status: string;
  payment_method: string;
  participant_type: string;
  payment_proof_path: string;
  full_paper_path: string;
  conference_id: number;
  conference: Conference;
  key_notes: KeyNote[];
  parallel_sessions: ParallelSession[];
  created_at: string;
  updated_at: string;
  deleted_at: string;
}

export type PaginatedData<T> = {
  data: T[];
  links: {
    first: string;
    last: string;
    prev: string | null;
    next: string | null;
  };

  meta: {
    current_page: number;
    from: number;
    last_page: number;
    path: string;
    per_page: number;
    to: number;
    total: number;

    links: {
      url: null | string;
      label: string;
      active: boolean;
    }[];
  };
};

export type PageProps<
  T extends Record<string, unknown> = Record<string, unknown>
> = T & {
  auth: {
    user: User;
  };
  flash: {
    success: string | null;
    error: string | null;
  };
  ziggy: Config & { location: string };
};
