<?php

namespace Purdia\Shared\Contracts\Document;

use Purdia\Document\Domain\Enums\DocumentStatus;

interface DocumentEngine
{
    /**
     * Generate the next document number for a given type.
     *
     * @param string $type Document type (e.g., 'INV', 'PO', 'SO', 'GR')
     * @param string $tenantId
     * @param string|null $branchId
     * @return string Generated number (e.g., 'INV-BDG-202607-0001')
     */
    public function generate(string $type, string $tenantId, ?string $branchId = null): string;

    /**
     * Transition a document to a new status.
     * Returns false if transition is not allowed.
     */
    public function transition(string $documentType, string $documentId, DocumentStatus $newStatus): bool;

    /**
     * Check if a transition is allowed from current status to target.
     */
    public function canTransition(DocumentStatus $from, DocumentStatus $to): bool;

    /**
     * Create a revision snapshot for a document.
     */
    public function createRevision(string $documentType, string $documentId, array $data, ?string $reason = null): int;

    /**
     * Get the current revision number for a document.
     */
    public function currentRevision(string $documentType, string $documentId): int;
}
