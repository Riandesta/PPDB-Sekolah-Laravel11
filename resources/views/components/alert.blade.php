<!-- resources/views/components/alert.blade.php -->
@props(['type' => 'info', 'message'])

@if($message)
<div     alert-dismissible fade show" role="alert">
    @if($type == 'success')
        <i class="fas fa-check-circle me-2"></i>
    @elseif($type == 'danger')
        <i class="fas fa-exclamation-circle me-2"></i>
    @elseif($type == 'warning')
        <i class="fas fa-exclamation-triangle me-2"></i>
    @else
        <i class="fas fa-info-circle me-2"></i>
    @endif
    {{ $message }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
