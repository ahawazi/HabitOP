<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'progress',
    'href' => null,
    'variant' => 'calendar',
    'outside' => false,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'progress',
    'href' => null,
    'variant' => 'calendar',
    'outside' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    /** @var \App\Data\DailyProgress $progress */
    $level = $progress->level();

    // Each level differs in lightness as well as hue, so the states remain
    // distinguishable without colour perception. The accessible label below always
    // states the percentage outright.
    $fill = match (true) {
        $progress->isFuture => 'bg-white dark:bg-white/5 border-dashed border-gray-300 dark:border-gray-700',
        $level === 0 => 'bg-gray-100 border-gray-200 dark:bg-white/5 dark:border-white/10',
        $level === 1 => 'bg-amber-200 border-amber-300 dark:bg-amber-500/30 dark:border-amber-500/40',
        $level === 2 => 'bg-sky-300 border-sky-400 dark:bg-sky-500/40 dark:border-sky-400/50',
        default => 'bg-emerald-500 border-emerald-600 dark:bg-emerald-500/70 dark:border-emerald-400/60',
    };

    $date = $progress->date->translatedFormat(__('habits.formats.long_date'));

    $label = $progress->isFuture
        ? __('habits.day_summary.future', ['date' => $date])
        : ($progress->moodScore !== null
            ? __('habits.day_summary.with_mood', [
                'date' => $date,
                'completed' => $progress->completedCount,
                'total' => $progress->totalCount,
                'percentage' => $progress->percentage,
                'mood' => $progress->moodScore,
                'max' => \App\Models\DailyCheckIn::MOOD_MAX,
            ])
            : __('habits.day_summary.without_mood', [
                'date' => $date,
                'completed' => $progress->completedCount,
                'total' => $progress->totalCount,
                'percentage' => $progress->percentage,
            ]));

    if ($progress->isToday) {
        $label = __('habits.calendar.today_badge').' — '.$label;
    }

    $tag = $href ? 'a' : 'span';
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant === 'heatmap'): ?>
    <<?php echo e($tag); ?>

        <?php if($href): ?> href="<?php echo e($href); ?>" wire:navigate <?php endif; ?>
        title="<?php echo e($label); ?>"
        aria-label="<?php echo e($label); ?>"
        data-test="year-day"
        data-date="<?php echo e($progress->date->toDateString()); ?>"
        data-level="<?php echo e($progress->isFuture ? 'future' : $level); ?>"
        class="<?php echo \Illuminate\Support\Arr::toCssClasses([
            'block size-3.5 rounded-[3px] border transition sm:size-4',
            'hover:scale-125 hover:ring-2 hover:ring-gray-500 dark:hover:ring-gray-300' => $href,
            $fill,
            'ring-2 ring-gray-900 ring-offset-1 ring-offset-white dark:ring-white dark:ring-offset-gray-900' => $progress->isToday,
        ]); ?>"
    ></<?php echo e($tag); ?>>
<?php else: ?>
    <<?php echo e($tag); ?>

        <?php if($href): ?> href="<?php echo e($href); ?>" wire:navigate <?php endif; ?>
        title="<?php echo e($label); ?>"
        aria-label="<?php echo e($label); ?>"
        aria-current="<?php echo e($progress->isToday ? 'date' : 'false'); ?>"
        data-test="calendar-day"
        data-date="<?php echo e($progress->date->toDateString()); ?>"
        data-level="<?php echo e($progress->isFuture ? 'future' : $level); ?>"
        class="<?php echo \Illuminate\Support\Arr::toCssClasses([
            'relative flex aspect-square flex-col items-center justify-center gap-0.5 rounded-xl border p-1 transition',
            'hover:z-10 hover:scale-105 hover:shadow-sm hover:ring-2 hover:ring-gray-500 dark:hover:ring-gray-300' => $href,
            $fill,
            'opacity-40' => $outside,
            'ring-2 ring-gray-900 dark:ring-white' => $progress->isToday,
        ]); ?>"
    >
        <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
            'text-sm font-semibold leading-none tabular-nums',
            'text-white' => $level === 3 && ! $progress->isFuture,
            'text-gray-900 dark:text-white' => $level !== 3 || $progress->isFuture,
        ]); ?>"><?php echo e($progress->date->translatedFormat('j')); ?></span>

        <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
            'text-[10px] leading-none tabular-nums',
            'text-white/90' => $level === 3 && ! $progress->isFuture,
            'text-gray-600 dark:text-gray-300' => $level !== 3 || $progress->isFuture,
        ]); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($progress->isFuture): ?> &mdash; <?php else: ?> <?php echo e($progress->percentage); ?>% <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </span>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($progress->moodScore !== null): ?>
            <span class="hidden text-[10px] leading-none text-gray-600 tabular-nums sm:inline dark:text-gray-300" aria-hidden="true">&#9829;<?php echo e($progress->moodScore); ?></span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($level === 3 && ! $progress->isFuture): ?>
            <?php if (isset($component)) { $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.icon','data' => ['icon' => 'heroicon-m-check-circle','class' => 'absolute end-1 bottom-1 size-3.5 text-white/90']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'heroicon-m-check-circle','class' => 'absolute end-1 bottom-1 size-3.5 text-white/90']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $attributes = $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $component = $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </<?php echo e($tag); ?>>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\laragon\www\HabitFlow\resources\views/components/habits/day-tile.blade.php ENDPATH**/ ?>