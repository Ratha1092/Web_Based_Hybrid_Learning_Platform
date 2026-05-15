{{-- resources/views/filament/pages/dashboard.blade.php --}}

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<!-- <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,600&family=Sora:wght@300;400;500;600&display=swap" rel="stylesheet"> -->
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
/* Reset & base */
.db-root *, .db-root *::before, .db-root *::after { box-sizing: border-box; }
.db-root {
    font-family: 'Sora', system-ui, sans-serif;
    color: rgba(255,255,255,0.85);
    padding: 0 0 48px 0;
    min-height: 100vh;
}

/* Light mode text color */
html:not(.dark) .db-root {
    color: rgba(0,0,0,0.85);
}

/* CSS Variables */
.db-root {
    --gold:      #d4a017;
    --gold-lt:   #e8c44a;
    --gold-dk:   #b8860b;
    --ink-900:   #030812;
    --ink-800:   #080f1a;
    --ink-700:   #0d1625;
    --ink-600:   #111e30;
    --ink-500:   #1a2840;
    --border:    rgba(212,160,23,0.12);
    --border-w:  rgba(255,255,255,0.06);
    --text-dim:  rgba(255,255,255,0.38);
    --text-mute: rgba(255,255,255,0.2);
    --green:     #34d399;
    --red:       #f87171;
    --blue:      #60a5fa;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* Light mode variables */
html:not(.dark) .db-root {
    --gold:      #b8860b;
    --gold-lt:   #d4a017;
    --gold-dk:   #8b6914;
    --ink-900:   #f8f8f8;
    --ink-800:   #ffffff;
    --ink-700:   #ffffff;
    --ink-600:   #f3f4f6;
    --ink-500:   #e5e7eb;
    --border:    rgba(212,160,23,0.15);
    --border-w:  rgba(0,0,0,0.1);
    --text-dim:  rgba(0,0,0,0.65);
    --text-mute: rgba(0,0,0,0.45);
    --green:     #059669;
    --red:       #dc2626;
    --blue:      #2563eb;
}

/* Page header */
.db-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 32px;
    padding-bottom: 24px;
    border-bottom: 1px solid var(--border-w);
}
.db-header-title {
    font-family: 'Manrope', system-ui, sans-serif;
    font-size: 36px;
    font-weight: 700;
    line-height: 1.1;
    letter-spacing: -0.01em;
    color: rgba(255,255,255,0.94);
}

/* Light mode header title */
html:not(.dark) .db-header-title {
    color: rgba(0,0,0,0.94);
}

/* Light mode stat card shadow */
html:not(.dark) .db-stat-card {
    box-shadow: 0 2px 8px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.06) !important;
    border-color: rgba(0,0,0,0.1) !important;
    background: #ffffff !important;
}

html:not(.dark) .db-stat-card:hover {
    box-shadow: 0 8px 24px rgba(0,0,0,0.12), 0 2px 4px rgba(0,0,0,0.08) !important;
    border-color: rgba(212,160,23,0.3) !important;
}
.db-header-title span {
    background: linear-gradient(90deg, var(--gold-lt), var(--gold));
    -webkit-background-clip: text; background-clip: text;
    -webkit-text-fill-color: transparent;
}
.db-header-sub {
    font-size: 12.5px;
    color: var(--text-dim);
    margin-top: 4px;
}
.db-date-badge {
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(212,160,23,0.06);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 8px 16px;
    font-size: 12px;
    color: rgba(212,160,23,0.7);
    font-weight: 500;
    letter-spacing: 0.04em;
}
.db-dot-green {
    width: 7px; height: 7px; border-radius: 50%;
    background: var(--green);
    box-shadow: 0 0 8px var(--green);
    flex-shrink: 0;
    animation: dbPulse 2s ease-in-out infinite;
}
@keyframes dbPulse { 0%,100%{opacity:1} 50%{opacity:.5} }

