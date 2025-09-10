<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\DetailTiket;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Tiket;
use App\Models\User;
use Livewire\Attributes\On;

class TasksKeluhan extends Component
{

    public $complaintId;
    public $tasks;
    public $newTask = '';
    public $taskId;
    public $name;
    public $tiket;
    public $phone;

    public function redirectToWhatsApp()
    {
       $complaint = Tiket::find($this->complaintId);

        if (!$complaint) {
            $this->alert('error', 'Error', ['text' => 'Data keluhan tidak ditemukan']);
            return;
        }

        $phoneNumber = Tiket::where('id', $this->complaintId)->value('phone_teknisi');

        $message = "Halo, ada keluhan dari pelanggan:\n\n"
                . "ID Keluhan: {$complaint->id}\n"
                . "Kategori: {$complaint->category}\n"
                . "Deskripsi: {$complaint->description}\n\n"
                . "Tolong listkan tugasan yang harus dilakukan untuk mengatasi hal tersebut.";

        $url = "https://wa.me/{$phoneNumber}?text=" . rawurlencode($message);

        return redirect()->away($url);
    }

    public function redirectToWhatsAppPelanggan()
    {
       $complaint = Tiket::find($this->complaintId);

        if (!$complaint) {
            $this->alert('error', 'Error', ['text' => 'Data keluhan tidak ditemukan']);
            return;
        }

        $phoneNumber = User::where('role', 'pelanggan')
            ->where('id', $complaint->user_id)
            ->value('phone');

        $url = "https://wa.me/{$phoneNumber}";

        return redirect()->away($url);
    }

    public function selesaikanKeluhan()
    {
        // 1. Verify all tasks are completed
        if (!$this->allTasksCompleted()) {
            LivewireAlert::title('Peringatan')
                ->text('Harap selesaikan semua tugas terlebih dahulu')
                ->warning()
                ->toast()
                ->position('top-end')
                ->timer(3000)
                ->show();
            return;
        }

        // 2. Show confirmation dialog
        LivewireAlert::title('Konfirmasi Penyelesaian')
            ->text('Apakah Anda yakin ingin menyelesaikan keluhan ini?')
            ->withConfirmButton('Ya, Selesaikan')
            ->withDenyButton('Tidak')
            ->onConfirm('prosesPenyelesaianKeluhan')
            ->show();
        }

        public function prosesPenyelesaianKeluhan()
        {

                Tiket::where('id', $this->complaintId)
                    ->update(['status' => 'selesai']);

                // 3. Log the completion
                Log::info('Keluhan diselesaikan', [
                    'complaint_id' => $this->complaintId,
                    'completed_at' => now(),
                    'tasks' => $this->tasks
                ]);

                // 4. Show success notification with redirect
                LivewireAlert::title('Berhasil!')
                    ->text('Keluhan berhasil diselesaikan')
                    ->success()
                    ->toast()
                    ->position('center')
                    ->timer(2000)
                    ->show();

                    $this->dispatch('redirect-after-delay',
                        url: route('tabel-keluhan.index')
                    );
        }

    #[On('redirect-after-delay')]
        public function returnToTableKeluhan()
    {
        sleep(2); // Delay for 3 seconds before redirecting
        return redirect()->route('tabel-keluhan.index');
    }


    public function mount($id)
    {
        $this->complaintId = $id;
        $detail = DetailTiket::where('tiket_id', $this->complaintId)->first();
        $this->tasks = $detail ? (json_decode($detail->tasks, true) ?? []) : [];
        $this->taskId = $detail->id ?? null;
        $tiket = Tiket::where('id', $this->complaintId)->first();
        $this->tiket = $tiket;
        $this->name = $tiket->nama_teknisi_menangani;
        $this->phone = $tiket->phone_teknisi;
    }

    public function render()
    {
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

    public function updateEdit() {
         LivewireAlert::title("Perhatian")
            ->text("Apakah Anda yakin ingin menambah penanggung jawab teknisi di keluhan ini ?")
            ->question()
            ->asConfirm()
            ->onConfirm("updateTeknisi")
            ->show();
    }

    public function updateTeknisi()
    {
        Tiket::where('id', $this->complaintId)->update([
            'nama_teknisi_menangani' => $this->name,
            'phone_teknisi' => $this->phone,
        ]);

        Log::info("testing", [$this->name, $this->phone, $this->complaintId]);
        LivewireAlert::title("Data Berhasil diperbarui")
            ->success()
            ->withConfirmButton('Ok!')
            ->onConfirm("refreshPage")
            ->show();
    }

    public function refreshPage() {
        return redirect(request()->header('Referer'));
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
