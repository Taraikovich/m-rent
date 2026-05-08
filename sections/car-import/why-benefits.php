<?php

$title = get_field('car_import_why_title') ?: 'Почему это выгодно для клиентов из Беларуси и России';
$body  = get_field('car_import_why_body');

if (!$title && !$body) {
    return;
}

?>
<section class="bg-mrent-black px-3.75 xl:px-25 pt-7.5 xl:pt-15">
    <div class="max-w-430 mx-auto w-full flex flex-col gap-3.75 xl:gap-7.5 text-mrent-white leading-[1.2]">
        <?php if ($title): ?>
            <h2 class="font-display font-bold text-[28px] xl:text-[74px] max-w-275"><?= esc_html($title) ?></h2>
        <?php endif; ?>
        <?php if ($body): ?>
            <div class="font-normal text-base xl:text-[30px] [&_strong]:font-bold [&_p]:mb-0"><?= wp_kses_post($body) ?></div>
        <?php endif; ?>
    </div>
</section>