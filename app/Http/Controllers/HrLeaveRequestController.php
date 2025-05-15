<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;

class HrLeaveRequestController extends Controller
{
    /**
     * Конструктор с проверкой прав доступа
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:hr_specialist,admin');
    }

    /**
     * Показать список всех заявок для управления
     */
    public function index(Request $request)
    {
        $query = LeaveRequest::with('user')->orderBy('created_at', 'desc');
        
        // Фильтрация по типу заявки
        if ($request->has('type') && in_array($request->type, ['vacation', 'sick_leave'])) {
            $query->where('type', $request->type);
        }
        
        // Фильтрация по статусу
        if ($request->has('status') && in_array($request->status, ['new', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }
        
        // Фильтрация по имени сотрудника
        if ($request->has('name') && $request->name) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->name . '%');
            });
        }
        
        // Фильтрация по департаменту
        if ($request->has('department') && $request->department) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('department', $request->department);
            });
        }

        // Фильтрация по дате
        if ($request->has('date_from') && $request->date_from) {
            $query->where('date_start', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->where('date_end', '<=', $request->date_to);
        }
        
        $requests = $query->paginate(20);
        
        // Получаем список всех департаментов для фильтра
        $departments = User::select('department')->distinct()->pluck('department')->toArray();
        
        return view('hr.leave_requests.index', compact('requests', 'departments'));
    }

    /**
     * Показать детали заявки
     */
    public function show(LeaveRequest $leaveRequest)
    {
        return view('hr.leave_requests.show', compact('leaveRequest'));
    }

    /**
     * Обновить статус заявки (одобрить или отклонить)
     */
    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'hr_comment' => 'nullable|string|max:255',
        ]);

        // Проверяем, не пытается ли пользователь одобрить свою собственную заявку
        $currentUser = Auth::user();
        
        if ($leaveRequest->user_id === $currentUser->id && !$currentUser->isAdmin()) {
            return redirect()->route('hr.leave_requests.index')
                ->with('error', 'Вы не можете одобрять или отклонять свои собственные заявки. Это может сделать только администратор.');
        }

        $leaveRequest->status = $request->status;
        $leaveRequest->hr_comment = $request->hr_comment;
        $leaveRequest->save();

        return redirect()->route('hr.leave_requests.index')
            ->with('success', 'Статус заявки успешно обновлен.');
    }

    /**
     * Показать страницу с фильтром только по заявкам на отпуск
     */
    public function vacations(Request $request)
    {
        $request->merge(['type' => 'vacation']);
        return $this->index($request);
    }

    /**
     * Показать страницу с фильтром только по заявкам на больничный
     */
    public function sickLeaves(Request $request)
    {
        $request->merge(['type' => 'sick_leave']);
        return $this->index($request);
    }
    
    /**
     * Показать страницу с фильтром только по новым заявкам
     */
    public function pending(Request $request)
    {
        $request->merge(['status' => 'new']);
        return $this->index($request);
    }
} 