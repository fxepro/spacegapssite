<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Paper;
use App\Models\PortfolioItem;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@spacegaps.com'],
            ['name' => 'Admin', 'password' => Hash::make('password')]
        );

        $catDefs = [
            ['name' => 'Technology', 'color' => '#6366f1'],
            ['name' => 'Society',    'color' => '#f59e0b'],
            ['name' => 'Research',   'color' => '#10b981'],
            ['name' => 'Philosophy', 'color' => '#8b5cf6'],
            ['name' => 'Politics',   'color' => '#ef4444'],
            ['name' => 'Creative',   'color' => '#ec4899'],
            ['name' => 'Science',    'color' => '#14b8a6'],
            ['name' => 'Design',     'color' => '#f97316'],
        ];
        $categories = [];
        foreach ($catDefs as $c) {
            $categories[$c['name']] = Category::firstOrCreate(['name' => $c['name']], $c);
        }

        $tagNames = ['ai', 'writing', 'future', 'history', 'economics', 'culture', 'infrastructure', 'democracy'];
        $tags = [];
        foreach ($tagNames as $name) {
            $tags[$name] = Tag::firstOrCreate(['name' => $name]);
        }

        $posts = [
            [
                'title'        => 'The Gaps Between What We Know',
                'excerpt'      => 'Every field of human knowledge is defined as much by what it cannot explain as by what it can. The most interesting work happens in the margins.',
                'content'      => $this->sampleContent(),
                'status'       => 'published',
                'featured'     => true,
                'published_at' => now()->subDays(2),
                'cats'         => ['Philosophy', 'Research'],
                'tags'         => ['writing', 'future'],
            ],
            [
                'title'        => 'Infrastructure as Ideology',
                'excerpt'      => 'The roads we build, the pipes we lay, and the networks we wire are never neutral. They encode choices about who matters and who doesn\'t.',
                'content'      => $this->sampleContent(),
                'status'       => 'published',
                'featured'     => true,
                'published_at' => now()->subDays(10),
                'cats'         => ['Society', 'Politics'],
                'tags'         => ['infrastructure', 'democracy'],
            ],
            [
                'title'        => 'On Writing for Nobody',
                'excerpt'      => 'There is a particular freedom in writing for an audience of zero. It\'s also where the best thinking happens.',
                'content'      => $this->sampleContent(),
                'status'       => 'published',
                'featured'     => true,
                'published_at' => now()->subDays(18),
                'cats'         => ['Creative'],
                'tags'         => ['writing'],
            ],
            [
                'title'        => 'AI and the Question of Authorship',
                'excerpt'      => 'When a machine writes a sentence, who owns it? The question sounds philosophical but it\'s already reshaping creative industries.',
                'content'      => $this->sampleContent(),
                'status'       => 'published',
                'published_at' => now()->subDays(25),
                'cats'         => ['Technology', 'Society'],
                'tags'         => ['ai', 'culture'],
            ],
            [
                'title'        => 'The Economics of Attention',
                'excerpt'      => 'Every scroll, every click, every second of watch time is a micro-transaction. Understanding this changes how you read the internet.',
                'content'      => $this->sampleContent(),
                'status'       => 'published',
                'published_at' => now()->subDays(35),
                'cats'         => ['Technology', 'Society'],
                'tags'         => ['economics', 'culture'],
            ],
            [
                'title'    => 'Draft: Notes on Democratic Backsliding',
                'excerpt'  => 'Working notes on the mechanisms through which democracies weaken from within.',
                'content'  => $this->sampleContent(),
                'status'   => 'draft',
                'cats'     => ['Politics', 'Research'],
                'tags'     => ['democracy', 'history'],
            ],
        ];

        foreach ($posts as $data) {
            $postCats = array_map(fn($c) => $categories[$c]->id, $data['cats']);
            $postTags = array_map(fn($t) => $tags[$t]->id, $data['tags']);
            unset($data['cats'], $data['tags']);
            $post = Post::firstOrCreate(['slug' => Str::slug($data['title'])], $data);
            $post->categories()->sync($postCats);
            $post->tags()->sync($postTags);
        }

        $portfolioItems = [
            [
                'title'        => 'Project Abyss',
                'excerpt'      => 'Deep-sea infrastructure concept exploring modular subsea habitats.',
                'content'      => '<p>A speculative design project exploring permanent human presence in deep ocean environments.</p>',
                'status'       => 'published',
                'featured'     => true,
                'client'       => 'Personal Project',
                'role'         => 'Research & Concept Design',
                'project_date' => now()->subYear()->toDateString(),
                'cats'         => ['Design', 'Science'],
            ],
            [
                'title'        => 'Symmetric Spacecraft Concept',
                'excerpt'      => 'A bilateral-symmetry approach to spacecraft design for deep space missions.',
                'content'      => '<p>Exploring how bilateral symmetry can inform more reliable spacecraft architectures.</p>',
                'status'       => 'published',
                'featured'     => true,
                'client'       => 'Personal Project',
                'role'         => 'Concept Design',
                'project_date' => now()->subMonths(8)->toDateString(),
                'cats'         => ['Design', 'Science'],
            ],
            [
                'title'        => 'Democracy Index Visualization',
                'excerpt'      => 'Interactive data visualization tracking democratic health across 80 countries.',
                'content'      => '<p>A data project visualizing the relationship between institutional strength and democratic resilience.</p>',
                'status'       => 'published',
                'featured'     => true,
                'client'       => 'SpaceGaps',
                'role'         => 'Research & Design',
                'project_date' => now()->subMonths(4)->toDateString(),
                'cats'         => ['Technology', 'Politics'],
            ],
        ];

        foreach ($portfolioItems as $data) {
            $itemCats = array_map(fn($c) => $categories[$c]->id, $data['cats']);
            unset($data['cats']);
            $item = PortfolioItem::firstOrCreate(['slug' => Str::slug($data['title'])], $data);
            $item->categories()->sync($itemCats);
        }

        Paper::firstOrCreate(
            ['slug' => 'a-brief-survey-of-time-balanced-democracy'],
            [
                'title'        => 'A Brief Survey of Time-Balanced Democracy',
                'excerpt'      => 'An examination of democratic structures that account for intergenerational equity.',
                'abstract'     => 'This paper surveys proposals for extending democratic representation across time — specifically mechanisms for giving weight to future generations in present-day political decisions. Drawing on political philosophy and comparative constitutional law, we evaluate several frameworks for their feasibility and normative grounding.',
                'content'      => $this->sampleContent(),
                'status'       => 'published',
                'featured'     => true,
                'author'       => 'Admin',
                'published_at' => now()->subMonths(2),
            ]
        );
    }

    private function sampleContent(): string
    {
        return <<<HTML
<p>The question at the center of this piece is not new, but it keeps finding new urgency. Every generation believes it stands at a turning point — and every generation is, in some narrow sense, correct.</p>

<h2>The Shape of the Problem</h2>
<p>What distinguishes the current moment is not the speed of change — though that is real — but the degree to which the tools we use to think about change are themselves changing. The frame is shifting while we try to use it.</p>
<p>This creates a peculiar kind of intellectual vertigo. The concepts that served well enough yesterday — progress, growth, democracy, identity — are not wrong exactly, but they are increasingly insufficient.</p>

<h2>Finding the Gaps</h2>
<p>The most interesting intellectual work of the next decade will happen not in the established disciplines but in the spaces between them. In the gap between biology and computation. Between law and cryptography. Between individual psychology and collective behavior.</p>
<blockquote>The map is not the territory — but a bad map is worse than no map. At least with no map you know you're guessing.</blockquote>
<p>These gaps are full of half-formed ideas, borrowed concepts, and working hypotheses that haven't yet been stress-tested by real scrutiny. They are the frontier, in the old sense: rough, productive, and not yet fenced off.</p>

<h2>What Comes Next</h2>
<p>This essay doesn't have a conclusion so much as an invitation. The gaps are real and they are where the work is.</p>
HTML;
    }
}
