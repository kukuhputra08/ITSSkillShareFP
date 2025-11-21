<?php
$role = $_SESSION['role'] ?? '';
?>
<aside class="bg-white shadow w-56 min-h-screen fixed left-0 top-0 pt-20 px-4">
  <ul class="space-y-3">
    <?php if($role == 'admin'): ?>
      <li><a href="/dashboard_admin.php" class="text-blue-700 hover:underline">Dashboard Admin</a></li>
      <li><a href="/manage_users.php" class="text-blue-700 hover:underline">Kelola User</a></li>
      <li><a href="/manage_jasa.php" class="text-blue-700 hover:underline">Kelola Jasa</a></li>
      <li><a href="/manage_order.php" class="text-blue-700 hover:underline">Kelola Order</a></li>
      <li><a href="/feedback.php" class="text-blue-700 hover:underline">Feedback</a></li>
    <?php elseif($role == 'provider'): ?>
      <li><a href="/dashboard_provider.php" class="text-blue-700 hover:underline">Dashboard Provider</a></li>
      <li><a href="/manage_jasa.php" class="text-blue-700 hover:underline">Kelola Jasa Saya</a></li>
      <li><a href="/manage_order.php" class="text-blue-700 hover:underline">Order Masuk</a></li>
      <li><a href="/feedback.php" class="text-blue-700 hover:underline">Feedback Jasa</a></li>
    <?php elseif($role == 'customer'): ?>
      <li><a href="/dashboard_customer.php" class="text-blue-700 hover:underline">Dashboard Customer</a></li>
      <li><a href="/manage_order.php" class="text-blue-700 hover:underline">Order Saya</a></li>
      <li><a href="/feedback.php" class="text-blue-700 hover:underline">Review/Feedback</a></li>
    <?php endif; ?>
  </ul>
</aside>