<footer class="border-t-2 border-sg-ink dark:border-sg-paper mt-16">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Top footer --}}
        <div class="py-10 grid grid-cols-1 md:grid-cols-4 gap-8 border-b border-sg-rule dark:border-sg-rule-dark">
            <div class="md:col-span-2">
                <a href="{{ route('home') }}" class="font-display text-3xl font-black text-sg-ink dark:text-sg-paper hover:opacity-70 transition">SpaceGaps</a>
                <p class="mt-3 text-sm text-sg-body dark:text-sg-paper/60 font-serif leading-relaxed max-w-sm">
                    Writing and research at the intersection of technology, society, and the ideas that shape how we live.
                </p>
            </div>

            <div>
                <h4 class="text-[10px] font-bold uppercase tracking-widest text-sg-muted mb-4">Explore</h4>
                <ul class="space-y-2.5">
                    @foreach([['Blog','blog.index'],['Portfolio','portfolio.index'],['Papers','papers.index'],['About','about'],['Contact','contact']] as [$l,$r])
                        <li><a href="{{ route($r) }}" class="text-sm font-serif text-sg-body dark:text-sg-paper/70 hover:text-sg-ink dark:hover:text-sg-paper transition">{{ $l }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h4 class="text-[10px] font-bold uppercase tracking-widest text-sg-muted mb-4">Newsletter</h4>
                <p class="text-sm text-sg-body dark:text-sg-paper/60 font-serif mb-4">Get new essays in your inbox.</p>
                @if(session('newsletter_success'))
                    <p class="text-sm font-semibold text-sg-body dark:text-sg-paper">{{ session('newsletter_success') }}</p>
                @else
                    <form action="{{ route('newsletter.subscribe') }}" method="POST" class="space-y-2">
                        @csrf
                        <input type="email" name="email" placeholder="your@email.com" required class="form-input text-sm">
                        <button type="submit" class="btn-primary w-full justify-center">Subscribe</button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Bottom strip --}}
        <div class="py-4 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-[11px] text-sg-muted">© {{ date('Y') }} SpaceGaps. All rights reserved.</p>
            <div class="flex items-center gap-6">
                <a href="{{ route('search') }}" class="text-[11px] text-sg-muted hover:text-sg-ink dark:hover:text-sg-paper transition uppercase tracking-widest">Search</a>
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="text-[11px] text-sg-muted hover:text-sg-ink dark:hover:text-sg-paper transition uppercase tracking-widest">Admin</a>
                @endauth
            </div>
        </div>
    </div>
</footer>
