<?php
namespace App\Services;

use App\Models\Audience;
use App\Models\Conference;

class AudienceService
{
    public function create(
        Conference $conference,
        array $data,
        string $paymentMethod,
        ?string $paymentProof = null,
        string $paymentStatus = 'pending'
    ): Audience {

        $finalPaperPath = app(FileService::class)
            ->moveTempPaper($data['full_paper_path'] ?? null);

        return Audience::create([
            'public_id' => uniqid(),
            'conference_id' => $conference->id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'paper_title' => $data['paper_title'] ?? null,
            'institution' => $data['institution'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'country' => $data['country'],
            'presentation_type' => $data['presentation_type'],
            'paid_fee' => $data['paid_fee'],
            'payment_method' => $paymentMethod,
            'payment_proof_path' => $paymentProof,
            'full_paper_path' => $finalPaperPath,
            'payment_status' => $paymentStatus
        ]);
    }
}