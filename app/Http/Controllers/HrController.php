<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class HrController extends Controller
{
    public function index()
    {
        // Перенаправляем на страницу кадрового учета
        return redirect()->route('hr.personnel.index');
    }
    
    /**
     * Показывает список сотрудников (кадровый учёт)
     */
    public function personnel(Request $request)
    {
        // Получение параметров сортировки из URL
        $sortField = $request->input('sort', 'name');
        $sortOrder = $request->input('order', 'asc');
        
        // Разрешенные поля для сортировки
        $allowedSortFields = ['name', 'position', 'department', 'role', 'hired_at'];
        
        // Проверка валидности поля сортировки
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'name';
        }
        
        // Запрос с сортировкой
        $query = User::query();
        
        // Специальная обработка для сортировки по имени
        if ($sortField === 'name') {
            $query->orderBy('name', $sortOrder);
        } 
        // Сортировка по дате приема
        elseif ($sortField === 'hired_at') {
            $query->orderBy('hired_at', $sortOrder);
        }
        // Сортировка по роли (с кастомным порядком)
        elseif ($sortField === 'role') {
            // Для сортировки по роли используем CASE WHEN в SQL
            $roleOrderCase = "CASE 
                WHEN role = 'admin' THEN 1 
                WHEN role = 'hr_specialist' THEN 2 
                WHEN role = 'employee' THEN 3 
                ELSE 4 
            END";
            
            if ($sortOrder === 'desc') {
                $roleOrderCase = "CASE 
                    WHEN role = 'employee' THEN 1 
                    WHEN role = 'hr_specialist' THEN 2 
                    WHEN role = 'admin' THEN 3 
                    ELSE 4 
                END";
            }
            
            $query->orderByRaw($roleOrderCase);
        }
        // Стандартная сортировка для остальных полей
        else {
            $query->orderBy($sortField, $sortOrder);
        }
        
        // Добавляем вторичную сортировку по имени для порядка
        if ($sortField !== 'name') {
            $query->orderBy('name', 'asc');
        }
        
        $employees = $query->paginate(8)
                          ->appends($request->except('page'));
                        
        return view('hr.personnel.index', compact('employees', 'sortField', 'sortOrder'));
    }
    
    /**
     * Показывает форму для добавления нового сотрудника
     */
    public function createEmployee()
    {
        $departments = User::select('department')
                         ->distinct()
                         ->pluck('department')
                         ->sort()
                         ->toArray();
                         
        $positions = User::select('position')
                        ->distinct()
                        ->whereNotNull('position')
                        ->pluck('position')
                        ->sort()
                        ->toArray();
                        
        return view('hr.personnel.create', compact('departments', 'positions'));
    }
    
    /**
     * Сохраняет нового сотрудника в базе данных
     */
    public function storeEmployee(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users',
            'email' => 'nullable|email|max:255|unique:users',
            'phone_number' => 'nullable|string|max:20',
            'position' => 'required|string|max:100',
            'department' => 'required|string|max:100',
            'hired_at' => 'nullable|date',
            'role' => 'required|in:employee,hr_specialist,admin',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Введите ФИО сотрудника',
            'username.required' => 'Введите логин сотрудника',
            'username.unique' => 'Такой логин уже используется',
            'email.unique' => 'Такой email уже используется',
            'position.required' => 'Укажите должность',
            'department.required' => 'Укажите отдел',
            'role.required' => 'Выберите роль',
            'password.required' => 'Введите пароль',
            'password.min' => 'Минимальная длина пароля - 6 символов',
            'password.confirmed' => 'Пароли не совпадают',
        ]);
        
        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'position' => $validated['position'],
            'department' => $validated['department'],
            'hired_at' => $validated['hired_at'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);
        
        return redirect()->route('hr.personnel.index')
                         ->with('success', 'Сотрудник успешно добавлен');
    }
    
    /**
     * Показывает форму для редактирования сотрудника
     */
    public function editEmployee(User $user)
    {
        $departments = User::select('department')
                         ->distinct()
                         ->pluck('department')
                         ->sort()
                         ->toArray();
                         
        $positions = User::select('position')
                        ->distinct()
                        ->whereNotNull('position')
                        ->pluck('position')
                        ->sort()
                        ->toArray();
                        
        return view('hr.personnel.edit', compact('user', 'departments', 'positions'));
    }
    
    /**
     * Обновляет данные сотрудника
     */
    public function updateEmployee(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone_number' => 'nullable|string|max:20',
            'position' => 'required|string|max:100',
            'department' => 'required|string|max:100',
            'hired_at' => 'nullable|date',
            'role' => 'required|in:employee,hr_specialist,admin',
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'name.required' => 'Введите ФИО сотрудника',
            'username.required' => 'Введите логин сотрудника',
            'username.unique' => 'Такой логин уже используется',
            'email.unique' => 'Такой email уже используется',
            'position.required' => 'Укажите должность',
            'department.required' => 'Укажите отдел',
            'role.required' => 'Выберите роль',
            'password.min' => 'Минимальная длина пароля - 6 символов',
            'password.confirmed' => 'Пароли не совпадают',
        ]);
        
        // Обновляем данные пользователя
        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->phone_number = $validated['phone_number'];
        $user->position = $validated['position'];
        $user->department = $validated['department'];
        $user->hired_at = $validated['hired_at'];
        $user->role = $validated['role'];
        
        // Если указан новый пароль, обновляем его
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        
        $user->save();
        
        return redirect()->route('hr.personnel.index')
                         ->with('success', 'Данные сотрудника успешно обновлены');
    }
    
    /**
     * Показывает детальную информацию о сотруднике
     */
    public function showEmployee(User $user)
    {
        // Получаем статистику по посещаемости за последний месяц
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        $attendanceStats = [
            'present' => 0,
            'absent' => 0,
            'vacation' => 0,
            'sick_leave' => 0,
        ];
        
        $attendances = \App\Models\Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get();
        
        foreach ($attendances as $attendance) {
            if (isset($attendanceStats[$attendance->status])) {
                $attendanceStats[$attendance->status]++;
            }
        }
        
        // Получаем заявки на отпуск и больничный
        $leaveRequests = \App\Models\LeaveRequest::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Рассчитываем трудовой стаж
        $experience = null;
        if ($user->hired_at) {
            $hiredDate = Carbon::parse($user->hired_at);
            $now = Carbon::now();
            $experience = [
                'years' => $now->diffInYears($hiredDate),
                'months' => $now->copy()->subYears($now->diffInYears($hiredDate))->diffInMonths($hiredDate),
                'days' => $now->copy()->subYears($now->diffInYears($hiredDate))->subMonths($now->copy()->subYears($now->diffInYears($hiredDate))->diffInMonths($hiredDate))->diffInDays($hiredDate)
            ];
        }
        
        return view('hr.personnel.show', compact('user', 'attendanceStats', 'leaveRequests', 'experience'));
    }
}
