@extends('layouts.app')
@section('title', 'Управление уведомлениями')

@section('content')
<div class="notifications-container">
    <div class="notifications-header">
        <h1 class="notifications-title">Управление уведомлениями</h1>
        <a href="{{ route('notifications.create') }}" class="notifications-create-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="16"></line>
                <line x1="8" y1="12" x2="16" y2="12"></line>
            </svg>
            <span>Создать уведомление</span>
        </a>
    </div>
    

    <div class="notifications-table-container">
        <table class="notifications-table">
            <thead>
                <tr>
                    <th>Заголовок</th>
                    <th>Тип</th>
                    <th>Статус</th>
                    <th>Глобальное</th>
                    <th>Период</th>
                    <th>Создано</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notifications as $notification)
                <tr>
                    <td>{{ $notification->title }}</td>
                    <td>
                        @if($notification->type == 'info')
                            <span class="badge badge-info">Информационное</span>
                        @elseif($notification->type == 'warning')
                            <span class="badge badge-warning">Предупреждение</span>
                        @elseif($notification->type == 'important')
                            <span class="badge badge-danger">Важное</span>
                        @endif
                    </td>
                    <td>
                        @if($notification->isActive())
                            <span class="badge badge-success">Активно</span>
                        @else
                            <span class="badge badge-secondary">Неактивно</span>
                        @endif
                    </td>
                    <td>
                        @if($notification->is_global)
                            <span class="badge badge-primary">Да</span>
                        @else
                            <span class="badge badge-secondary">Нет</span>
                        @endif
                    </td>
                    <td>
                        @if($notification->start_date && $notification->end_date)
                            {{ \Carbon\Carbon::parse($notification->start_date)->format('d.m.Y') }} - 
                            {{ \Carbon\Carbon::parse($notification->end_date)->format('d.m.Y') }}
                        @elseif($notification->start_date)
                            С {{ \Carbon\Carbon::parse($notification->start_date)->format('d.m.Y') }}
                        @elseif($notification->end_date)
                            До {{ \Carbon\Carbon::parse($notification->end_date)->format('d.m.Y') }}
                        @else
                            Бессрочно
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($notification->created_at)->format('d.m.Y H:i') }}</td>
                    <td class="notifications-actions">
                        <a href="{{ route('notifications.edit', $notification) }}" class="action-icon edit-icon" title="Изменить">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        </a>
                        
                        <form method="POST" action="{{ route('notifications.destroy', $notification) }}" class="delete-form" data-notification-title="{{ $notification->title }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="action-icon delete-icon" title="Удалить">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">Уведомления не найдены</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="pagination-container">
        {{ $notifications->links() }}
    </div>
</div>

<style>

    .notifications-container {
        background: #fff;
        border-radius: 10px;
        border: 1.5px solid var(--color-light-gray);
        padding: 30px;
        margin-bottom: 40px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);


    }

    .notifications-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .notifications-title {
        font-size: 1.8rem;
        margin: 0;
    }
    
    .notifications-create-btn {
        background-color: #F37032;
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(243, 112, 50, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .notifications-create-btn:hover {
        background-color: #e06328;
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(243, 112, 50, 0.3);
    }
    
    .notifications-create-btn svg {
        width: 18px;
        height: 18px;
    }
    
    .notifications-table-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        overflow: hidden;
        margin-bottom: 20px;
    }
    
    .notifications-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .notifications-table th {
        background-color: #f7f7f7;
        padding: 12px 15px;
        text-align: left;
        font-weight: 600;
        color: #333;
        border-bottom: 1px solid #eee;
    }
    
    .notifications-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
    }
    
    .notifications-table tr:last-child td {
        border-bottom: none;
    }
    
    .notifications-table tr:hover {
        background-color: #f9f9f9;
    }
    
    .notifications-actions {
        display: flex;
        gap: 10px;
        justify-content: center;
        align-items: center;
    }
    
    /* Стиль для формы удаления */
    .delete-form {
        display: inline-flex;
        margin: 0;
        padding: 0;
    }
    
    .action-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 4px;
        background-color: #f5f5f5;
        transition: all 0.2s ease;
        text-decoration: none;
        border: 1px solid #e0e0e0;
        color: #555;
        cursor: pointer;
        padding: 0;
    }
    
    .edit-icon {
        color: #555;
    }
    
    .edit-icon:hover {
        background-color: #e0e0e0;
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        color: var(--color-primary);
    }
    
    .delete-icon {
        color: #555;
    }
    
    .delete-icon:hover {
        background-color: #e0e0e0;
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        color: var(--color-primary);
        border: 0px;
    }
    
    .badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
        text-align: center;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .badge-info {
        background-color: #2196F3;
        color: white;
    }
    
    .badge-warning {
        background-color: #FF9800;
        color: white;
    }
    
    .badge-danger {
        background-color: #F44336;
        color: white;
    }
    
    .badge-success {
        background-color: #4CAF50;
        color: white;
    }
    
    .badge-secondary {
        background-color: #9E9E9E;
        color: white;
    }
    
    .badge-primary {
        background-color: var(--color-primary);
        color: white;
    }
    
    .pagination-container {
        display: flex;
        justify-content: center;
    }
    
    /* Адаптивность таблицы для мобильных устройств */
    @media (max-width: 768px) {
        .notifications-table {
            display: block;
            overflow-x: auto;
        }
        
        .notifications-table th, 
        .notifications-table td {
            white-space: nowrap;
            min-width: 100px;
        }
        
        .notifications-table td:first-child {
            min-width: 150px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteForms = document.querySelectorAll('.delete-form');
        
        deleteForms.forEach(form => {
            const deleteButton = form.querySelector('.delete-icon');
            const notificationTitle = form.getAttribute('data-notification-title');
            
            if (deleteButton) {
                deleteButton.addEventListener('click', function() {
                    if (confirm(`Вы уверены, что хотите удалить уведомление "${notificationTitle}"?`)) {
                        form.submit();
                    }
                });
            }
        });
    });
</script>
@endsection 