<?php
/**
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module view
 */

?>
<table class="list">
<tr>
  <td class="header">Nom</td>
  <td class="header">Solde</td>
  <td class="header">Actions</td>
</tr>
<?php $total = 0; ?>
<?php foreach ($list as $anItem): ?>
<?php $total += $anItem->getBalance(); ?>
<tr>
  <td><?php echo $anItem->getName(); ?></td>
  <td class="amount <?php echo $anItem->getBalance() >= 0 ? 'positive' : 'negative'; ?>"><?php echo $anItem->getBalance(); ?></td>
  <td>
    <?php if ($anItem->isEditable()): ?>
    <a href="<?php echo DIRECTORY_PREFIX; ?>account/update?id=<?php echo $anItem->getId(); ?>">Modifier</a>
    <a href="<?php echo DIRECTORY_PREFIX; ?>account/delete?id=<?php echo $anItem->getId(); ?>">Supprimer</a>
    <?php endif;?>
    <a href="<?php echo DIRECTORY_PREFIX; ?>account/last?id=<?php echo $anItem->getId(); ?>">Derniers mouvements</a>
  </td>
</tr>
<?php endforeach; ?>
<tr>
  <td style="text-align: right; font-weight: bold;">TOTAL</td>
  <td  style="font-weight: bold;" class="amount <?php echo $total >= 0 ? 'positive' : 'negative'; ?>"><?php echo number_format($total, 2); ?></td>
  <td>&nbsp;</td>
</tr>
</table>
<br />
<a href="<?php echo DIRECTORY_PREFIX; ?>movement/certify" onClick="return confirm('Voulez-vous vraiment certifier tous les mouvements ?');">Certifier tous les mouvement</a>
<br />
<br /><a href="<?php echo DIRECTORY_PREFIX; ?>account/update">Créer un compte</a>