<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Tiket;
use App\Models\DetailTiket;
use App\Models\User;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Support\Facades\Auth;

class LihatKeluhan extends Component
{
    public $complaintId;
    public $complaint;
    public $tasks = [];
    public $showTaskModal = false;
    public $selectedComplaintId;
    public $authUserId;
    public $complaintTeknisi;

    protected $listeners = ['showComplaintDetail' => 'loadComplaint'];

    public function mount($id = null)
    {
        if ($id) {
            $this->loadComplaint($id);
        }
        $this->authUserId = Auth::id();
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

    public function alertPopup($title, $text, $confirm)
    {

        LivewireAlert::title($title)
            ->text($text)
            ->question()
            ->asConfirm()
            ->onConfirm($confirm)
            ->show();
    }

    public function prosesKeluhan()
    {
        Tiket::where('id', $this->complaintId)
            ->update([
                'status' => 'proses',
                'cs_menangani' => $this->authUserId,
        ]);
       return redirect()->route('tasks.keluhan', ['id' => $this->complaintId]);
    }

    public function openTaskModal()
    {
        $user =  User::where('id', $this->authUserId)->first();
        if ($user->role == 'admin') {
            return redirect()->route('tasks.keluhan', ['id' => $this->complaintId]);
        } else {
            return redirect()->route('tasks.keluhan.teknisi', ['id' => $this->complaintId]);
        }
    }

    // Add new task
    public function addTask()
    {
        $this->validate(['newTask' => 'required|string|max:255']);

        $this->tasks[] = [
            'task' => $this->newTask,
            'completed' => false
        ];

        $this->newTask = '';
    }

    // Remove task
    public function removeTask($index)
    {
        unset($this->tasks[$index]);
        $this->tasks = array_values($this->tasks); // Reindex array
    }

    // Save all changes
    public function saveTasks()
    {
        DetailTiket::updateOrCreate(
            ['tiket_id' => $this->complaintId],
            ['tasks' => json_encode($this->tasks)]
        );

        $this->showTaskModal = false;
        $this->alert('success', 'Tasks updated successfully!');
    }

    public function loadComplaint($id)
    {
        $this->complaintId = $id;
        $this->complaint = Tiket::with(['user', 'teknisis'])->findOrFail($id);

        $this->complaintTeknisi = $this->complaint->teknisis;
    }

    public function render()
    {
        return view('livewire.lihat-keluhan',
            [
                'authUser' => User::find($this->authUserId),
            ]
    );
    }
}
