<?php
    $direction = \App\Support\Locale::direction();
    $locales = \App\Support\Locale::supported();
    $dashboardUrl = \App\Filament\Pages\Dashboard::getUrl();
    $loginUrl = filament()->getLoginUrl();
    $registerUrl = filament()->getRegistrationUrl();

    $features = [
        'check_in' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
        'weekly' => 'M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5',
        'streaks' => 'M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48Z',
        'calendar' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5',
        'custom' => 'M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75',
        'private' => 'M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z',
    ];

    // Static demo data for the hero preview (not the visitor's data).
    $preview = [
        ['name' => __('habits.names.exercise'), 'color' => '#f59e0b', 'days' => [1, 1, 0, 1, 1, 0, 1], 'target' => 5],
        ['name' => __('habits.names.reading'), 'color' => '#ec4899', 'days' => [1, 1, 1, 1, 0, 1, 1], 'target' => 7],
        ['name' => __('habits.names.journaling'), 'color' => '#8b5cf6', 'days' => [0, 1, 1, 0, 1, 1, 0], 'target' => 4],
    ];
    $weekDays = array_map(
        fn (\Carbon\CarbonImmutable $day): string => \App\Support\LocalCalendar::format($day, __('habits.formats.weekday_short')),
        \App\Support\Week::days(),
    );