/* Stat cards row */
.db-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 24px;
}
@media (max-width: 1100px) { .db-stats { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 580px)  { .db-stats { grid-template-columns: 1fr; } }

.db-stat-card {
    position: relative;
    background: var(--ink-700);
    border: 1px solid var(--border-w);
    border-radius: 18px;
    padding: 22px 22px 20px;
    overflow: hidden;
    transition: border-color .25s, transform .2s, box-shadow .25s;
    opacity: 0;
    animation: dbFadeUp .6s cubic-bezier(.16,1,.3,1) forwards;
}
.db-stat-card:nth-child(1) { animation-delay: .05s; }
.db-stat-card:nth-child(2) { animation-delay: .12s; }
.db-stat-card:nth-child(3) { animation-delay: .19s; }
.db-stat-card:nth-child(4) { animation-delay: .26s; }
.db-stat-card:hover {
    border-color: rgba(212,160,23,0.25);
    transform: translateY(-2px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.4), 0 0 0 1px rgba(212,160,23,0.12);
}
@keyframes dbFadeUp { 0%{opacity:0;transform:translateY(18px)} 100%{opacity:1;transform:translateY(0)} }

/* Card accent glow top */
.db-stat-card::before {
    content: '';
    position: absolute;
    top: 0; left: 20%; right: 20%; height: 1px;
    background: linear-gradient(90deg, transparent, rgba(212,160,23,0.4), transparent);
}

.db-stat-icon {
    width: 40px; height: 40px; border-radius: 11px;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 16px; font-size: 18px;
}
.db-stat-icon.gold  { background: rgba(212,160,23,0.12); color: var(--gold); }
.db-stat-icon.green { background: rgba(52,211,153,0.1);  color: var(--green); }
.db-stat-icon.blue  { background: rgba(96,165,250,0.1);  color: var(--blue); }
.db-stat-icon.red   { background: rgba(248,113,113,0.1); color: var(--red); }

.db-stat-val {
    font-family: 'Manrope', system-ui, sans-serif;
    font-size: 34px;
    font-weight: 800;
    line-height: 1;
    color: rgba(255,255,255,0.92);
    letter-spacing: -0.02em;
}

/* Light mode stat value */
html:not(.dark) .db-stat-val {
    color: rgba(0,0,0,0.92);
}
.db-stat-label {
    font-size: 11px;
    font-weight: 500;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: var(--text-dim);
    margin-top: 5px;
}
.db-stat-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 10.5px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 20px;
    margin-top: 12px;
}
.db-stat-badge.up   { background: rgba(52,211,153,0.1); color: var(--green); }
.db-stat-badge.warn { background: rgba(248,113,113,0.1); color: var(--red); }
.db-stat-badge.neu  { background: rgba(212,160,23,0.1);  color: var(--gold); }

/* Card shimmer bg */
.db-stat-bg {
    position: absolute;
    right: -20px; bottom: -20px;
    width: 90px; height: 90px;
    border-radius: 50%;
    filter: blur(30px);
    pointer-events: none;
    opacity: .35;
}
.bg-gold  { background: var(--gold); }
.bg-green { background: var(--green); }
.bg-blue  { background: var(--blue); }
.bg-red   { background: var(--red); }

/* Two-col bottom layout */
.db-bottom {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 14px;
    margin-bottom: 14px;
}
@media (max-width: 900px) { .db-bottom { grid-template-columns: 1fr; } }

/* Panel (shared card style) */
.db-panel {
    background: var(--ink-700);
    border: 1px solid var(--border-w);
    border-radius: 18px;
    overflow: hidden;
    opacity: 0;
    animation: dbFadeUp .6s .3s cubic-bezier(.16,1,.3,1) forwards;
}
.db-panel-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 22px 16px;
    border-bottom: 1px solid var(--border-w);
}
.db-panel-title {
    font-size: 13px;
    font-weight: 600;
    letter-spacing: .04em;
    color: rgba(255,255,255,0.75);
}

/* Light mode panel title */
html:not(.dark) .db-panel-title {
    color: rgba(0,0,0,0.75);
}
.db-panel-action {
    font-size: 11px;
    color: rgba(212,160,23,0.65);
    text-decoration: none;
    font-weight: 500;
    transition: color .2s;
}
.db-panel-action:hover { color: var(--gold-lt); }

/* Table rows */
.db-table-row {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 13px 22px;
    border-bottom: 1px solid rgba(255,255,255,0.04);
    transition: background .18s;
}
.db-table-row:last-child { border-bottom: none; }
.db-table-row:hover { background: rgba(255,255,255,0.025); }

/* Light mode mini card shadow */
html:not(.dark) .db-mini-card {
    box-shadow: 0 2px 8px rgba(0,0,0,0.08) !important;
    border-color: rgba(0,0,0,0.1) !important;
    background: #ffffff !important;
}

html:not(.dark) .db-mini-card:hover {
    box-shadow: 0 6px 16px rgba(0,0,0,0.1) !important;
    border-color: rgba(212,160,23,0.25) !important;
}

