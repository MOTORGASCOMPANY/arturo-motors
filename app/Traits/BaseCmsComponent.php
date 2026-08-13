<?php

namespace App\Traits;

trait BaseCmsComponent
{
    use InvalidatesLandingCache;

    public $successMessage = '';

    public $errorMessage = '';

    public $showForm = false;

    public $editingId = null;

    public $active = true;

    /**
     * Reset form fields to defaults.
     * Override in child component to add more fields.
     */
    public function resetForm(): void
    {
        $this->reset([
            'editingId',
            'showForm',
            'active',
            'successMessage',
            'errorMessage',
        ]);
        $this->active = true;
    }

    /**
     * Clear success message.
     */
    public function clearSuccessMessage(): void
    {
        $this->successMessage = '';
    }

    /**
     * Clear error message.
     */
    public function clearErrorMessage(): void
    {
        $this->errorMessage = '';
    }

    /**
     * Set success message and flash to session.
     */
    protected function setSuccess(string $message): void
    {
        $this->successMessage = $message;
        session()->flash('success', $message);
    }

    /**
     * Set error message and flash to session.
     */
    protected function setError(string $message): void
    {
        $this->errorMessage = $message;
        session()->flash('error', $message);
    }

    /**
     * Load items with consistent ordering.
     * Must be implemented by child component.
     */
    abstract public function loadItems(): void;

    /**
     * Create a new item.
     * Must be implemented by child component.
     */
    abstract public function create(): void;

    /**
     * Edit an existing item.
     * Must be implemented by child component.
     */
    abstract public function edit($id): void;

    /**
     * Save item (create or update).
     * Must be implemented by child component.
     */
    abstract public function save(): void;

    /**
     * Delete an item.
     * Must be implemented by child component.
     */
    abstract public function delete($id): void;

    /**
     * Toggle active status.
     * Must be implemented by child component.
     */
    abstract public function toggleActive($id): void;

    /**
     * Move item up in order.
     * Optional - implement if needed.
     */
    public function moveUp($id): void
    {
        // Override in child if needed
    }

    /**
     * Move item down in order.
     * Optional - implement if needed.
     */
    public function moveDown($id): void
    {
        // Override in child if needed
    }
}
