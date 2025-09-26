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
use App\Mail\TicketNotificationMail;
use App\Mail\TicketResolvedMail;
use Illuminate\Support\Facades\Mail;

class TasksKeluhan extends Component
{

    public $complaintId;
    public $tasks;
    public $newTask = '';
    public $taskId;
    public $name;
    public $tiket;
    public $phone;
    public $listTeknisi = [];
    public $allTeknisi = [];
    public $selectedTeknisi = [];
    public $CS;

    public function redirectToWhatsApp()
    {
        $complaint = Tiket::with(['teknisis', 'cs'])->find($this->complaintId);

        if (!$complaint) {
            $this->alert('error', 'Error', ['text' => 'Data keluhan tidak ditemukan']);
            return;
        }

        $this->listTeknisi = $complaint->teknisis()
            ->wherePivot('tiket_id', $this->complaintId)
            ->get();

        $message = "Halo, ada keluhan dari pelanggan:\n\n"
            . "ID Keluhan: {$complaint->id}\n"
            . "Kategori: {$complaint->category}\n"
            . "Deskripsi: {$complaint->description}\n\n"
            . "Tolong listkan tugasan yang harus dilakukan untuk mengatasi hal tersebut.";

        if (Auth::check() && Auth::user()->role === 'admin') {
            $teknisi = $complaint->teknisis;

            if ($teknisi->isEmpty()) {
                $this->alert('error', 'Error', ['text' => 'Tidak ada teknisi pada tiket ini']);
                return;
            }

            if ($this->listTeknisi->count() === 1) {
                $phoneNumber = $teknisi->first()->phone;
            } else {
                $this->dispatch('show-teknisi-modal');
                return;
            }
        } else {
            $phoneNumber = $complaint->cs->phone ?? null;
        }

        if (!$phoneNumber) {
            $this->alert('error', 'Error', ['text' => 'Nomor WhatsApp tidak ditemukan']);
            return;
        }

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

        $tiket = Tiket::with("user")->find($this->complaintId);

        Log::info('Keluhan diselesaikan', [
            'complaint_id' => $this->complaintId,
            'completed_at' => now(),
            'tasks' => $this->tasks
        ]);

        $customer = $tiket->user; // pastikan relasi customer ada

        // Update status tiket
        $tiket->status = 'selesai';
        $tiket->save();

        // Kirim email ke pelanggan
        Mail::to($customer->email)->send(
            new TicketResolvedMail(
                $tiket->id,
                $tiket->category,
                $tiket->description,
                $customer->name
            )
        );

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
        $this->allTeknisi = User::where('role', 'teknisi')
            ->whereDoesntHave('tiketTeknisi', function ($query) {
                $query->where('status', 'proses');
            })
            ->get();
        $this->CS = Tiket::where('id', $this->complaintId)->pluck('cs_menangani');

        $this->selectedTeknisi = $tiket->teknisi_menangani
            ? explode(',', $tiket->teknisi_menangani)
            : [];

        if ($this->listTeknisi === null || empty($this->listTeknisi)) {
            $this->listTeknisi = Tiket::with('teknisis')
                ->find($id)
                ?->teknisis ?? collect();
        }
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
        $this->saveTasks();

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
        $tiket = Tiket::findOrFail($this->complaintId);

        // Sinkronkan teknisi yang dipilih ke pivot
        $tiket->teknisis()->attach($this->selectedTeknisi);

        Log::info("Update teknisi", [
            'selected' => $this->selectedTeknisi,
            'tiket_id' => $this->complaintId
        ]);

        foreach ($this->selectedTeknisi as $teknisiId) {
            $teknisi = User::find($teknisiId);

            if ($teknisi && $teknisi->email) {
                $subject = "Tugas Baru: Tiket #{$tiket->id}";

                Mail::to($teknisi->email)->send(
                    new TicketNotificationMail(
                        $subject,                           // subjectText
                        "Anda mendapatkan tugas tiket baru", // messageText
                        $tiket->id,                          // ticketId
                        $tiket->category,                    // ticketCategory
                        $tiket->description,                 // ticketDescription
                        $teknisi->name                       // teknisiName
                    )
                );
            }
        }

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



    public function markAsCompleted($tiketId)
    {
        $tiket = Tiket::findOrFail($tiketId);
        $tiket->status = 'selesai';
        $tiket->save();

        $user = $tiket->user;
        if ($user && $user->email) {
            $subject = "Tiket #{$tiket->id} Selesai";
            $message = "Halo {$user->name},\n\n"
                . "Tiket Anda dengan ID #{$tiket->id} sudah berhasil diselesaikan.\n"
                . "Terima kasih telah menggunakan layanan kami 🙏.";

            Mail::to($user->email)->send(new TicketNotificationMail($subject, $message));
        }
    }


}