/* Light mode panel shadow */
html:not(.dark) .db-panel {
    box-shadow: 0 2px 8px rgba(0,0,0,0.08) !important;
    border-color: rgba(0,0,0,0.1) !important;
    background: #ffffff !important;
}

/* Light mode verify panel shadow */
html:not(.dark) .db-verify-panel {
    box-shadow: 0 2px 8px rgba(0,0,0,0.08) !important;
    border-color: rgba(0,0,0,0.1) !important;
    background: #ffffff !important;
}

/* Light mode table row hover */
html:not(.dark) .db-table-row:hover { background: rgba(0,0,0,0.03); }

.db-avatar {
    width: 36px; height: 36px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-family: 'Manrope', system-ui, sans-serif;
    font-size: 16px; font-weight: 700; color: #030812;
    flex-shrink: 0;
    background: linear-gradient(135deg, var(--gold-lt), var(--gold-dk));
}

/* Light mode avatar */
html:not(.dark) .db-avatar {
    color: #ffffff;
}

/* Light mode borders */
html:not(.dark) .db-header {
    border-bottom-color: rgba(0,0,0,0.08) !important;
}

html:not(.dark) .db-date-badge {
    background: rgba(184,134,11,0.08) !important;
    border-color: rgba(184,134,11,0.25) !important;
    color: rgba(184,134,11,0.8) !important;
}

html:not(.dark) .db-panel-head {
    border-bottom-color: rgba(0,0,0,0.08) !important;
}

/* Light mode text in panels - target all divs with color to make them dark */
html:not(.dark) .db-panel div,
html:not(.dark) .db-panel span {
    color: rgba(0,0,0,0.75) !important;
}

html:not(.dark) .db-verify-panel div,
html:not(.dark) .db-verify-panel span {
    color: rgba(0,0,0,0.75) !important;
}

/* Light mode borders in panels */
html:not(.dark) .db-panel {
    border-color: rgba(0,0,0,0.1) !important;
}

html:not(.dark) .db-panel > div[style*="border-top"],
html:not(.dark) .db-panel > div[style*="border-bottom"] {
    border-color: rgba(0,0,0,0.1) !important;
}

/* Light mode table row borders */
html:not(.dark) .db-table-row {
    border-bottom-color: rgba(0,0,0,0.08) !important;
}

/* Light mode spark bar colors */
html:not(.dark) .db-spark-bar {
    background: rgba(184,134,11,0.15) !important;
}

html:not(.dark) .db-spark-bar:hover {
    background: rgba(184,134,11,0.35) !important;
}
.db-avatar.sm {
    width: 30px; height: 30px; border-radius: 8px;
    font-size: 13px;
}

