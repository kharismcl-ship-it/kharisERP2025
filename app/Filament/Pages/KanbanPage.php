<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;

/**
 * Abstract base class for Filament v4 Kanban boards.
 *
 * Built on native Livewire 3 + Alpine.js drag-and-drop.
 *
 * Subclasses must implement:
 *   - getKanbanStatuses(): array   — ordered list of columns
 *   - getKanbanRecords(string $status): Collection
 *   - onCardMoved(int|string $id, string $newStatus): void
 *   - getCardView(): string        — blade partial for each card
 *
 * Each status entry MUST have: 'key', 'label', 'border_class', 'dot_color'
 */
abstract class KanbanPage extends Page
{
    protected string $view = 'kanban.board';

    public ?string $search = null;

    // ── Abstract interface ────────────────────────────────────────────────────

    /**
     * Return the ordered list of status columns.
     *
     * Required keys per entry:
     *   'key'          => string   (DB value, e.g. 'applied')
     *   'label'        => string   (column heading, e.g. 'Applied')
     *   'border_class' => string   (Tailwind top-border, e.g. 'border-blue-400')
     *   'dot_color'    => string   (small dot colour, e.g. 'bg-blue-400')
     */
    abstract public function getKanbanStatuses(): array;

    /**
     * Return records for one status column.
     * $this->search is available for filtering.
     */
    abstract public function getKanbanRecords(string $status): Collection;

    /**
     * Handle a card being dropped onto a different column.
     */
    abstract protected function onCardMoved(int|string $recordId, string $newStatus): void;

    /**
     * Blade view path for rendering each card.
     * Receives: $record (Model), $status (array from getKanbanStatuses).
     */
    abstract public function getCardView(): string;

    // ── Optional hooks ────────────────────────────────────────────────────────

    /**
     * Return ['view' => string, 'props' => array] to render a filter bar
     * above the board (e.g. a project selector for Construction kanbans).
     * Return null to hide the filter bar.
     */
    public function getKanbanFilterBarView(): ?array
    {
        return null;
    }

    // ── Event handler ─────────────────────────────────────────────────────────

    #[On('kanban-card-moved')]
    public function handleCardMoved(int $recordId, string $newStatus): void
    {
        $this->onCardMoved($recordId, $newStatus);
    }
}
