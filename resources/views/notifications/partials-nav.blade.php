<div class="actions" style="margin-bottom: 24px;">
    <a class="button secondary" href="{{ route('notifications.dashboard') }}">Dashboard</a>
    <a class="button secondary" href="{{ route('notifications.index') }}">All notifications</a>
    @if(auth()->user()->canAccessAdmin('notifications.rules.manage'))
        <a class="button secondary" href="{{ route('notifications.rules.index') }}">Rules</a>
    @endif
    @if(auth()->user()->canAccessAdmin('notifications.manage'))
        <form method="POST" action="{{ route('notifications.refresh') }}">@csrf<button class="button" type="submit">Refresh alerts</button></form>
        <form method="POST" action="{{ route('notifications.mark-all-read') }}">@csrf<button class="button secondary" type="submit">Mark all read</button></form>
    @endif
</div>
