@extends('layouts.app')
@section('title', $type === 'vacation' ? 'Заявка на отпуск' : 'Заявка на больничный')

@section('content')
<div class="request-form">
    <div class="request-form__card">
        <div class="request-form__header">
            <h2 class="request-form__title">
                {{ $type === 'vacation' ? 'Заявка на отпуск' : 'Заявка на больничный' }}
            </h2>
            <div class="request-form__icon">
                @if($type === 'vacation')
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 7.5V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-1.5"></path>
                    <path d="M12 12h9"></path>
                    <path d="M16 16l5-5"></path>
                    <path d="M16 8l5 5"></path>
                </svg>
                @else
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                </svg>
                @endif
            </div>
        </div>
        
        @if($errors->any())
        <div class="alert alert-danger">
            <strong>Ошибка!</strong> Пожалуйста, проверьте введенные данные:
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        
        <form method="POST" action="{{ route('leave_requests.store') }}" class="request-form__form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">
            
            <div class="request-form__field">
                <label for="date_start" class="request-form__label">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    Дата начала
                </label>
                <input type="date" id="date_start" name="date_start" value="{{ old('date_start') }}" required class="request-form__input">
            </div>
            
            <div class="request-form__field">
                <label for="date_end" class="request-form__label">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    Дата окончания
                </label>
                <input type="date" id="date_end" name="date_end" value="{{ old('date_end') }}" required class="request-form__input">
            </div>
            
            <div class="request-form__field">
                <label for="reason" class="request-form__label">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                    </svg>
                    Причина (необязательно)
                </label>
                <input type="text" id="reason" name="reason" value="{{ old('reason') }}" maxlength="255" placeholder="Укажите причину (необязательно)" class="request-form__input">
            </div>
            
            @if($type === 'sick_leave')
            <div class="request-form__field">
                <label for="document" class="request-form__label">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                    Справка от врача (необязательно)
                </label>
                <input type="file" id="document" name="document" class="request-form__input request-form__input--file" accept="image/jpeg,image/png,application/pdf">
                <div class="request-form__file-info">Допустимые форматы: JPEG, PNG, PDF. Максимальный размер: 5MB</div>
            </div>
            @endif
            
            <div class="request-form__info">
                @if($type === 'vacation')
                <div class="request-form__info-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                </div>
                <div class="request-form__info-text">
                    <p>Заявка на отпуск должна быть подана не менее чем за 14 дней до предполагаемой даты начала отпуска.</p>
                </div>
                @else
                <div class="request-form__info-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                </div>
                <div class="request-form__info-text">
                    <p>Заявка на больничный должна быть подана в день невыхода на работу. По возможности, приложите справку от врача.</p>
                </div>
                @endif
            </div>
            
            <div class="request-form__actions">
                <button type="submit" class="request-form__btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    Отправить заявку
                </button>
                <a href="{{ route('leave_requests.index') }}" class="request-form__btn request-form__btn--gray">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Отмена
                </a>
            </div>
        </form>
    </div>
</div>

<style>
.request-form {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 2.5rem 0;
}

.request-form__card {
    background: var(--color-white);
    border-radius: 12px;
    box-shadow: 0 2px 16px rgba(36,36,36,0.07);
    padding: 2.2rem 2.5rem 2rem 2.5rem;
    min-width: 340px;
    max-width: 480px;
    width: 100%;
    border: 1.5px solid var(--color-light-gray);
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.request-form__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.request-form__title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--color-black);
    margin: 0;
    letter-spacing: -0.5px;
}

.request-form__icon {
    color: var(--color-primary);
    opacity: 0.9;
}

.request-form__form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.request-form__field {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.request-form__label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 1rem;
    font-weight: 600;
    color: var(--color-dark-gray);
}

.request-form__label svg {
    color: var(--color-primary);
}

.request-form__input {
    width: 100%;
    padding: 0.8rem 1rem;
    font-size: 1.02rem;
    color: var(--color-black);
    background: var(--color-background);
    border: 1.2px solid var(--color-light-gray);
    border-radius: 8px;
    outline: none;
    transition: border-color 0.18s, box-shadow 0.18s;
    box-shadow: none;
    font-weight: 500;
    letter-spacing: 0.01em;
}

.request-form__input[type="date"] {
    appearance: none;
    -webkit-appearance: none;
    max-width: 100%;
    padding-right: 0.6rem;
}

.request-form__input--file {
    padding: 0.6rem;
    font-size: 0.95rem;
}

.request-form__file-info {
    font-size: 0.85rem;
    color: #777;
    margin-top: 0.3rem;
}

.request-form__input:focus {
    border-color: var(--color-primary);
    background: #fff7f3;
    box-shadow: 0 0 0 2px rgba(243, 112, 50, 0.10);
}

.request-form__info {
    display: flex;
    gap: 10px;
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid #eee;
}

.request-form__info-icon {
    color: var(--color-primary);
    padding-top: 2px;
}

.request-form__info-text {
    font-size: 0.95rem;
    color: #666;
}

.request-form__info-text p {
    margin: 0;
    line-height: 1.5;
}

.request-form__actions {
    display: flex;
    gap: 1rem;
    margin-top: 0.5rem;
}

.request-form__btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 0.7rem 1.2rem;
    background-color: var(--color-primary);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
    cursor: pointer;
    font-size: 1rem;
    justify-content: center;
    flex: 1;
}

.request-form__btn:hover {
    background-color: var(--color-accent);
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(243, 112, 50, 0.3);
}

.request-form__btn--gray {
    background-color: #e0e0e0;
    color: var(--color-dark-gray);
}

.request-form__btn--gray:hover {
    background-color: #d0d0d0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.request-form__btn svg {
    flex-shrink: 0;
}

/* Адаптивная верстка */
@media (max-width: 900px) {
    .request-form__card {
        padding: 1.5rem 1.2rem;
        max-width: 95vw;
    }
    
    .request-form {
        padding: 1.5rem 0;
    }
    
    .request-form__actions {
        flex-direction: column;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateStart = document.getElementById('date_start');
    const dateEnd = document.getElementById('date_end');
    
    // Установка минимальной даты начала как сегодня
    const today = new Date().toISOString().split('T')[0];
    dateStart.min = today;
    
    // При изменении даты начала, дата окончания не может быть раньше
    dateStart.addEventListener('change', function() {
        dateEnd.min = this.value;
        
        // Если дата окончания уже выбрана и она раньше новой даты начала
        if (dateEnd.value && dateEnd.value < this.value) {
            dateEnd.value = this.value;
        }
    });
    
    // Если дата окончания еще не выбрана, устанавливаем минимальную дату
    if (!dateEnd.value) {
        dateEnd.min = dateStart.value || today;
    }
});
</script>
@endsection 