.db-row-main { flex: 1; min-width: 0; }
.db-row-name {
    font-size: 13px;
    font-weight: 500;
    color: rgba(255,255,255,0.82);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Light mode row name */
html:not(.dark) .db-row-name {
    color: rgba(0,0,0,0.82);
}
.db-row-meta {
    font-size: 11px;
    color: var(--text-dim);
    margin-top: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.db-badge {
    font-size: 10.5px;
    font-weight: 600;
    padding: 3px 9px;
    border-radius: 20px;
    white-space: nowrap;
    flex-shrink: 0;
}
.db-badge.published { background: rgba(52,211,153,0.1); color: var(--green); }
.db-badge.draft     { background: rgba(212,160,23,0.1);  color: var(--gold); }
.db-badge.pending   { background: rgba(248,113,113,0.1); color: var(--red); }
.db-badge.student   { background: rgba(96,165,250,0.1);  color: var(--blue); }
.db-badge.instructor{ background: rgba(212,160,23,0.1);  color: var(--gold); }
.db-badge.admin     { background: rgba(167,139,250,0.12); color: #a78bfa; }

/* Mini stats row (3-up) */
.db-mini-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
    margin-bottom: 14px;
}
@media (max-width: 700px) { .db-mini-row { grid-template-columns: 1fr; } }

.db-mini-card {
    background: var(--ink-700);
    border: 1px solid var(--border-w);
    border-radius: 18px;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 14px;
    opacity: 0;
    animation: dbFadeUp .6s cubic-bezier(.16,1,.3,1) forwards;
    transition: border-color .25s, transform .2s;
}
.db-mini-card:nth-child(1) { animation-delay: .32s; }
.db-mini-card:nth-child(2) { animation-delay: .38s; }
.db-mini-card:nth-child(3) { animation-delay: .44s; }
.db-mini-card:hover { border-color: rgba(212,160,23,0.22); transform: translateY(-1px); }

.db-mini-icon {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; flex-shrink: 0;
}
.db-mini-val {
    font-family: 'Manrope', system-ui, sans-serif;
    font-size: 28px; font-weight: 700;
    color: rgba(255,255,255,0.9);
    line-height: 1;
}

/* Light mode mini value */
html:not(.dark) .db-mini-val {
    color: rgba(0,0,0,0.9);
}
.db-mini-label {
    font-size: 11px; font-weight: 500;
    letter-spacing: .08em; text-transform: uppercase;
    color: var(--text-dim);
    margin-top: 3px;
}

/* Trend sparkline (CSS bars) */
.db-spark {
    display: flex;
    align-items: flex-end;
    gap: 4px;
    height: 40px;
    padding: 0 22px 14px;
}
.db-spark-bar {
    flex: 1;
    background: rgba(212,160,23,0.2);
    border-radius: 3px 3px 0 0;
    min-height: 4px;
    transition: background .2s;
    position: relative;
}
.db-spark-bar:hover { background: rgba(212,160,23,0.55); }
.db-spark-bar.peak { background: linear-gradient(to top, var(--gold-dk), var(--gold-lt)); }

/* Pending verifications panel */
.db-verify-panel {
    background: var(--ink-700);
    border: 1px solid var(--border-w);
    border-radius: 18px;
    overflow: hidden;
    opacity: 0;
    animation: dbFadeUp .6s .38s cubic-bezier(.16,1,.3,1) forwards;
}

/* Action buttons */
.db-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 11.5px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: opacity .2s, transform .15s;
    text-decoration: none;
}
.db-btn:hover { opacity: .85; transform: translateY(-1px); }
.db-btn.approve { background: rgba(52,211,153,0.15); color: var(--green); }
.db-btn.view    { background: rgba(212,160,23,0.12);  color: var(--gold); }

/* Divider */
.db-divider {
    height: 1px;
    background: var(--border-w);
    margin: 4px 0;
}

/* Empty state */
.db-empty {
    padding: 32px;
    text-align: center;
    color: var(--text-mute);
    font-size: 12.5px;
}

/* Responsive full-width panels */
.db-full {
    opacity: 0;
    animation: dbFadeUp .6s .45s cubic-bezier(.16,1,.3,1) forwards;
}
</style>

<div class="db-root">

    {{-- HEADER --}}
    <div class="db-header">
        <div>
            <h1 class="db-header-title">
                Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }},
                <span>{{ auth()->user()->name ?? 'Admin' }}</span> 👋
            </h1>
            <p class="db-header-sub">Here's what's happening on your platform today.</p>
        </div>
        <div class="db-date-badge">
            <div class="db-dot-green"></div>
            {{ now()->format('l, F j, Y') }}
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="db-stats">

        {{-- Students --}}
        <div class="db-stat-card">
            <div class="db-stat-bg bg-gold"></div>
            <div class="db-stat-icon gold">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div class="db-stat-val">{{ number_format($totalStudents) }}</div>
            <div class="db-stat-label">Total Students</div>
            <div class="db-stat-badge up">↑ {{ $newStudentsThisMonth }} this month</div>
        </div>

        {{-- Courses --}}
        <div class="db-stat-card">
            <div class="db-stat-bg bg-blue"></div>
            <div class="db-stat-icon blue">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div class="db-stat-val">{{ number_format($totalCourses) }}</div>
            <div class="db-stat-label">Total Courses</div>
            <div class="db-stat-badge neu">{{ $newCoursesThisMonth }} added this month</div>
        </div>

        {{-- Instructors --}}
        <div class="db-stat-card">
            <div class="db-stat-bg bg-green"></div>
            <div class="db-stat-icon green">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div class="db-stat-val">{{ number_format($totalInstructors) }}</div>
            <div class="db-stat-label">Instructors</div>
            <div class="db-stat-badge up">Active on platform</div>
        </div>

        {{-- Pending Verifications --}}
        <div class="db-stat-card">
            <div class="db-stat-bg bg-red"></div>
            <div class="db-stat-icon red">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div class="db-stat-val">{{ number_format($pendingVerifications) }}</div>
            <div class="db-stat-label">Pending Verifications</div>
            <div class="db-stat-badge {{ $pendingVerifications > 0 ? 'warn' : 'up' }}">
                {{ $pendingVerifications > 0 ? 'Requires attention' : 'All clear' }}
            </div>
        </div>

    </div>

    {{-- MINI STATS (Sections, Lessons, Users) --}}
    <div class="db-mini-row">

        <div class="db-mini-card">
            <div class="db-mini-icon" style="background:rgba(96,165,250,0.1);color:#60a5fa;">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
            </div>
            <div>
                <div class="db-mini-val">{{ number_format($totalSections) }}</div>
                <div class="db-mini-label">Sections</div>
            </div>
        </div>

        <div class="db-mini-card">
            <div class="db-mini-icon" style="background:rgba(167,139,250,0.1);color:#a78bfa;">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="db-mini-val">{{ number_format($totalLessons) }}</div>
                <div class="db-mini-label">Lessons</div>
            </div>
        </div>

        <div class="db-mini-card">
            <div class="db-mini-icon" style="background:rgba(52,211,153,0.1);color:#34d399;">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div>
                <div class="db-mini-val">{{ number_format($totalStudents + $totalInstructors) }}</div>
                <div class="db-mini-label">Total Users</div>
            </div>
        </div>

    </div>

    {{-- MAIN 2-COL --}}
    <div class="db-bottom">

        {{-- Recent Courses --}}
        <div class="db-panel">
            <div class="db-panel-head">
                <span class="db-panel-title">
                    <svg style="display:inline;vertical-align:-3px;margin-right:7px;" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="rgba(212,160,23,0.7)" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Recent Courses
                </span>
                <a href="{{ route('filament.admin.resources.courses.index') }}" class="db-panel-action">View all →</a>
            </div>

            @forelse($recentCourses as $course)
            <div class="db-table-row">
                <div class="db-avatar">{{ strtoupper(substr($course->title ?? 'C', 0, 1)) }}</div>
                <div class="db-row-main">
                    <div class="db-row-name">{{ $course->title ?? 'Untitled Course' }}</div>
                    <div class="db-row-meta">
                        by {{ $course->user->name ?? 'Unknown' }} &nbsp;·&nbsp; {{ $course->created_at->diffForHumans() }}
                    </div>
                </div>
                <span class="db-badge {{ $course->is_published ?? false ? 'published' : 'draft' }}">
                    {{ $course->is_published ?? false ? 'Published' : 'Draft' }}
                </span>
            </div>
            @empty
            <div class="db-empty">No courses yet.</div>
            @endforelse
        </div>

        {{-- Recent Users --}}
        <div class="db-panel" style="animation-delay:.36s;">
            <div class="db-panel-head">
                <span class="db-panel-title">
                    <svg style="display:inline;vertical-align:-3px;margin-right:7px;" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="rgba(212,160,23,0.7)" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Recent Users
                </span>
                <a href="{{ route('filament.admin.resources.users.index') }}" class="db-panel-action">View all →</a>
            </div>

            @forelse($recentUsers as $user)
            <div class="db-table-row">
                <div class="db-avatar sm">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</div>
                <div class="db-row-main">
                    <div class="db-row-name">{{ $user->name ?? 'Unknown' }}</div>
                    <div class="db-row-meta">{{ $user->email }} &nbsp;·&nbsp; {{ $user->created_at->diffForHumans() }}</div>
                </div>
                <span class="db-badge {{ $user->role ?? 'student' }}">{{ ucfirst($user->role ?? 'student') }}</span>
            </div>
            @empty
            <div class="db-empty">No users yet.</div>
            @endforelse
        </div>

    </div>

    {{-- PENDING VERIFICATIONS + TREND --}}
    <div class="db-bottom db-full">

        {{-- Student Trend (sparkline) --}}
        <div class="db-verify-panel">
            <div class="db-panel-head">
                <span class="db-panel-title">
                    <svg style="display:inline;vertical-align:-3px;margin-right:7px;" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="rgba(212,160,23,0.7)" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    Student Registrations — Last 6 Months
                </span>
            </div>

            @php
                $maxVal = $studentTrend->max('count') ?: 1;
            @endphp

            <div style="padding: 20px 22px 8px;">
                <div style="display:flex; align-items:flex-end; gap:10px; height:80px;">
                    @foreach($studentTrend as $point)
                    @php
                        $h = max(6, round(($point['count'] / $maxVal) * 72));
                        $isPeak = $point['count'] === $studentTrend->max('count');
                    @endphp
                    <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:6px;">
                        <span style="font-size:11px;color:rgba(255,255,255,0.38);">{{ $point['count'] }}</span>
                        <div style="
                            width:100%;
                            height:{{ $h }}px;
                            border-radius:5px 5px 0 0;
                            background: {{ $isPeak
                                ? 'linear-gradient(to top, #b8860b, #e8c44a)'
                                : 'rgba(212,160,23,0.18)' }};
                            transition: background .2s;
                        "></div>
                    </div>
                    @endforeach
                </div>
                <div style="display:flex; gap:10px; margin-top:8px;">
                    @foreach($studentTrend as $point)
                    <div style="flex:1;text-align:center;font-size:10px;color:rgba(255,255,255,0.28);letter-spacing:.04em;">
                        {{ $point['month'] }}
                    </div>
                    @endforeach
                </div>
            </div>

            <div style="padding:16px 22px 20px; border-top:1px solid rgba(255,255,255,0.05); margin-top:4px;">
                <div style="display:flex; gap:24px;">
                    <div>
                        <div style="font-family:'Manrope',Georgia,serif;font-size:26px;font-weight:700;color:rgba(255,255,255,0.9);">
                            {{ $studentTrend->sum('count') }}
                        </div>
                        <div style="font-size:10.5px;text-transform:uppercase;letter-spacing:.09em;color:rgba(255,255,255,0.35);margin-top:2px;">New over 6 months</div>
                    </div>
                    <div>
                        <div style="font-family:'Manrope',Georgia,serif;font-size:26px;font-weight:700;color:rgba(255,255,255,0.9);">
                            {{ $newStudentsThisMonth }}
                        </div>
                        <div style="font-size:10.5px;text-transform:uppercase;letter-spacing:.09em;color:rgba(255,255,255,0.35);margin-top:2px;">This month</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pending Instructor Verifications --}}
        <div class="db-verify-panel" style="animation-delay:.5s;">
            <div class="db-panel-head">
                <span class="db-panel-title">
                    <svg style="display:inline;vertical-align:-3px;margin-right:7px;" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="rgba(248,113,113,0.8)" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Pending Verifications
                </span>
                <a href="{{ route('filament.admin.resources.instructor-verifications.index') }}" class="db-panel-action">View all →</a>
            </div>

            @forelse($pendingInstructors as $instructor)
            <div class="db-table-row">
                <div class="db-avatar sm">{{ strtoupper(substr($instructor->name ?? 'I', 0, 1)) }}</div>
                <div class="db-row-main">
                    <div class="db-row-name">{{ $instructor->name }}</div>
                    <div class="db-row-meta">{{ $instructor->email }} &nbsp;·&nbsp; {{ $instructor->created_at->diffForHumans() }}</div>
                </div>
                <a href="{{ route('filament.admin.resources.instructor-verifications.index') }}" class="db-btn view">Review</a>
            </div>
            @empty
            <div class="db-empty">
                <svg style="margin:0 auto 8px;display:block;" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="rgba(52,211,153,0.5)" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                All instructors verified!
            </div>
            @endforelse
        </div>

    </div>

    {{-- PAYMENT & FINANCE SECTION --}}
    <div style="margin-top: 32px; padding-top: 32px; border-top: 1px solid var(--border-w);">
        <div style="margin-bottom: 24px;">
            <h2 style="font-family:'Manrope',Georgia,serif; font-size:24px; font-weight:700; color:rgba(255,255,255,0.92); margin-bottom:4px;">
                💳 Payments & Finance
            </h2>
            <p style="font-size:12.5px; color:var(--text-dim);">Monitor revenue, orders, and instructor payouts.</p>
        </div>

        {{-- PAYMENT STATS --}}
        <div class="db-stats" style="opacity: 0; animation: dbFadeUp .6s .35s cubic-bezier(.16,1,.3,1) forwards;">

            {{-- Total Revenue --}}
            <div class="db-stat-card">
                <div class="db-stat-bg bg-gold"></div>
                <div class="db-stat-icon gold">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="db-stat-val">${{ number_format($totalRevenue, 0) }}</div>
                <div class="db-stat-label">Total Revenue</div>
                <div class="db-stat-badge up">+${{ number_format($revenueThisMonth, 0) }} this month</div>
            </div>

            {{-- Total Orders --}}
            <div class="db-stat-card">
                <div class="db-stat-bg bg-blue"></div>
                <div class="db-stat-icon blue">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="db-stat-val">{{ number_format($totalOrders) }}</div>
                <div class="db-stat-label">Total Orders</div>
                <div class="db-stat-badge neu">{{ $ordersThisMonth }} this month</div>
            </div>

            {{-- Completed Payments --}}
            <div class="db-stat-card">
                <div class="db-stat-bg bg-green"></div>
                <div class="db-stat-icon green">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="db-stat-val">{{ number_format($completedPayments) }}</div>
                <div class="db-stat-label">Completed Payments</div>
                <div class="db-stat-badge up">Success rate: {{ $totalOrders > 0 ? round(($completedPayments / $totalOrders) * 100) : 0 }}%</div>
            </div>

            {{-- Pending Payments --}}
            <div class="db-stat-card">
                <div class="db-stat-bg bg-red"></div>
                <div class="db-stat-icon red">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="db-stat-val">{{ number_format($pendingPayments) }}</div>
                <div class="db-stat-label">Pending Payments</div>
                <div class="db-stat-badge {{ $pendingPayments > 0 ? 'warn' : 'up' }}">
                    {{ $pendingPayments > 0 ? 'Requires attention' : 'All processed' }}
                </div>
            </div>

        </div>

        {{-- FINANCE MINI STATS --}}
        <div class="db-mini-row" style="margin-top: 24px; opacity: 0; animation: dbFadeUp .6s .4s cubic-bezier(.16,1,.3,1) forwards;">

            <div class="db-mini-card">
                <div class="db-mini-icon" style="background:rgba(52,211,153,0.1);color:#34d399;">
                    <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="db-mini-val">${{ number_format($totalInstructorBalance, 0) }}</div>
                    <div class="db-mini-label">Instructor Balance</div>
                </div>
            </div>

            <div class="db-mini-card">
                <div class="db-mini-icon" style="background:rgba(96,165,250,0.1);color:#60a5fa;">
                    <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <div>
                    <div class="db-mini-val">${{ number_format($totalPendingBalance, 0) }}</div>
                    <div class="db-mini-label">Pending Payouts</div>
                </div>
            </div>

            <div class="db-mini-card">
                <div class="db-mini-icon" style="background:rgba(167,139,250,0.1);color:#a78bfa;">
                    <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="db-mini-val">{{ number_format($failedPayments) }}</div>
                    <div class="db-mini-label">Failed Payments</div>
                </div>
            </div>

        </div>

        {{-- RECENT ORDERS & PAYMENTS --}}
        <div class="db-bottom" style="margin-top: 24px; opacity: 0; animation: dbFadeUp .6s .45s cubic-bezier(.16,1,.3,1) forwards;">

            {{-- Recent Orders --}}
            <div class="db-panel">
                <div class="db-panel-head">
                    <span class="db-panel-title">
                        <svg style="display:inline;vertical-align:-3px;margin-right:7px;" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="rgba(212,160,23,0.7)" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Recent Orders
                    </span>
                    <a href="{{ route('filament.admin.resources.orders.index') }}" class="db-panel-action">View all →</a>
                </div>

                @forelse($recentOrders as $order)
                @php
                    $paymentStatus = $order->payment_status?->value ?? $order->payment_status;
                @endphp
                <div class="db-table-row">
                    <div class="db-avatar">{{ strtoupper(substr($order->order_number, 0, 3)) }}</div>
                    <div class="db-row-main">
                        <div class="db-row-name">{{ $order->order_number }}</div>
                        <div class="db-row-meta">
                            {{ $order->user->name ?? 'Unknown' }} &nbsp;·&nbsp; ${{ number_format($order->final_amount, 2) }} &nbsp;·&nbsp; {{ $order->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <span class="db-badge {{ $paymentStatus === 'paid' ? 'published' : ($paymentStatus === 'pending' ? 'pending' : 'draft') }}">
                        {{ str($paymentStatus)->headline() }}
                    </span>
                </div>
                @empty
                <div class="db-empty">No orders yet.</div>
                @endforelse
            </div>

            {{-- Recent Payments --}}
            <div class="db-panel" style="animation-delay:.36s;">
                <div class="db-panel-head">
                    <span class="db-panel-title">
                        <svg style="display:inline;vertical-align:-3px;margin-right:7px;" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="rgba(212,160,23,0.7)" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h10M3 20h18M5 7l1-2h8l1 2M5 15l1-2h4l1 2M5 20l1-2h4l1 2"/>
                        </svg>
                        Recent Payments
                    </span>
                    <a href="{{ route('filament.admin.resources.payments.index') }}" class="db-panel-action">View all →</a>
                </div>

                @forelse($recentPayments as $payment)
                @php
                    $paymentGateway = $payment->payment_gateway?->value ?? $payment->payment_gateway;
                    $paymentStatus = $payment->status?->value ?? $payment->status;
                @endphp
                <div class="db-table-row">
                    <div class="db-avatar">{{ strtoupper(substr($paymentGateway, 0, 2)) }}</div>
                    <div class="db-row-main">
                        <div class="db-row-name">{{ $payment->order->order_number ?? 'N/A' }}</div>
                        <div class="db-row-meta">
                            {{ ucfirst($paymentGateway) }} &nbsp;·&nbsp; ${{ number_format($payment->amount, 2) }} &nbsp;·&nbsp; {{ $payment->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <span class="db-badge {{ in_array($paymentStatus, ['paid', 'completed'], true) ? 'published' : ($paymentStatus === 'pending' ? 'pending' : 'draft') }}">
                        {{ str($paymentStatus)->headline() }}
                    </span>
                </div>
                @empty
                <div class="db-empty">No payments yet.</div>
                @endforelse
            </div>

        </div>

        {{-- REVENUE TREND --}}
        <div class="db-verify-panel" style="margin-top: 24px; opacity: 0; animation: dbFadeUp .6s .52s cubic-bezier(.16,1,.3,1) forwards;">
            <div class="db-panel-head">
                <span class="db-panel-title">
                    <svg style="display:inline;vertical-align:-3px;margin-right:7px;" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="rgba(212,160,23,0.7)" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    Revenue & Orders — Last 6 Months
                </span>
            </div>

            @php
                $maxRevenue = collect($revenueTrend)->max('revenue') ?: 1;
                $maxOrders = collect($revenueTrend)->max('orders') ?: 1;
            @endphp

            <div style="padding: 20px 22px 8px;">
                <div style="display:flex; align-items:flex-end; gap:10px; height:100px;">
                    @foreach($revenueTrend as $point)
                    @php
                        $h1 = $point['revenue'] > 0 ? max(6, round(($point['revenue'] / max($maxRevenue, 1)) * 85)) : 0;
                        $h2 = $point['orders'] > 0 ? max(2, round(($point['orders'] / max($maxOrders, 1)) * 40)) : 0;
                    @endphp
                    <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:4px; position:relative;">
                        <div style="display:flex; gap:2px; width:100%; align-items:flex-end; height:85px;">
                            <div style="
                                flex:1;
                                height:{{ $h1 }}px;
                                border-radius:3px 3px 0 0;
                                background: rgba(212,160,23,0.25);
                                position:relative;
                            "></div>
                            <div style="
                                flex:1;
                                height:{{ $h2 }}px;
                                border-radius:3px 3px 0 0;
                                background: rgba(96,165,250,0.25);
                            "></div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div style="display:flex; gap:10px; margin-top:8px;">
                    @foreach($revenueTrend as $point)
                    <div style="flex:1;text-align:center;font-size:10px;color:rgba(255,255,255,0.28);letter-spacing:.04em;">
                        {{ $point['month'] }}
                    </div>
                    @endforeach
                </div>
            </div>

            <div style="padding:16px 22px 20px; border-top:1px solid rgba(255,255,255,0.05); margin-top:4px; display:flex; gap:24px;">
                <div>
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
                        <div style="width:12px; height:12px; border-radius:3px; background:rgba(212,160,23,0.4);"></div>
                        <span style="font-size:11px; color:rgba(255,255,255,0.65);">Revenue</span>
                    </div>
                    <div style="font-family:'Manrope',Georgia,serif;font-size:24px;font-weight:700;color:rgba(255,255,255,0.9);">
                        ${{ number_format(collect($revenueTrend)->sum('revenue'), 0) }}
                    </div>
                    <div style="font-size:10.5px;text-transform:uppercase;letter-spacing:.09em;color:rgba(255,255,255,0.35);margin-top:2px;">6 months</div>
                </div>
                <div>
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
                        <div style="width:12px; height:12px; border-radius:3px; background:rgba(96,165,250,0.4);"></div>
                        <span style="font-size:11px; color:rgba(255,255,255,0.65);">Orders</span>
                    </div>
                    <div style="font-family:'Manrope',Georgia,serif;font-size:24px;font-weight:700;color:rgba(255,255,255,0.9);">
                        {{ collect($revenueTrend)->sum('orders') }}
                    </div>
                    <div style="font-size:10.5px;text-transform:uppercase;letter-spacing:.09em;color:rgba(255,255,255,0.35);margin-top:2px;">6 months</div>
                </div>
            </div>
        </div>

    </div>

</div>