?>
<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" dir="<?php echo e($direction); ?>" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="<?php echo e(__('habits.landing.meta_description')); ?>">

        <title><?php echo e(__('habits.brand')); ?> — <?php echo e(__('habits.landing.title')); ?></title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    </head>
    <body class="min-h-screen bg-slate-950 font-sans text-slate-100 antialiased selection:bg-emerald-400/30">
        
        <div aria-hidden="true" class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
            <div class="absolute -top-40 start-1/2 h-[36rem] w-[36rem] -translate-x-1/2 rounded-full bg-emerald-500/20 blur-3xl rtl:translate-x-1/2"></div>
            <div class="absolute top-1/3 -end-40 h-[28rem] w-[28rem] rounded-full bg-cyan-500/10 blur-3xl"></div>
        </div>

        
        <header class="sticky top-0 z-40 border-b border-white/5 bg-slate-950/70 backdrop-blur">
            <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
                <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-2.5">
                    <span class="flex size-9 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-400 to-cyan-500 text-slate-950 shadow-lg shadow-emerald-500/20">
                        <svg class="size-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0 1 12 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 0 1 3.498 1.307 4.491 4.491 0 0 1 1.307 3.497A4.49 4.49 0 0 1 21.75 12a4.49 4.49 0 0 1-1.549 3.397 4.491 4.491 0 0 1-1.307 3.497 4.491 4.491 0 0 1-3.497 1.307A4.49 4.49 0 0 1 12 21.75a4.49 4.49 0 0 1-3.397-1.549 4.49 4.49 0 0 1-3.498-1.306 4.491 4.491 0 0 1-1.307-3.498A4.49 4.49 0 0 1 2.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 0 1 1.307-3.497 4.49 4.49 0 0 1 3.497-1.307Zm7.007 6.387a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd"/></svg>
                    </span>
                    <span class="text-lg font-bold tracking-tight"><?php echo e(__('habits.brand')); ?></span>
                </a>

                <nav class="flex items-center gap-2 sm:gap-3">
                    <div class="flex items-center rounded-lg bg-white/5 p-1 text-xs font-medium" data-test="locale-switcher">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $locales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <a href="<?php echo e(route('locale.switch', $code)); ?>" lang="<?php echo e($code); ?>" hreflang="<?php echo e($code); ?>"
                               class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                   'rounded-md px-2.5 py-1 transition',
                                   'bg-white/10 text-white' => app()->getLocale() === $code,
                                   'text-slate-400 hover:text-white' => app()->getLocale() !== $code,
                               ]); ?>"><?php echo e($name); ?></a>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e($dashboardUrl); ?>" class="whitespace-nowrap rounded-xl bg-emerald-400 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-emerald-300" data-test="open-dashboard">
                            <?php echo e(__('habits.landing.open_dashboard')); ?>

                        </a>
                    <?php else: ?>
                        <a href="<?php echo e($loginUrl); ?>" class="hidden rounded-xl px-3 py-2 text-sm font-medium text-slate-300 transition hover:text-white sm:inline-block" data-test="login-link">
                            <?php echo e(__('habits.landing.login')); ?>

                        </a>
                        <a href="<?php echo e($registerUrl); ?>" class="whitespace-nowrap rounded-xl bg-emerald-400 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-emerald-300" data-test="register-link">
                            <?php echo e(__('habits.landing.register')); ?>

                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </nav>
            </div>
        </header>

        <main>
            
            <section class="mx-auto grid max-w-6xl items-center gap-12 px-4 pt-16 pb-20 sm:px-6 lg:grid-cols-2 lg:pt-24">
                <div>
                    <span class="inline-flex items-center gap-2 rounded-full border border-emerald-400/20 bg-emerald-400/10 px-3 py-1 text-xs font-medium text-emerald-300">
                        <span class="size-1.5 rounded-full bg-emerald-400"></span>
                        <?php echo e(__('habits.landing.badge')); ?>

                    </span>
                    <h1 class="mt-6 text-4xl leading-tight font-extrabold tracking-tight sm:text-5xl lg:text-6xl">
                        <span class="bg-gradient-to-r from-emerald-300 via-emerald-400 to-cyan-400 bg-clip-text text-transparent"><?php echo e(__('habits.landing.title')); ?></span>
                    </h1>
                    <p class="mt-6 max-w-xl text-lg leading-8 text-slate-400"><?php echo e(__('habits.landing.subtitle')); ?></p>

                    <div class="mt-10 flex flex-wrap items-center gap-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                            <a href="<?php echo e($dashboardUrl); ?>" class="rounded-xl bg-emerald-400 px-6 py-3 font-semibold text-slate-950 shadow-lg shadow-emerald-500/20 transition hover:bg-emerald-300"><?php echo e(__('habits.landing.open_dashboard')); ?></a>
                        <?php else: ?>
                            <a href="<?php echo e($registerUrl); ?>" class="rounded-xl bg-emerald-400 px-6 py-3 font-semibold text-slate-950 shadow-lg shadow-emerald-500/20 transition hover:bg-emerald-300"><?php echo e(__('habits.landing.register')); ?></a>
                            <a href="<?php echo e($loginUrl); ?>" class="rounded-xl border border-white/10 px-6 py-3 font-semibold text-slate-200 transition hover:border-white/20 hover:bg-white/5"><?php echo e(__('habits.landing.login')); ?></a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div class="relative">
                    <div class="absolute -inset-4 rounded-3xl bg-gradient-to-br from-emerald-400/20 to-cyan-400/10 blur-2xl" aria-hidden="true"></div>
                    <div class="relative rounded-2xl border border-white/10 bg-slate-900/80 p-5 shadow-2xl backdrop-blur sm:p-6">
                        <div class="mb-5 flex items-center justify-between">
                            <h2 class="font-semibold"><?php echo e(__('habits.landing.preview_title')); ?></h2>
                            <div class="flex gap-1.5" aria-hidden="true">
                                <span class="size-2.5 rounded-full bg-rose-400/70"></span>
                                <span class="size-2.5 rounded-full bg-amber-400/70"></span>
                                <span class="size-2.5 rounded-full bg-emerald-400/70"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-3 text-center">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [['84%', __('habits.dashboard.this_week'), 'text-emerald-400'], ['12', __('habits.dashboard.current_streak'), 'text-amber-400'], ['8.1', __('habits.dashboard.average_mood'), 'text-violet-400']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$value, $label, $color]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div class="rounded-xl border border-white/5 bg-white/5 p-3">
                                    <div class="text-2xl font-bold <?php echo e($color); ?>"><?php echo e($value); ?></div>
                                    <div class="mt-1 text-[11px] text-slate-400"><?php echo e($label); ?></div>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>

                        <div class="mt-5 space-y-3" aria-hidden="true">
                            <div class="grid grid-cols-[minmax(0,1fr)_repeat(7,1.75rem)] items-center gap-1.5 text-center text-[10px] text-slate-500">
                                <span></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $weekDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <span><?php echo e(mb_substr($day, 0, 2)); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $preview; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $habit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div class="grid grid-cols-[minmax(0,1fr)_repeat(7,1.75rem)] items-center gap-1.5">
                                    <span class="flex items-center gap-2 truncate text-sm">
                                        <span class="size-2.5 shrink-0 rounded-full" style="background-color: <?php echo e($habit['color']); ?>"></span>
                                        <?php echo e($habit['name']); ?>

                                    </span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $habit['days']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $done): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <span class="flex size-7 items-center justify-center rounded-lg border-2 text-xs font-bold <?php echo e($done ? 'text-slate-950' : 'border-white/10'); ?>"
                                              <?php if($done): ?> style="background-color: <?php echo e($habit['color']); ?>; border-color: <?php echo e($habit['color']); ?>" <?php endif; ?>>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($done): ?> ✓ <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>

                        <div class="mt-5 flex h-20 items-end gap-2" aria-hidden="true">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [2, 3, 2, 2, 2, 3, 2]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div class="flex-1 rounded-t-md <?php echo e($index === 5 ? 'bg-cyan-400' : 'bg-emerald-500/70'); ?>" style="height: <?php echo e($value * 33); ?>%"></div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>

            
            <section class="mx-auto max-w-6xl px-4 py-20 sm:px-6">
                <h2 class="text-center text-3xl font-bold tracking-tight sm:text-4xl"><?php echo e(__('habits.landing.features_title')); ?></h2>
                <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $path): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="group rounded-2xl border border-white/5 bg-white/[0.03] p-6 transition hover:border-emerald-400/30 hover:bg-white/[0.05]">
                            <span class="flex size-11 items-center justify-center rounded-xl bg-emerald-400/10 text-emerald-300 transition group-hover:bg-emerald-400/20">
                                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="<?php echo e($path); ?>" /></svg>
                            </span>
                            <h3 class="mt-5 text-lg font-semibold"><?php echo e(__("habits.landing.features.{$key}.title")); ?></h3>
                            <p class="mt-2 text-sm leading-7 text-slate-400"><?php echo e(__("habits.landing.features.{$key}.body")); ?></p>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </section>

            
            <section class="mx-auto max-w-6xl px-4 py-20 sm:px-6">
                <h2 class="text-center text-3xl font-bold tracking-tight sm:text-4xl"><?php echo e(__('habits.landing.steps_title')); ?></h2>
                <ol class="mt-12 grid gap-5 md:grid-cols-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [1, 2, 3]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <li class="rounded-2xl border border-white/5 bg-gradient-to-b from-white/[0.04] to-transparent p-6">
                            <span class="flex size-9 items-center justify-center rounded-full bg-emerald-400 font-bold text-slate-950"><?php echo e($step); ?></span>
                            <h3 class="mt-4 font-semibold"><?php echo e(__("habits.landing.steps.{$step}.title")); ?></h3>
                            <p class="mt-2 text-sm leading-7 text-slate-400"><?php echo e(__("habits.landing.steps.{$step}.body")); ?></p>
                        </li>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </ol>
            </section>

            
            <section class="mx-auto max-w-6xl px-4 pb-24 sm:px-6">
                <div class="relative overflow-hidden rounded-3xl border border-emerald-400/20 bg-gradient-to-br from-emerald-500/20 via-slate-900 to-cyan-500/10 px-6 py-14 text-center sm:px-12">
                    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl"><?php echo e(__('habits.landing.cta_title')); ?></h2>
                    <p class="mx-auto mt-4 max-w-xl text-slate-300"><?php echo e(__('habits.landing.cta_body')); ?></p>
                    <a href="<?php echo e(auth()->check() ? $dashboardUrl : $registerUrl); ?>" class="mt-8 inline-block rounded-xl bg-emerald-400 px-8 py-3 font-semibold text-slate-950 shadow-lg shadow-emerald-500/20 transition hover:bg-emerald-300">
                        <?php echo e(auth()->check() ? __('habits.landing.open_dashboard') : __('habits.landing.register')); ?>

                    </a>
                </div>
            </section>
        </main>

        <footer class="border-t border-white/5 py-8 text-center text-sm text-slate-500">
            &copy; <?php echo e(now()->year); ?> <?php echo e(__('habits.brand')); ?> · <?php echo e(__('habits.landing.footer')); ?>

        </footer>
    </body>
</html>
<?php /**PATH C:\laragon\www\HabitFlow\resources\views/welcome.blade.php ENDPATH**/ ?>