@extends('layouts.admin')
@section('page_title', 'Gallery')

@section('content')

{{-- ── Drag-and-drop upload zone ──────────────────────────────────────── --}}
<div class="mb-8"
     x-data="galleryUploader()"
     x-init="init()"
     @dragover.prevent="dragging = true"
     @dragleave.prevent="dragging = false"
     @drop.prevent="onDrop($event)">

    {{-- Drop zone --}}
    <div :class="dragging ? 'border-indigo-500 bg-indigo-50' : 'border-zinc-300 bg-white hover:border-indigo-400 hover:bg-zinc-50'"
         class="border-2 border-dashed rounded-xl p-10 text-center transition-all duration-150 cursor-pointer"
         @click="$refs.fileInput.click()">

        <svg class="w-10 h-10 mx-auto mb-3 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
        </svg>
        <p class="text-sm font-semibold text-zinc-600">Drop images here, or <span class="text-indigo-600">browse</span></p>
        <p class="text-xs text-zinc-400 mt-1">JPG, PNG, GIF, WebP · up to 20 MB each · multiple files at once</p>

        <input type="file" x-ref="fileInput" multiple accept="image/*"
               class="hidden" @change="onFileInput($event)">
    </div>

    {{-- Upload queue --}}
    <div x-show="queue.length > 0" class="mt-4 space-y-2">
        <template x-for="(item, i) in queue" :key="i">
            <div class="flex items-center gap-3 bg-white border border-zinc-200 rounded-xl px-4 py-2.5">
                {{-- Thumb --}}
                <img :src="item.preview" class="w-10 h-10 rounded-lg object-cover flex-shrink-0">

                {{-- Name + bar --}}
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-zinc-700 truncate" x-text="item.name"></p>
                    <div class="mt-1 h-1.5 bg-zinc-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-200"
                             :class="item.error ? 'bg-red-400' : item.done ? 'bg-emerald-400' : 'bg-indigo-500'"
                             :style="`width: ${item.progress}%`"></div>
                    </div>
                </div>

                {{-- State badge --}}
                <span class="text-xs font-medium shrink-0"
                      :class="item.error ? 'text-red-500' : item.done ? 'text-emerald-600' : 'text-zinc-400'"
                      x-text="item.error ? 'Error' : item.done ? 'Done' : item.progress + '%'"></span>
            </div>
        </template>
    </div>

    {{-- Newly uploaded thumbnails (appear inline before page refresh) --}}
    <div x-show="uploaded.length > 0" class="mt-4">
        <p class="text-xs font-semibold text-zinc-500 uppercase tracking-widest mb-2">Just uploaded</p>
        <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-2">
            <template x-for="img in uploaded" :key="img.id">
                <div class="relative group rounded-xl overflow-hidden aspect-square bg-zinc-100">
                    <img :src="img.url" class="w-full h-full object-cover">
                    <a :href="img.edit_url"
                       class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition text-white text-xs font-semibold">
                        Edit
                    </a>
                </div>
            </template>
        </div>
    </div>
</div>

{{-- ── Toolbar ─────────────────────────────────────────────────────────── --}}
<div class="flex items-center justify-between mb-5">
    <div class="flex items-center gap-3">
        @if($categories->isNotEmpty())
            <form method="GET" action="{{ route('admin.gallery.index') }}">
                <select name="category" onchange="this.form.submit()"
                        class="text-sm border border-zinc-200 rounded-lg px-3 py-1.5 text-zinc-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </form>
        @endif
        <span class="text-xs text-zinc-400">{{ $images->total() }} image{{ $images->total() !== 1 ? 's' : '' }}</span>
    </div>
    <a href="{{ route('admin.gallery.create') }}"
       class="px-4 py-2 border border-zinc-300 hover:bg-zinc-50 text-zinc-700 text-sm font-medium rounded-lg transition">
        + Add by URL
    </a>
</div>

{{-- ── Image grid ──────────────────────────────────────────────────────── --}}
@if($images->isNotEmpty())
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
        @foreach($images as $image)
            <div class="group relative bg-zinc-100 rounded-xl overflow-hidden aspect-square border border-zinc-200">
                <img src="{{ $image->image_url }}"
                     alt="{{ $image->title ?? 'Gallery image' }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                     loading="lazy">

                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/55 transition-all duration-200
                            flex flex-col justify-between p-2 opacity-0 group-hover:opacity-100">
                    <div class="flex justify-end">
                        @if($image->featured)
                            <span class="px-1.5 py-0.5 text-xs bg-amber-400 text-amber-900 rounded-full font-bold">★</span>
                        @endif
                    </div>
                    <div>
                        @if($image->title)
                            <p class="text-white text-xs font-semibold truncate mb-1">{{ $image->title }}</p>
                        @endif
                        @if($image->category)
                            <p class="text-white/60 text-xs truncate mb-2">{{ $image->category }}</p>
                        @endif
                        <div class="flex gap-1.5">
                            <a href="{{ route('admin.gallery.edit', $image) }}"
                               class="flex-1 py-1 text-center text-xs bg-white/20 hover:bg-white/35 text-white rounded-lg transition">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.gallery.destroy', $image) }}"
                                  onsubmit="return confirm('Delete this image?')">
                                @csrf @method('DELETE')
                                <button class="px-2.5 py-1 text-xs bg-red-500/70 hover:bg-red-500 text-white rounded-lg transition">✕</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $images->withQueryString()->links() }}
    </div>
@else
    <div class="bg-white rounded-xl border border-zinc-200 p-16 text-center text-zinc-400">
        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <p class="text-sm">No images yet — drag some in above.</p>
    </div>
@endif

@endsection

@push('scripts')
<script>
function galleryUploader() {
    return {
        dragging: false,
        queue:    [],   // { name, preview, progress, done, error }
        uploaded: [],   // successfully created { id, url, edit_url }

        init() {},

        onDrop(e) {
            this.dragging = false;
            this.handleFiles([...e.dataTransfer.files]);
        },

        onFileInput(e) {
            this.handleFiles([...e.target.files]);
            e.target.value = ''; // reset so same files can be re-dropped
        },

        handleFiles(files) {
            const images = files.filter(f => f.type.startsWith('image/'));
            if (!images.length) return;
            images.forEach(file => this.enqueue(file));
        },

        enqueue(file) {
            const item = {
                name:     file.name,
                preview:  URL.createObjectURL(file),
                progress: 0,
                done:     false,
                error:    false,
                _file:    file,
            };
            this.queue.push(item);
            this.uploadFile(item);
        },

        uploadFile(item) {
            const fd = new FormData();
            fd.append('files[]', item._file);
            fd.append('_token', document.querySelector('meta[name="csrf-token"]')?.content
                                ?? '{{ csrf_token() }}');

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route('admin.gallery.upload') }}');

            xhr.upload.onprogress = e => {
                if (e.lengthComputable) item.progress = Math.round(e.loaded / e.total * 95);
            };

            xhr.onload = () => {
                if (xhr.status === 200) {
                    const data = JSON.parse(xhr.responseText);
                    item.progress = 100;
                    item.done     = true;
                    this.uploaded.push(...data.images);
                } else {
                    item.error = true;
                    item.progress = 100;
                }
            };

            xhr.onerror = () => { item.error = true; item.progress = 100; };
            xhr.send(fd);
        },
    };
}
</script>
@endpush
