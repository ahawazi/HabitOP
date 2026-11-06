<?php
    $locales = \App\Support\Locale::supported();
    $current = app()->getLocale();
    $floating = $floating ?? false;
?>

<div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
    'flex items-center gap-1 rounded-lg bg-gray-100 p-1 text-xs font-medium dark:bg-white/5',
    'fixed top-4 end-4 z-50 shadow-sm' => $floating,
    'me-2' => ! $floating,
]); ?>" data-test="locale-switcher">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $locales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <a
            href="<?php echo e(route('locale.switch', $code)); ?>"
            hreflang="<?php echo e($code); ?>"
            lang="<?php echo e($code); ?>"
            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                'rounded-md px-2 py-1 transition',
                'bg-white text-gray-950 shadow-sm dark:bg-white/10 dark:text-white' => $code === $current,
                'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white' => $code !== $current,
            ]); ?>"
            <?php if($code === $current): ?> aria-current="true" <?php endif; ?>
        ><?php echo e($name); ?></a>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
</div>
<?php /**PATH C:\laragon\www\HabitFlow\resources\views/filament/locale-switcher.blade.php ENDPATH**/ ?>