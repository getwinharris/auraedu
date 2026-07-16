---
title: Institute Content Model
description: Verified content model for Aura Medical Institute of Electropathy and Hospital — B.E.M.S., admissions, faculty, hospital, facilities, hostel, placements, approvals, gallery, news, contact.
category: docs
---

# Institute Content Model

The institute content model defines the structured content Aura Medical publishes. Each area maps to a
collection in `storage/schema/collections.php` (or a `content/` Markdown page) and a view. **All factual claims
must be verified against Aura's Instagram (@aurahealthedu) and supplied documents before publishing — see
`docs/route-audit.md` and `docs/competitor-review.md`.**

## 1. B.E.M.S. (programme)
- Full name: Bachelor of Electro-Medical Sciences (verify exact Aura wording).
- Duration: verify (electropathy B.E.M.S. commonly 4 years + 6-month internship — confirm for Aura).
- Eligibility: 10+2 Science with Biology (verify); **No NEET**, **No age bar** (verify).
- Syllabus modules: Anatomy, Physiology, Pharmacy, Electropathy Materia Medica, Acupuncture, Pathology,
  Gynecology/Obstetrics, Pediatrics, Surgery, Practice of Medicine (confirm Aura's actual module list).
- Outcome: electropathy practitioner / allied-health pathway (disclose recognition status honestly).

## 2. Admissions
- Process: enquire → documents → seat confirmation.
- Documents: 10th/12th certificates, TC, conduct, residence, community, photos (confirm Aura's list).
- Fees: verify exact Aura fee structure (do NOT copy competitor ₹60,000 figure).
- Intake timing: verify.

## 3. Course structure
- Year-wise breakdown matching the verified syllabus.
- Internship / hospital rotation description.

## 4. Faculty
- Name, qualification, speciality, photo, bio, languages.
- Repurposes the legacy `astrologer` collection shape (`card-therapist` in Design.md).

## 5. Hospital
- Departments / care offered, facilities, timings.
- Repurposes legacy `temples` collection shape.

## 6. Facilities
- Campus, labs, library, clinic, therapy rooms, acupuncture suite.

## 7. Hostel
- Accommodation, rules, fees, amenities.

## 8. Placements
- Career paths: electropathy practitioner, acupuncturist, allied-health, wellness centres, independent practice.
- Honest scope note (private/wellness sector; not NMC/MCI allopathic registration).

## 9. Approvals & disclosures
- Affiliating/approving body name + number (verify exact wording).
- Recognition status disclosure: electropathy is alternative/complementary medicine; not NMC/MCI-recognised as
  allopathic. State this plainly — do not overclaim.
- Statutory disclaimers, refund policy pointer.

## 10. Gallery
- Campus, hospital, events, lab, hostel imagery (16:9, one intentional image per post per Design.md).

## 11. News
- Announcements, admissions open, events — Markdown posts in `content/blog/posts/` (category `news`).

## 12. Contact
- Address: 10/6A, VKV Kumaraguru Nagar, Saravanampatti, Coimbatore, Tamil Nadu 641035.
- Phone: +91 97902 21065.
- Email: verify (legacy `support@auraedu.co.in` appears inconsistent — confirm).
- Instagram: @aurahealthedu.
- Map embed, hours.

## Schema mapping (proposed collections)
| Content area | Collection / source | View |
|-------------|-------------------|------|
| B.E.M.S. + admissions + course | `content/` Markdown or `programmes` collection | `/education` |
| Faculty | `faculty` collection (repurpose `astrologers`) | `/consult` (therapies) |
| Hospital | `hospital` collection (repurpose `temples`) | `/temples` |
| Facilities/Hostel/Placements/Approvals | `content/` Markdown pages | `/about`, `/education` |
| Gallery | media library | gallery section |
| News | `content/blog/posts/` (news category) | `/blog` |
| Contact | `contact` config / `content/` | `/contact` |

## Verification gate
Before any of the above is published to `auraedu.co.in`:
- [ ] Cross-checked against @aurahealthedu Instagram.
- [ ] Cross-checked against supplied Aura documents.
- [ ] Approval/disclosure wording approved by Aura.
- [ ] No copied competitor claims, fees, or imagery.
