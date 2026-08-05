<?php
namespace App\Services;

final class CourseService {
    public function all(): array {
        return [
            'bems' => [
                'slug' => 'bems', 'title' => 'B.E.M.S.', 'short' => 'Bachelor of Electro-Medical Sciences',
                'lede' => 'A practice-led electropathy medical education programme with hospital training and allied-health practice. Admissions open — no NEET required, no upper age bar.',
                'duration' => '4½ years (including internship)',
                'eligibility' => '10+2 with Biology (verify exact requirement with admissions). No NEET, no age bar.',
                'highlights' => ['Anatomy, Physiology, Pharmacy', 'Electropathy & Electrotherapy', 'Acupuncture, Gynecology, Pediatrics', 'Compulsory hospital internship'],
            ],
            'mdeh' => [
                'slug' => 'mdeh', 'title' => 'M.D.E.H.', 'short' => 'Doctor of Electro-Homeopathy',
                'lede' => 'Postgraduate electropathy training building on foundational medical education for advanced clinical practice.',
                'duration' => 'Confirm with admissions desk',
                'eligibility' => 'B.E.M.S. or equivalent (verify with admissions).',
                'highlights' => ['Advanced electropathy practice', 'Clinical specialisation', 'Research & case methodology'],
            ],
            'dacu' => [
                'slug' => 'dacu', 'title' => 'D.Acu', 'short' => 'Diploma in Acupuncture',
                'lede' => 'A focused acupuncture programme covering needling theory, meridian systems, and supervised clinical practice.',
                'duration' => 'Confirm with admissions desk',
                'eligibility' => '10+2 (verify with admissions).',
                'highlights' => ['Meridian & point theory', 'Needling technique', 'Supervised clinic practice'],
            ],
            'macu' => [
                'slug' => 'macu', 'title' => 'M.Acu', 'short' => 'Master in Acupuncture',
                'lede' => 'Advanced acupuncture training for practitioners seeking deeper clinical and theoretical mastery.',
                'duration' => 'Confirm with admissions desk',
                'eligibility' => 'D.Acu or equivalent (verify with admissions).',
                'highlights' => ['Advanced meridian diagnosis', 'Integrated therapy planning', 'Clinical research'],
            ],
            'dhm' => [
                'slug' => 'dhm', 'title' => 'D.H.M. & C.T.', 'short' => 'Diploma in Hotel Management & Catering Technology',
                'lede' => 'A hospitality and catering technology programme with kitchen, front-office, and service training.',
                'duration' => 'Confirm with admissions desk',
                'eligibility' => '10+2 (verify with admissions).',
                'highlights' => ['Food production & kitchen ops', 'Front office & housekeeping', 'Catering technology'],
            ],
        ];
    }

    public function find(string $slug): ?array {
        return $this->all()[$slug] ?? null;
    }
}