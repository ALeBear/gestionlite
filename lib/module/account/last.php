<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module view
 */

?>
<table class="list">
<tr>
  <td class="header">Date</td>
  <td class="header">Libellé</td>
  <td class="header">Montant</td>
  <td class="header">Solde après</td>
</tr>
<?php $balance = $account->getBalance(); ?>
<?php foreach ($list as $anItem): ?>
<tr>
  <td><?php echo date('d/m/Y', strtotime($anItem->getDatePassed())); ?></td>
  <td><?php echo $anItem->getLabel(); ?></td>
  <td class="amount <?php echo $anItem->getAccountFrom()->getId() != $account->getId() ? 'positive' : 'negative'; ?>"><?php echo $anItem->getAmount(); ?></td>
  <td class="amount <?php echo $balance >= 0 ? 'positive' : 'negative'; ?>"><?php echo number_format($balance, 2); ?></td>
</tr>
<?php
  if ($account->getId() == account::EXTERNAL_ID)
  {
    continue;
  }
  if ($anItem->getAccountFrom()->getId() == $account->getId())
  {
    $balance += $anItem->getAmount();
  }
  else
  {
    $balance -= $anItem->getAmount();
  }
?>
<?php endforeach; ?>
</table>
<br /><a href="<?php echo DIRECTORY_PREFIX; ?>account">Retour</a>