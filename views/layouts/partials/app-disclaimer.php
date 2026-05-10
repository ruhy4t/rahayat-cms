<?php
$appDisclaimerClass = $appDisclaimerClass ?? 'text-xs text-slate-500';
$appDisclaimerLabelClass = $appDisclaimerLabelClass ?? 'font-semibold';
$appDisclaimerHighlightClass = $appDisclaimerHighlightClass ?? 'font-semibold';
?>
<p class="<?= e($appDisclaimerClass) ?>">
    <span class="<?= e($appDisclaimerLabelClass) ?>">Disclaimer Aplikasi:</span>
    aplikasi ini dibangun dengan cara
    <span class="<?= e($appDisclaimerHighlightClass) ?>">VIVE CODING</span>,
    full dengan bantuan AI.
</p>
<?php
unset($appDisclaimerClass, $appDisclaimerLabelClass, $appDisclaimerHighlightClass);
?>
