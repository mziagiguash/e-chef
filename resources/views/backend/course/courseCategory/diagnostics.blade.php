<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Translations (raw)</th>
            <th>Translation Locales</th>
            <th>Category Name by Locale (en / ru / ka)</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($data as $d)
            @php
                $translationsArray = $d->translations->map(fn($t) => [
                    'locale' => $t->locale ?? ($t->language ?? 'no-locale'),
                    'category_name' => $t->category_name ?? ($t->name ?? 'no-name'),
                ]);
                $name_en = $d->translations->firstWhere('locale', 'en')->category_name ?? 'No Name';
                $name_ru = $d->translations->firstWhere('locale', 'ru')->category_name ?? 'No Name';
                $name_ka = $d->translations->firstWhere('locale', 'ka')->category_name ?? 'No Name';
            @endphp
            <tr>
                <td>{{ $d->id }}</td>
                <td>
                    <pre style="white-space: pre-wrap; max-width: 400px;">{{ json_encode($translationsArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </td>
                <td>
                    {{ $d->translations->pluck('locale')->join(', ') }}
                </td>
                <td>
                    en: {{ $name_en }} <br>
                    ru: {{ $name_ru }} <br>
                    ka: {{ $name_ka }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center">No categories found</td>
            </tr>
        @endforelse
    </tbody>
</table>
