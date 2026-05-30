@extends('layouts.admin')
@section('page_title', 'Profile')

@section('content')
<form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="max-w-5xl">
    @csrf @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Main column ──────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Identity --}}
            <div class="bg-white rounded-xl border border-zinc-200 p-6 space-y-4">
                <h3 class="text-sm font-semibold text-zinc-700 border-b border-zinc-100 pb-3">Identity</h3>
                <div>
                    <label class="admin-label">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $profile->name) }}" class="admin-input" placeholder="Your full name">
                </div>
                <div>
                    <label class="admin-label">Tagline <span class="text-zinc-400 font-normal">— shown under your name</span></label>
                    <input type="text" name="tagline" value="{{ old('tagline', $profile->tagline) }}" class="admin-input" placeholder="Writer · Researcher · Builder">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="admin-label">Location</label>
                        <input type="text" name="location" value="{{ old('location', $profile->location) }}" class="admin-input" placeholder="City, Country">
                    </div>
                    <div>
                        <label class="admin-label">Public Email</label>
                        <input type="email" name="email" value="{{ old('email', $profile->email) }}" class="admin-input" placeholder="hello@example.com">
                    </div>
                </div>
            </div>

            {{-- Intro --}}
            <div class="bg-white rounded-xl border border-zinc-200 p-6 space-y-4">
                <h3 class="text-sm font-semibold text-zinc-700 border-b border-zinc-100 pb-3">Intro</h3>
                <div>
                    <label class="admin-label">Short Intro <span class="text-zinc-400 font-normal">— 1-3 sentences, shown prominently at the top</span></label>
                    <textarea name="intro" rows="3" class="admin-input resize-none" placeholder="A brief, personal intro about who you are and what you do.">{{ old('intro', $profile->intro) }}</textarea>
                </div>
            </div>

            {{-- Professional Summary --}}
            <div class="bg-white rounded-xl border border-zinc-200 p-6 space-y-4">
                <h3 class="text-sm font-semibold text-zinc-700 border-b border-zinc-100 pb-3">Professional Summary</h3>
                <div>
                    <label class="admin-label">Summary <span class="text-zinc-400 font-normal">(HTML supported)</span></label>
                    <textarea name="summary" rows="12" class="admin-input resize-y font-mono text-sm" placeholder="Full professional background, experience, and interests…">{{ old('summary', $profile->summary) }}</textarea>
                </div>
            </div>

            {{-- Video Intro --}}
            <div class="bg-white rounded-xl border border-zinc-200 p-6 space-y-4"
                 x-data="{ url: '{{ old('video_url', $profile->video_url) }}' }">
                <h3 class="text-sm font-semibold text-zinc-700 border-b border-zinc-100 pb-3">Video Intro</h3>
                <div>
                    <label class="admin-label">YouTube or Vimeo URL</label>
                    <input type="url" name="video_url" x-model="url" class="admin-input font-mono text-sm"
                           placeholder="https://youtube.com/watch?v=… or https://vimeo.com/…">
                    <p class="text-xs text-zinc-400 mt-1.5">Paste a watch URL — it will be converted to an embed automatically.</p>
                </div>
                {{-- Preview --}}
                <div x-show="url" class="rounded-xl overflow-hidden bg-zinc-100 aspect-video">
                    <iframe :src="embedUrl(url)" class="w-full h-full" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                </div>
            </div>

        </div>

        {{-- ── Sidebar ──────────────────────────────────── --}}
        <div class="space-y-5">

            {{-- Save --}}
            <div class="bg-white rounded-xl border border-zinc-200 p-5">
                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg text-sm transition">
                    Save Profile
                </button>
                <a href="{{ route('about') }}" target="_blank"
                   class="mt-3 flex items-center justify-center gap-1.5 text-xs text-zinc-400 hover:text-indigo-600 transition">
                    View public page ↗
                </a>
            </div>

            {{-- Photo --}}
            <div class="bg-white rounded-xl border border-zinc-200 p-5 space-y-4"
                 x-data="{ preview: '{{ $profile->photo_url }}' }">
                <h3 class="text-sm font-semibold text-zinc-700">Profile Photo</h3>

                {{-- Current / preview --}}
                <div x-show="preview" class="flex justify-center">
                    <img :src="preview" alt="Profile photo"
                         class="w-32 h-32 rounded-full object-cover border-4 border-zinc-100 shadow">
                </div>
                <div x-show="!preview" class="flex justify-center">
                    <div class="w-32 h-32 rounded-full bg-zinc-100 flex items-center justify-center">
                        <svg class="w-12 h-12 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                </div>

                {{-- Upload --}}
                <div>
                    <label class="admin-label">Upload Photo</label>
                    <input type="file" name="photo_file" accept="image/*"
                           @change="preview = URL.createObjectURL($event.target.files[0])"
                           class="text-sm text-zinc-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 w-full">
                </div>

                {{-- Or URL --}}
                <div>
                    <label class="admin-label">Or paste URL</label>
                    <input type="url" name="photo_url" x-model="preview"
                           value="{{ old('photo_url', $profile->photo_url) }}"
                           class="admin-input font-mono text-xs" placeholder="https://…">
                </div>
            </div>

            {{-- Social Links --}}
            <div class="bg-white rounded-xl border border-zinc-200 p-5"
                 x-data="{
                    links: {{ json_encode($profile->social_links ?? []) }},
                    platforms: [
                        { value: 'linkedin',   label: 'LinkedIn'   },
                        { value: 'twitter',    label: 'Twitter / X' },
                        { value: 'github',     label: 'GitHub'     },
                        { value: 'youtube',    label: 'YouTube'    },
                        { value: 'instagram',  label: 'Instagram'  },
                        { value: 'facebook',   label: 'Facebook'   },
                        { value: 'substack',   label: 'Substack'   },
                        { value: 'website',    label: 'Website'    },
                    ],
                    add()    { this.links.push({ platform: 'linkedin', url: '' }) },
                    remove(i){ this.links.splice(i, 1) }
                 }">

                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-zinc-700">Social Links</h3>
                    <button type="button" @click="add()"
                            class="text-xs px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg font-medium transition">
                        + Add
                    </button>
                </div>

                <div class="space-y-2.5">
                    <template x-for="(link, i) in links" :key="i">
                        <div class="flex gap-2 items-center group">
                            <select x-model="link.platform"
                                    class="text-xs border border-zinc-200 rounded-lg px-2 py-1.5 text-zinc-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white flex-shrink-0">
                                <template x-for="p in platforms" :key="p.value">
                                    <option :value="p.value" :selected="link.platform === p.value" x-text="p.label"></option>
                                </template>
                            </select>
                            <input type="url" x-model="link.url"
                                   placeholder="https://…"
                                   class="flex-1 admin-input text-xs font-mono py-1.5">
                            <button type="button" @click="remove(i)"
                                    class="text-zinc-300 hover:text-red-400 transition opacity-0 group-hover:opacity-100 flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                </div>

                <p x-show="links.length === 0" class="text-xs text-zinc-400 text-center py-3">No links yet.</p>

                <input type="hidden" name="social_json" :value="JSON.stringify(links)">
            </div>

        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function embedUrl(url) {
    if (!url) return '';
    const yt = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/);
    if (yt) return 'https://www.youtube.com/embed/' + yt[1];
    const vm = url.match(/vimeo\.com\/(\d+)/);
    if (vm) return 'https://player.vimeo.com/video/' + vm[1];
    return url;
}
</script>
@endpush
