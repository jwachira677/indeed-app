<!-- resources/views/partials/messages.blade.php -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1050;" id="toast-container">
    @if(session('success') || request('message'))
        <div class="toast-notify toast-notify--success" role="alert">
            <div class="toast-notify__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="toast-notify__body">
                <p class="toast-notify__title">Success</p>
                <p class="toast-notify__message">{{ session('success') ?? request('message') }}</p>
            </div>
            <button class="toast-notify__close" type="button" aria-label="Close">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
            <div class="toast-notify__progress"></div>
        </div>
    @endif

    @if(session('error') || (request('type') === 'error' && request('message')))
        <div class="toast-notify toast-notify--error" role="alert">
            <div class="toast-notify__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div class="toast-notify__body">
                <p class="toast-notify__title">Error</p>
                <p class="toast-notify__message">{{ session('error') ?? request('message') }}</p>
            </div>
            <button class="toast-notify__close" type="button" aria-label="Close">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
            <div class="toast-notify__progress"></div>
        </div>
    @endif

    @if ($errors->any())
        <div class="toast-notify toast-notify--warning" role="alert">
            <div class="toast-notify__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <div class="toast-notify__body">
                <p class="toast-notify__title">Validation Error</p>
                <p class="toast-notify__message">{{ $errors->first() }}</p>
            </div>
            <button class="toast-notify__close" type="button" aria-label="Close">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
            <div class="toast-notify__progress"></div>
        </div>
    @endif
</div>

<style>
.toast-notify {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.875rem 1rem;
    min-width: 300px;
    max-width: 380px;
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,.15);
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / .15), 0 10px 30px -5px rgb(0 0 0 / .25);
    position: relative;
    overflow: hidden;
    margin-bottom: 0.5rem;
    animation: toastIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    pointer-events: auto;
}

.toast-notify--success { background: #16a34a; }
.toast-notify--error   { background: #dc2626; }
.toast-notify--warning { background: #d97706; }

@keyframes toastIn {
    from { opacity: 0; transform: translateX(calc(100% + 1.25rem)); }
    to   { opacity: 1; transform: translateX(0); }
}

.toast-notify::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 3.5px;
    background: rgba(255,255,255,0.4);
}

.toast-notify__icon {
    flex-shrink: 0;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: grid;
    place-items: center;
    background: rgba(255,255,255,0.2);
    color: #fff;
}
.toast-notify__icon svg { width: 16px; height: 16px; }

.toast-notify__body { flex: 1; min-width: 0; }
.toast-notify__title {
    font-size: 0.8125rem;
    font-weight: 600;
    color: #fff;
    margin: 0 0 0.125rem;
}
.toast-notify__message {
    font-size: 0.8125rem;
    color: rgba(255,255,255,0.85);
    margin: 0;
    line-height: 1.4;
}

.toast-notify__close {
    flex-shrink: 0;
    width: 24px;
    height: 24px;
    border: none;
    background: transparent;
    border-radius: 6px;
    cursor: pointer;
    color: rgba(255,255,255,0.7);
    display: grid;
    place-items: center;
    padding: 0;
    transition: background 0.15s, color 0.15s;
}
.toast-notify__close:hover {
    background: rgba(255,255,255,0.15);
    color: #fff;
}
.toast-notify__close svg { width: 13px; height: 13px; }

.toast-notify__progress {
    position: absolute;
    bottom: 0; left: 0;
    height: 2.5px;
    width: 100%;
    transform-origin: left;
    background: rgba(255,255,255,0.5);
    animation: toastProgress 4s linear forwards;
}

@keyframes toastProgress {
    from { transform: scaleX(1); }
    to   { transform: scaleX(0); }
}
</style>

<script>
(function() {
    // Function to close a toast with animation
    function closeToast(toast) {
        if (!toast || toast._closing) return;
        toast._closing = true;
        
        // Stop the progress animation
        var progress = toast.querySelector('.toast-notify__progress');
        if (progress) {
            progress.style.animation = 'none';
        }
        
        toast.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(calc(100% + 1.25rem))';
        
        setTimeout(function() {
            if (toast && toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }
    
    // Initialize close buttons
    function initToasts() {
        var closeButtons = document.querySelectorAll('.toast-notify__close');
        for (var i = 0; i < closeButtons.length; i++) {
            var button = closeButtons[i];
            button.removeEventListener('click', closeButtonHandler);
            button.addEventListener('click', closeButtonHandler);
        }
    }
    
    function closeButtonHandler(e) {
        e.preventDefault();
        e.stopPropagation();
        var toast = this.closest('.toast-notify');
        if (toast) {
            closeToast(toast);
        }
    }
    
    // Auto-close after 4 seconds
    function autoCloseToasts() {
        var toasts = document.querySelectorAll('.toast-notify');
        for (var i = 0; i < toasts.length; i++) {
            var toast = toasts[i];
            if (!toast._autoCloseTimer) {
                toast._autoCloseTimer = setTimeout(function(t) {
                    return function() {
                        closeToast(t);
                    };
                }(toast), 4000);
            }
        }
    }
    
    // Run when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initToasts();
            autoCloseToasts();
        });
    } else {
        initToasts();
        autoCloseToasts();
    }
})();
</script>