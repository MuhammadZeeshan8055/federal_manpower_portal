<div
    class="master-page"
    x-data="{
        methodOpen: @js((bool) $editingMethodId),
        accountOpen: @js((bool) $editingAccountId)
    }"
    x-on:method-form-reset.window="methodOpen = false"
    x-on:account-form-reset.window="accountOpen = false"
>
    @if ($denied)
        <div class="panel empty-card">
            <h3>No access</h3>
            <p>You do not have permission to manage payment methods.</p>
        </div>
    @else
        @if ($successMessage !== '')
            <div
                class="portal-toast portal-toast--{{ $toastType }}"
                wire:key="method-toast-{{ $toastVersion }}"
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 5000)"
                x-show="show"
                role="status"
            >
                <span class="portal-toast__copy"><strong>{{ $successMessage }}</strong></span>
            </div>
        @endif

        @if ($canManage)
            <div class="loan-forms-row">
                <section class="panel master-form-panel master-form-panel--collapsible loan-forms-row__col" :class="{ 'master-form-panel--open': methodOpen }">
                    @include('partials.master-form-toggle', [
                        'targetId' => 'payment-method-form-fields',
                        'eyebrow' => $editingMethodId ? 'EDIT' : 'ADD',
                        'title' => $editingMethodId ? 'Edit method' : 'Payment method',
                        'actionLabel' => 'Add method',
                        'openVar' => 'methodOpen',
                    ])

                    <div id="payment-method-form-fields" class="master-form-collapse" :class="{ 'master-form-collapse--open': methodOpen }" :aria-hidden="(!methodOpen).toString()" :inert="!methodOpen">
                        <div class="master-form-collapse__inner"><div class="master-form-content">
                            <form class="master-form" wire:submit="saveMethod">
                                <label>
                                    Method name
                                    <input type="text" wire:model="method_name" placeholder="e.g. Bank transfer, EasyPaisa" maxlength="120">
                                    @error('method_name') <span class="master-error">{{ $message }}</span> @enderror
                                </label>

                                <label class="master-check">
                                    <input type="checkbox" wire:model="method_active">
                                    <span>Active</span>
                                </label>

                                <div class="master-form__actions">
                                    <button type="submit" class="button button--soft">
                                        {{ $editingMethodId ? 'Save method' : 'Add method' }}
                                    </button>
                                    @if ($editingMethodId)
                                        <button type="button" class="button button--ghost" wire:click="cancelMethodEdit">Cancel</button>
                                    @endif
                                </div>
                            </form>
                        </div></div>
                    </div>
                </section>

                <section class="panel master-form-panel master-form-panel--collapsible loan-forms-row__col" :class="{ 'master-form-panel--open': accountOpen }">
                    @include('partials.master-form-toggle', [
                        'targetId' => 'payment-account-form-fields',
                        'eyebrow' => $editingAccountId ? 'EDIT' : 'ADD',
                        'title' => $editingAccountId ? 'Edit account' : 'Receiving account',
                        'actionLabel' => 'Add account',
                        'openVar' => 'accountOpen',
                    ])

                    <div id="payment-account-form-fields" class="master-form-collapse" :class="{ 'master-form-collapse--open': accountOpen }" :aria-hidden="(!accountOpen).toString()" :inert="!accountOpen">
                        <div class="master-form-collapse__inner"><div class="master-form-content">
                            <form class="master-form" wire:submit="saveAccount">
                                <label>
                                    Under method
                                    <select wire:model="account_method_id">
                                        <option value="">Select method</option>
                                        @foreach ($methodOptions as $option)
                                            <option value="{{ $option->id }}">{{ $option->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('account_method_id') <span class="master-error">{{ $message }}</span> @enderror
                                </label>

                                <label>
                                    Account name
                                    <input type="text" wire:model="account_name" placeholder="e.g. Meezan, Alfalah, 03001234567" maxlength="160">
                                    @error('account_name') <span class="master-error">{{ $message }}</span> @enderror
                                </label>

                                <label>
                                    Details (optional)
                                    <input type="text" wire:model="account_details" placeholder="e.g. IBAN or account title" maxlength="255">
                                    @error('account_details') <span class="master-error">{{ $message }}</span> @enderror
                                </label>

                                <label class="master-check">
                                    <input type="checkbox" wire:model="account_active">
                                    <span>Active</span>
                                </label>

                                <div class="master-form__actions">
                                    <button type="submit" class="button button--soft">
                                        {{ $editingAccountId ? 'Save account' : 'Add account' }}
                                    </button>
                                    @if ($editingAccountId)
                                        <button type="button" class="button button--ghost" wire:click="cancelAccountEdit">Cancel</button>
                                    @endif
                                </div>
                            </form>
                        </div></div>
                    </div>
                </section>
            </div>
        @endif

        <section class="panel master-list-panel">
            <div class="panel__header">
                <div>
                    <p class="panel__eyebrow">ACCOUNTS</p>
                    <h3>Methods &amp; receiving accounts</h3>
                </div>
                <label class="master-search">
                    <span class="master-search__icon" aria-hidden="true">@include('partials.icon', ['name' => 'search'])</span>
                    <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search..." aria-label="Search">
                </label>
            </div>

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Method</th>
                            <th>Receiving accounts</th>
                            <th>Status</th>
                            @if ($canManage)
                                <th class="master-actions-heading">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($methods as $method)
                            <tr wire:key="method-{{ $method->id }}">
                                <td><strong>{{ $method->name }}</strong></td>
                                <td>
                                    @if ($method->accounts->isEmpty())
                                        <span class="muted">No accounts yet</span>
                                    @else
                                        <ul class="payment-account-list">
                                            @foreach ($method->accounts as $account)
                                                <li wire:key="account-{{ $account->id }}">
                                                    <span class="{{ $account->is_active ? '' : 'muted' }}">
                                                        {{ $account->label() }}
                                                        @unless ($account->is_active)
                                                            (inactive)
                                                        @endunless
                                                    </span>
                                                    @if ($canManage)
                                                        <span class="payment-account-list__actions">
                                                            <button type="button" class="row-action row-action--edit" wire:click="editAccount({{ $account->id }})" @click="accountOpen = true">
                                                                <span>Edit</span>
                                                            </button>
                                                            <button type="button" class="row-action row-action--delete" wire:click="deleteAccount({{ $account->id }})" wire:confirm="Delete this receiving account?">
                                                                <span>Delete</span>
                                                            </button>
                                                        </span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </td>
                                <td>
                                    @if ($method->is_active)
                                        <span class="status status--ready">Active</span>
                                    @else
                                        <span class="status status--interview">Inactive</span>
                                    @endif
                                </td>
                                @if ($canManage)
                                    <td class="master-row-actions">
                                        <button type="button" class="row-action row-action--edit" wire:click="editMethod({{ $method->id }})" @click="methodOpen = true">
                                            <span>Edit</span>
                                        </button>
                                        <button type="button" class="row-action row-action--delete" wire:click="deleteMethod({{ $method->id }})" wire:confirm="Delete this method and its empty accounts?">
                                            @include('partials.icon', ['name' => 'trash'])
                                            <span>Delete</span>
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $canManage ? 4 : 3 }}" class="muted">No payment methods yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif
</div>
