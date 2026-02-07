<div class="card bg-zinc-900 border-secondary text-white shadow-sm">
    <div class="card-header border-secondary bg-dark text-center py-2">Rewards</div>
    <div class="card-body p-2">
        <div class="row g-2">
            <div class="col-12 col-md-6">
                <label class="form-label" for="reward_xp">XP</label>
                <input id="reward_xp" name="xp" type="number" min="0" class="form-control form-control-sm" value="{{ old('xp', $reward?->xp ?? 0) }}" required>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label" for="reward_gold">Oro</label>
                <input id="reward_gold" name="gold" type="number" min="0" class="form-control form-control-sm" value="{{ old('gold', $reward?->gold ?? 0) }}" required>
            </div>
        </div>

        @if ($hasItemsTable)
            @php
                $itemsInput = old('items', $reward?->items_json ?? []);
                if (!is_array($itemsInput)) {
                    $itemsInput = [];
                }
                $rows = max(3, count($itemsInput));
            @endphp

            <div class="mt-3">
                <div class="small text-secondary mb-2">Items (item_id + cantidad)</div>
                @for ($i = 0; $i < $rows; $i++)
                    @php
                        $row = $itemsInput[$i] ?? ['item_id' => '', 'qty' => ''];
                    @endphp
                    <div class="row g-2 mb-2">
                        <div class="col-8">
                            <select name="items[{{ $i }}][item_id]" class="form-select form-select-sm">
                                <option value="">Sin item</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}" @selected((string) ($row['item_id'] ?? '') === (string) $item->id)>
                                        {{ $item->name }} ({{ $item->id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4">
                            <input name="items[{{ $i }}][qty]" type="number" min="1" class="form-control form-control-sm" placeholder="Qty" value="{{ $row['qty'] ?? '' }}">
                        </div>
                    </div>
                @endfor
            </div>
        @else
            <div class="mt-3">
                <label class="form-label" for="items_json_raw">Items JSON</label>
                <textarea id="items_json_raw" name="items_json_raw" rows="6" class="form-control form-control-sm" placeholder='[{"item_id":1,"qty":2}]'>{{ old('items_json_raw', $reward?->items_json ? json_encode($reward->items_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '') }}</textarea>
                <div class="small text-secondary mt-1">No hay tabla de items, edita el JSON manualmente.</div>
            </div>
        @endif
    </div>
</div>
