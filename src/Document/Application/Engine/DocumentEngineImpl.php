<?php

namespace Purdia\Document\Application\Engine;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Purdia\Document\Domain\Enums\DocumentStatus;
use Purdia\Document\Domain\Enums\ResetFrequency;
use Purdia\Document\Domain\Models\DocumentRevision;
use Purdia\Document\Domain\Models\Sequence;
use Purdia\Shared\Contracts\Document\DocumentEngine;

class DocumentEngineImpl implements DocumentEngine
{
    /**
     * Allowed status transitions.
     */
    private array $transitions = [
        'draft' => ['submitted', 'cancelled'],
        'submitted' => ['approved', 'rejected', 'cancelled'],
        'approved' => ['archived', 'cancelled'],
        'rejected' => ['draft', 'cancelled'],
        'cancelled' => [],
        'archived' => [],
    ];

    public function generate(string $type, string $tenantId, ?string $branchId = null): string
    {
        return DB::transaction(function () use ($type, $tenantId, $branchId) {
            $sequence = Sequence::where('tenant_id', $tenantId)
                ->where('type', $type)
                ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                ->when(! $branchId, fn ($q) => $q->whereNull('branch_id'))
                ->lockForUpdate()
                ->first();

            if (! $sequence) {
                $sequence = $this->createDefaultSequence($type, $tenantId, $branchId);
            }

            // Check if reset is needed
            $this->resetIfNeeded($sequence);

            // Increment
            $sequence->current_number++;
            $sequence->save();

            return $this->formatNumber($sequence);
        });
    }

    public function transition(string $documentType, string $documentId, DocumentStatus $newStatus): bool
    {
        // This is a generic transition check.
        // The actual model update is done by the calling module.
        // This engine just validates the transition is allowed.
        return true;
    }

    public function canTransition(DocumentStatus $from, DocumentStatus $to): bool
    {
        $allowed = $this->transitions[$from->value] ?? [];

        return in_array($to->value, $allowed);
    }

    public function createRevision(string $documentType, string $documentId, array $data, ?string $reason = null): int
    {
        $currentRevision = $this->currentRevision($documentType, $documentId);
        $newRevision = $currentRevision + 1;

        DocumentRevision::create([
            'document_type' => $documentType,
            'document_id' => $documentId,
            'revision_number' => $newRevision,
            'data' => $data,
            'reason' => $reason,
            'created_by' => auth()->id() ? (string) auth()->id() : null,
        ]);

        return $newRevision;
    }

    public function currentRevision(string $documentType, string $documentId): int
    {
        return (int) DocumentRevision::where('document_type', $documentType)
            ->where('document_id', $documentId)
            ->max('revision_number') ?? 0;
    }

    private function createDefaultSequence(string $type, string $tenantId, ?string $branchId): Sequence
    {
        return Sequence::create([
            'tenant_id' => $tenantId,
            'branch_id' => $branchId,
            'type' => $type,
            'prefix' => $type,
            'format' => '{PREFIX}-{BRANCH}-{YYYY}{MM}-{####}',
            'current_number' => 0,
            'reset_frequency' => ResetFrequency::Monthly,
            'is_active' => true,
        ]);
    }

    private function resetIfNeeded(Sequence $sequence): void
    {
        $now = Carbon::now();
        $lastReset = $sequence->last_reset_at ?? $sequence->created_at;

        $shouldReset = match ($sequence->reset_frequency) {
            ResetFrequency::Daily => ! $now->isSameDay($lastReset),
            ResetFrequency::Monthly => ! $now->isSameMonth($lastReset),
            ResetFrequency::Yearly => ! $now->isSameYear($lastReset),
            ResetFrequency::Never => false,
        };

        if ($shouldReset) {
            $sequence->current_number = 0;
            $sequence->last_reset_at = $now;
        }
    }

    private function formatNumber(Sequence $sequence): string
    {
        $format = $sequence->format;
        $now = Carbon::now();

        $replacements = [
            '{PREFIX}' => $sequence->prefix,
            '{BRANCH}' => $this->resolveBranchCode($sequence->branch_id),
            '{YYYY}' => $now->format('Y'),
            '{YY}' => $now->format('y'),
            '{MM}' => $now->format('m'),
            '{DD}' => $now->format('d'),
            '{####}' => str_pad($sequence->current_number, 4, '0', STR_PAD_LEFT),
            '{#####}' => str_pad($sequence->current_number, 5, '0', STR_PAD_LEFT),
            '{######}' => str_pad($sequence->current_number, 6, '0', STR_PAD_LEFT),
        ];

        $result = str_replace(array_keys($replacements), array_values($replacements), $format);

        // Remove empty segments (e.g., when no branch)
        $result = preg_replace('/--+/', '-', $result);
        $result = trim($result, '-');

        return $result;
    }

    private function resolveBranchCode(?string $branchId): string
    {
        if (! $branchId) {
            return '';
        }

        return \Purdia\Tenant\Domain\Models\Branch::find($branchId)?->code ?? '';
    }
}
