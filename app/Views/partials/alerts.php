<?php if ($message = flash('success')): ?>
<div class="alert alert-success" role="alert"><i class="bi bi-check-circle me-2"></i><?= e($message) ?></div>
<?php endif; ?>
<?php if ($message = flash('error')): ?>
<div class="alert alert-danger" role="alert"><i class="bi bi-exclamation-circle me-2"></i><?= e($message) ?></div>
<?php endif; ?>