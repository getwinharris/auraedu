---
type: doc
title: Admissions & Appointment Module
description: Owns admission enquiries and hospital appointment requests.
category: module
---
# Admissions & Appointment Module

Owns admission enquiries and hospital appointment requests, repurposed from the legacy consultation/booking module.

Main files: `ConsultationController.php` (initiate), `AppointmentService.php`, `views/public/consult.php` (rendered as Therapies/appointments), `views/account/bookings.php`.

Key checks: customers request a date and time for a hospital/therapy appointment, the central admin receives the full request through SMTP, appointment status is admin-managed, and ended sessions can collect five-star reviews. Admission enquiries route through the contact form.
