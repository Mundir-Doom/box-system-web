@if(!blank($merchantShops))
    @foreach ($merchantShops as $shop)
        <option value="{{ $shop->id }}" {{ (old('shop_id') == $shop->id) ? 'selected' : '' }}>{{ $shop->name }}</option>
    @endforeach
@else
    <option value="" disabled>No shops available for this merchant</option>
    <option value="create_shop" style="color: #28a745; font-weight: bold;">+ Create New Shop</option>
@endif
