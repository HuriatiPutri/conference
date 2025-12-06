# Conference Management Application - Documentation

## Overview
A web-based conference management system for handling conference registration, audience management, LoA (Letter of Approval) generation, payment integration, and admin features. Built with Laravel (backend) and React (frontend, Mantine UI, PrimeReact, Inertia.js).

---

## Main Features
- Conference CRUD (admin)
- Audience registration & management
- Keynote & parallel session management
- Payment integration (PayPal, Bank Transfer)
- Letters of Approval (LoA) management
- LoA Volume management (admin)
- Admin dashboard & statistics
- Search, filter, pagination on all major lists
- Role-based access (admin, user)

---

## User Flow Chart

```mermaid
flowchart TD
    Start([Start])
    HomePage([Landing Page])
    Register([User Registration])
    Login([User Login])
    Dashboard([User/Admin Dashboard])
    ConferenceList([View Conferences])
    ConferenceDetail([Conference Detail])
    AudienceReg([Audience Registration])
    Payment([Payment Process])
    PaymentSuccess([Payment Success])
    AdminPanel([Admin Panel])
    ManageAudience([Manage Audiences])
    ManageLoA([Manage LoA & Volumes])
    GenerateLoA([Generate/Download LoA])
    Logout([Logout])
    End([End])

    Start --> HomePage
    HomePage -->|Register| Register
    HomePage -->|Login| Login
    Register --> Dashboard
    Login --> Dashboard
    Dashboard --> ConferenceList
    ConferenceList --> ConferenceDetail
    ConferenceDetail --> AudienceReg
    AudienceReg --> Payment
    Payment --> PaymentSuccess
    PaymentSuccess --> Dashboard
    Dashboard -->|Admin| AdminPanel
    AdminPanel --> ManageAudience
    AdminPanel --> ManageLoA
    ManageLoA --> GenerateLoA
    Dashboard --> Logout
    AdminPanel --> Logout
    Logout --> End
```

---

## Sequence Diagram: LoA Generation (Admin)

```mermaid
sequenceDiagram
    participant Admin
    participant WebApp
    participant Backend
    participant DB
    participant PDFGen

    Admin->>WebApp: Open Letters of Approval page
    WebApp->>Backend: GET /letters-of-approval
    Backend->>DB: Query audiences with paid status & papers
    DB-->>Backend: Audiences data (with LoA Volume)
    Backend-->>WebApp: Render LoA list
    Admin->>WebApp: Click 'Download Form' for an audience
    WebApp->>Backend: GET /letters-of-approval/{audience}/download-form
    Backend->>DB: Get audience, LoA volumes
    DB-->>Backend: Audience + LoA Volumes
    Backend-->>WebApp: Render form
    Admin->>WebApp: Submit LoA info (authors, volume)
    WebApp->>Backend: POST /letters-of-approval/{audience}/update-info
    Backend->>DB: Update audience with LoA info
    DB-->>Backend: OK
    Backend-->>WebApp: Success response
    Admin->>WebApp: Click 'Download PDF'
    WebApp->>Backend: GET /letters-of-approval/{audience}/download
    Backend->>DB: Get audience, LoA info
    Backend->>PDFGen: Generate PDF
    PDFGen-->>Backend: PDF file
    Backend-->>WebApp: Return PDF
    Admin->>WebApp: View/Download LoA PDF
```

---

## How It Works (Summary)

- **User** dapat mendaftar, login, melihat daftar konferensi, dan melakukan registrasi sebagai audience.
- **Pembayaran** dilakukan via PayPal atau transfer bank, diverifikasi oleh admin.
- **Admin** dapat mengelola data conference, audience, keynote, parallel session, dan LoA Volume.
- **LoA** dapat di-generate untuk audience yang sudah membayar dan mengisi data paper.
- **LoA Volume** dikelola terpisah, audience dapat di-assign ke volume tertentu.
- **Semua list** (audience, LoA, volume, dsb) mendukung pencarian, filter, dan pagination.
- **PDF LoA** dihasilkan secara otomatis dan dapat diunduh oleh admin.

---

## Tech Stack
- **Backend:** Laravel (PHP)
- **Frontend:** React (TypeScript), Mantine UI, PrimeReact, Inertia.js
- **Database:** MySQL/PostgreSQL
- **PDF:** DomPDF
- **Payment:** PayPal API

---

## For Developers
- Ikuti struktur folder dan pola coding yang sudah ada.
- Semua fitur baru harus konsisten dengan UI/UX dan arsitektur project.
- Lihat `.github/agents/instruction.md` untuk instruksi pembuatan fitur baru.
