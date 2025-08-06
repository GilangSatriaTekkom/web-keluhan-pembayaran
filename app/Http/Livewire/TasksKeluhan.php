<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\DetailTiket;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Tiket;

class TasksKeluhan extends Component
{
    public $complaintId;
    public $tasks;
    public $newTask = '';
    public $taskId;

    public function selesaikanKeluhan()
    {
        // 1. Verify all tasks are completed
        if (!$this->allTasksCompleted()) {
            $this->alert('warning', 'Peringatan', [
                'text' => 'Harap selesaikan semua tugas terlebih dahulu',
                'timer' => 3000,
                'toast' => true,
                'position' => 'top-end'
            ]);
            return;
        }

        // 2. Update complaint status
        try {
            Tiket::where('id', $this->complaintId)
                ->update(['status' => 'selesai']);

            // 3. Log the completion
            Log::info('Keluhan diselesaikan', [
                'complaint_id' => $this->complaintId,
                'completed_at' => now(),
                'tasks' => $this->tasks
            ]);

            // 4. Show success notification
            $this->alert('success', 'Berhasil!', [
                'text' => 'Keluhan berhasil diselesaikan',
                'timer' => 3000,
                'toast' => true,
                'position' => 'top-end',
                'showConfirmButton' => false
            ]);

            // 5. Optional: Redirect after completion
            return redirect()->route('tabel-keluhan.index');

        } catch (\Exception $e) {
            Log::error('Gagal menyelesaikan keluhan', [
                'error' => $e->getMessage(),
                'complaint_id' => $this->complaintId
            ]);

            $this->alert('error', 'Gagal!', [
                'text' => 'Gagal menyelesaikan keluhan: ' . $e->getMessage(),
                'timer' => 5000,
                'toast' => true,
                'position' => 'top-end'
            ]);
        }
    }


    public function mount($id)
    {
        $this->complaintId = $id;
        $detail = DetailTiket::where('tiket_id', $this->complaintId)->first();
        $this->tasks = json_decode($detail->tasks, true) ?? [];
        $this->taskId = $detail->id ?? null;
    }

    public function render()
    {
        Log::debug("message", [
            'complaint_id' => $this->complaintId,
            'tasks' => $this->tasks
        ]);
        return view('livewire.tasks-keluhan'
        );
    }

    // In your Livewire component
    public function updateTaskStatus($index)
    {
        // Toggle task completion status
        $newStatus = $this->tasks[$index]['completed'];
        $this->saveTasks(); // Save to database

    }

    public function allTasksCompleted()
    {
        if (empty($this->tasks)) {
            return false;
        }

        // Check if every task is completed
        foreach ($this->tasks as $task) {
            if (!$task['completed']) {
                return false;
            }
        }
        return true;
    }

    // Add new task
    public function addTask()
    {
        $this->validate([
            'newTask' => 'required|string|max:255'
        ]);

        $this->tasks[] = [
            'task' => $this->newTask,
            'completed' => false
        ];

        $this->saveTasks();
        $this->newTask = '';
    }

    // Remove task
    public function removeTask($index)
    {
        unset($this->tasks[$index]);
        $this->tasks = array_values($this->tasks); // Reindex array
        $this->saveTasks();
    }

    // Save all tasks to database
    protected function saveTasks()
    {
        DetailTiket::updateOrCreate(
            ['tiket_id' => $this->complaintId],
            ['tasks' => json_encode($this->tasks)]
        );
    }
}
